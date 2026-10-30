import {
  Controller,
  Get,
  Post,
  Put,
  Body,
  Param,
  Query,
  UseGuards,
} from '@nestjs/common';
import { VideoService, CreateVideoDto } from './video.service';
import { JwtAuthGuard } from '../../common/guards/jwt-auth.guard';
import { CurrentUser, AuthenticatedUser } from '../../common/decorators/current-user.decorator';

@Controller('videos')
export class VideoController {
  constructor(private readonly videoService: VideoService) {}

  @Get()
  async getVideos(
    @Query('page') page?: number,
    @Query('pageSize') pageSize?: number,
    @Query('keyword') keyword?: string,
    @Query('status') status?: string,
  ) {
    const data = await this.videoService.getVideos({ page, pageSize, keyword, status });
    return {
      code: 200,
      data,
    };
  }

  @Post()
  @UseGuards(JwtAuthGuard)
  async createVideo(
    @Body() body: CreateVideoDto,
    @CurrentUser() user: AuthenticatedUser,
  ) {
    const data = await this.videoService.createVideo(body, user);
    return {
      code: 200,
      message: '短视频发布成功',
      data,
    };
  }

  @Post(':id/audit')
  @UseGuards(JwtAuthGuard)
  async auditVideo(
    @Param('id') id: string,
    @Body('approved') approved: boolean,
  ) {
    const data = await this.videoService.auditVideo(id, approved);
    return {
      code: 200,
      message: approved ? '视频已审核通过并公开发布' : '视频审核已驳回',
      data,
    };
  }

  @Put(':id/status')
  @UseGuards(JwtAuthGuard)
  async updateStatus(
    @Param('id') id: string,
    @Body('status') status: string,
  ) {
    const data = await this.videoService.updateStatus(id, status);
    return {
      code: 200,
      message: status === 'OFFLINE' ? '视频已下架' : '视频已重新上架',
      data,
    };
  }

  @Post(':id/progress')
  async reportWatchProgress(
    @Param('id') id: string,
    @Body('watchedSeconds') watchedSeconds: number,
    @Body('memberId') memberId?: string,
  ) {
    const data = await this.videoService.reportWatchProgress(id, watchedSeconds || 0, memberId);
    return {
      code: 200,
      data,
    };
  }
}
