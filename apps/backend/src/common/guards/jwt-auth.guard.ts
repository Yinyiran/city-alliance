import { Injectable, CanActivate, ExecutionContext, UnauthorizedException } from '@nestjs/common';
import { JwtService } from '@nestjs/jwt';
import { ConfigService } from '@nestjs/config';
import { FastifyRequest } from 'fastify';

@Injectable()
export class JwtAuthGuard implements CanActivate {
  constructor(
    private readonly jwtService: JwtService,
    private readonly configService: ConfigService,
  ) {}

  async canActivate(context: ExecutionContext): Promise<boolean> {
    const request = context.switchToHttp().getRequest<FastifyRequest>();
    const authHeader = request.headers.authorization;

    if (!authHeader || !authHeader.startsWith('Bearer ')) {
      throw new UnauthorizedException('缺少身份认证 Token，请先登录');
    }

    const token = authHeader.split(' ')[1];
    try {
      const secret = this.configService.get<string>(
        'JWT_ACCESS_SECRET',
        'city_alliance_access_token_secret_key_2026_xyz',
      );
      const payload = await this.jwtService.verifyAsync(token, { secret });
      (request as any).user = payload;
      return true;
    } catch {
      throw new UnauthorizedException('Token 无效或已过期，请重新登录');
    }
  }
}
