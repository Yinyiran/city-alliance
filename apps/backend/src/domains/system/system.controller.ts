import {
  Controller,
  Get,
  Post,
  Body,
  Query,
  UseGuards,
  Req,
} from '@nestjs/common';
import { FastifyRequest } from 'fastify';
import { SystemService } from './system.service';
import { JwtAuthGuard } from '../../common/guards/jwt-auth.guard';
import { CurrentUser, AuthenticatedUser } from '../../common/decorators/current-user.decorator';

@Controller('system')
@UseGuards(JwtAuthGuard)
export class SystemController {
  constructor(private readonly systemService: SystemService) {}

  @Get('configs')
  async getConfigs(@Query('group') group?: string) {
    const data = await this.systemService.getConfigs(group);
    return {
      code: 200,
      data,
    };
  }

  @Post('configs')
  async saveConfigs(
    @Body() body: Record<string, any>,
    @CurrentUser() user: AuthenticatedUser,
    @Req() req: FastifyRequest,
  ) {
    const ipAddress = req.ip || (req.headers['x-forwarded-for'] as string) || '127.0.0.1';
    const userAgent = (req.headers['user-agent'] as string) || 'unknown';

    const result = await this.systemService.saveConfigs(body, user, ipAddress, userAgent);
    return {
      code: 200,
      message: '平台系统配置已成功保存并同步',
      data: result,
    };
  }

  @Get('audit-logs')
  async getAuditLogs(
    @Query('page') page?: number,
    @Query('pageSize') pageSize?: number,
  ) {
    const data = await this.systemService.getAuditLogs({ page, pageSize });
    return {
      code: 200,
      data,
    };
  }
}
