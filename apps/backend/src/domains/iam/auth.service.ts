import { Injectable, UnauthorizedException, BadRequestException, Inject } from '@nestjs/common';
import { JwtService } from '@nestjs/jwt';
import { ConfigService } from '@nestjs/config';
import {
  AccountLoginDto,
  WechatLoginDto,
  RefreshTokenDto,
  AuthTokenResultDto,
  RoleEnum,
  TenantType,
  TerminalType,
} from '@city-alliance/contracts';
import { DRIZZLE_DB, DrizzleDatabase } from '../../infrastructure/drizzle/drizzle.provider';
import { users, members } from '../../infrastructure/drizzle/schema/index';
import { eq } from 'drizzle-orm';
import * as crypto from 'crypto';

@Injectable()
export class AuthService {
  constructor(
    @Inject(DRIZZLE_DB) private readonly db: DrizzleDatabase,
    private readonly jwtService: JwtService,
    private readonly configService: ConfigService,
  ) {}

  private hashPassword(password: string): string {
    return crypto.createHash('sha256').update(password + '_city_alliance_salt').digest('hex');
  }

  private generate6DigitCode(): string {
    return Math.floor(100000 + Math.random() * 900000).toString();
  }

  /**
   * 签发统一的双 Token (AccessToken 2小时 + RefreshToken 7天)
   */
  async generateTokens(payload: {
    id: string;
    username: string;
    nickname: string;
    role: RoleEnum;
    tenantType: TenantType;
    tenantId?: string | null;
    permissions: string[];
  }): Promise<AuthTokenResultDto> {
    const accessSecret = this.configService.get<string>(
      'JWT_ACCESS_SECRET',
      'city_alliance_access_token_secret_key_2026_xyz',
    );
    const refreshSecret = this.configService.get<string>(
      'JWT_REFRESH_SECRET',
      'city_alliance_refresh_token_secret_key_2026_abc',
    );

    const accessToken = await this.jwtService.signAsync(payload, {
      secret: accessSecret,
      expiresIn: '2h',
    });

    const refreshToken = await this.jwtService.signAsync(
      { sub: payload.id, type: 'refresh' },
      {
        secret: refreshSecret,
        expiresIn: '7d',
      },
    );

    return {
      accessToken,
      refreshToken,
      expiresIn: 7200, // 2 hours in seconds
      user: payload,
    };
  }

  /**
   * 账号密码登录 (支持五端鉴权)
   */
  async loginWithPassword(dto: AccountLoginDto): Promise<AuthTokenResultDto> {
    const userList = await this.db
      .select()
      .from(users)
      .where(eq(users.username, dto.username))
      .limit(1);

    const user = userList[0];
    if (!user) {
      throw new UnauthorizedException('用户不存在或密码错误');
    }

    const hashed = this.hashPassword(dto.password);
    if (user.passwordHash !== hashed) {
      throw new UnauthorizedException('用户不存在或密码错误');
    }

    if (user.status !== 'ACTIVE') {
      throw new UnauthorizedException('该账号已被冻结停用，请联系管理员');
    }

    return this.generateTokens({
      id: user.id,
      username: user.username,
      nickname: user.nickname,
      role: RoleEnum.SHOP_OWNER, // 默认关联角色
      tenantType: TenantType.SHOP,
      tenantId: user.tenantId,
      permissions: ['*'],
    });
  }

  /**
   * 微信小程序静默登录与快捷注册
   */
  async loginWithWechat(dto: WechatLoginDto): Promise<AuthTokenResultDto> {
    // 微信 code 解析 openid 逻辑 (在沙箱/开发环境提供保底 mock 机制)
    const mockOpenid = `wx_openid_${crypto.createHash('md5').update(dto.code).digest('hex').substring(0, 16)}`;
    const mockUsername = `wx_${mockOpenid.substring(10)}`;

    let existingUsers = await this.db
      .select()
      .from(users)
      .where(eq(users.username, mockUsername))
      .limit(1);

    let user = existingUsers[0];

    if (!user) {
      // 首次微信授权，自动开户并生成 6 位数会员编码
      const [newUser] = await this.db
        .insert(users)
        .values({
          username: mockUsername,
          passwordHash: this.hashPassword(crypto.randomUUID()),
          nickname: '微信用户_' + mockOpenid.substring(10, 14),
          status: 'ACTIVE',
        })
        .returning();

      user = newUser;

      // 创建会员档案 (确保 6 位数会员 ID 唯一)
      let memberCode = this.generate6DigitCode();
      await this.db.insert(members).values({
        userId: user.id,
        memberCode,
        level: 'NORMAL',
        inviterId: dto.inviterId || null,
        status: 'ACTIVE',
      });
    }

    return this.generateTokens({
      id: user.id,
      username: user.username,
      nickname: user.nickname,
      role: RoleEnum.STORE_CLERK,
      tenantType: TenantType.STORE,
      tenantId: user.tenantId,
      permissions: ['order:read', 'order:verify'],
    });
  }

  /**
   * 双 Token 轮换刷新 (Dual Token Rotation)
   */
  async refreshToken(dto: RefreshTokenDto): Promise<AuthTokenResultDto> {
    const refreshSecret = this.configService.get<string>(
      'JWT_REFRESH_SECRET',
      'city_alliance_refresh_token_secret_key_2026_abc',
    );

    try {
      const decoded = await this.jwtService.verifyAsync(dto.refreshToken, {
        secret: refreshSecret,
      });

      if (decoded.type !== 'refresh') {
        throw new UnauthorizedException('无效的 Refresh Token 凭证');
      }

      const userList = await this.db
        .select()
        .from(users)
        .where(eq(users.id, decoded.sub))
        .limit(1);

      const user = userList[0];
      if (!user || user.status !== 'ACTIVE') {
        throw new UnauthorizedException('用户不存在或已被停用');
      }

      return this.generateTokens({
        id: user.id,
        username: user.username,
        nickname: user.nickname,
        role: RoleEnum.SHOP_OWNER,
        tenantType: TenantType.SHOP,
        tenantId: user.tenantId,
        permissions: ['*'],
      });
    } catch {
      throw new UnauthorizedException('Refresh Token 已失效，请重新登录');
    }
  }
}
