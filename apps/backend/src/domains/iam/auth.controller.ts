import {
  Controller,
  Post,
  Body,
  Get,
  UseGuards,
  UsePipes,
  Req,
  Res,
  UnauthorizedException,
} from '@nestjs/common';
import { FastifyRequest, FastifyReply } from 'fastify';
import { AuthService } from './auth.service';
import {
  AccountLoginDto,
  AccountLoginSchema,
  WechatLoginDto,
  WechatLoginSchema,
  WechatPhoneAuthDto,
  WechatPhoneAuthSchema,
  RefreshTokenDto,
  RefreshTokenSchema,
  AuthTokenResultDto,
} from '@city-alliance/contracts';
import { ZodValidationPipe } from '../../common/pipes/zod-validation.pipe';
import { JwtAuthGuard } from '../../common/guards/jwt-auth.guard';
import { CurrentUser, AuthenticatedUser } from '../../common/decorators/current-user.decorator';

@Controller('auth')
export class AuthController {
  constructor(private readonly authService: AuthService) {}

  @Post('login')
  @UsePipes(new ZodValidationPipe(AccountLoginSchema))
  async login(
    @Body() dto: AccountLoginDto,
    @Res({ passthrough: true }) res: FastifyReply,
  ): Promise<AuthTokenResultDto> {
    const result = await this.authService.loginWithPassword(dto);

    // 为 Web 终端设置工业级安全的 HttpOnly Cookie 存储长效 RefreshToken (阻断 XSS 脚本窃取)
    res.setCookie('refresh_token', result.refreshToken, {
      httpOnly: true,
      secure: false, // 本地开发环境无需强制 https，生产由网关反向代理启用
      sameSite: 'lax',
      path: '/api/v1/auth',
      maxAge: 7 * 86400, // 7 天
    });

    return result;
  }

  @Post('wechat-login')
  @UsePipes(new ZodValidationPipe(WechatLoginSchema))
  async wechatLogin(@Body() dto: WechatLoginDto): Promise<AuthTokenResultDto> {
    return this.authService.loginWithWechat(dto);
  }

  @Post('wechat-phone-bind')
  @UseGuards(JwtAuthGuard)
  @UsePipes(new ZodValidationPipe(WechatPhoneAuthSchema))
  async bindWechatPhone(
    @CurrentUser() user: AuthenticatedUser,
    @Body() dto: WechatPhoneAuthDto,
  ) {
    return this.authService.bindWechatPhone(user, dto);
  }

  @Post('refresh')
  async refresh(
    @Req() req: FastifyRequest,
    @Res({ passthrough: true }) res: FastifyReply,
    @Body() bodyDto?: any,
  ): Promise<AuthTokenResultDto> {
    // 优先从安全 HttpOnly Cookie 读取，回退从请求 Body 读取 (无缝兼容多端/移动端/小程序)
    const cookieToken = (req.cookies as Record<string, string> | undefined)?.['refresh_token'];
    const refreshToken = cookieToken || bodyDto?.refreshToken;

    if (!refreshToken) {
      throw new UnauthorizedException('缺少 Refresh Token 凭证，请重新登录');
    }

    const result = await this.authService.refreshToken({ refreshToken });

    // 轮换写入全新的 HttpOnly Cookie (JTI Token Rotation)
    res.setCookie('refresh_token', result.refreshToken, {
      httpOnly: true,
      secure: false,
      sameSite: 'lax',
      path: '/api/v1/auth',
      maxAge: 7 * 86400,
    });

    return result;
  }

  @Post('logout')
  async logout(
    @Req() req: FastifyRequest,
    @Res({ passthrough: true }) res: FastifyReply,
  ) {
    const cookieToken = (req.cookies as Record<string, string> | undefined)?.['refresh_token'];
    if (cookieToken) {
      await this.authService.revokeRefreshToken(cookieToken);
    }
    res.clearCookie('refresh_token', { path: '/api/v1/auth' });
    return {
      code: 200,
      message: '安全登出成功',
    };
  }

  @Get('me')
  @UseGuards(JwtAuthGuard)
  async getProfile(@CurrentUser() user: AuthenticatedUser) {
    return {
      code: 200,
      data: user,
    };
  }

  @Get('menu-tree')
  @UseGuards(JwtAuthGuard)
  async getMenuTree(@CurrentUser() user: AuthenticatedUser) {
    const menus = await this.authService.getMenuTree(user);
    return {
      code: 200,
      data: menus,
    };
  }
}
