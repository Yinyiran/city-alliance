import { Controller, Post, Body, Get, UseGuards, UsePipes } from '@nestjs/common';
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
  async login(@Body() dto: AccountLoginDto): Promise<AuthTokenResultDto> {
    return this.authService.loginWithPassword(dto);
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
  @UsePipes(new ZodValidationPipe(RefreshTokenSchema))
  async refresh(@Body() dto: RefreshTokenDto): Promise<AuthTokenResultDto> {
    return this.authService.refreshToken(dto);
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
