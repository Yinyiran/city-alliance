import { Controller, Post, Body, UseGuards, UsePipes } from '@nestjs/common';
import { SigninService } from './signin.service';
import {
  StoreSignInRequestDto,
  StoreSignInRequestSchema,
  VideoProgressReportDto,
  VideoProgressReportSchema,
} from '@city-alliance/contracts';
import { ZodValidationPipe } from '../../common/pipes/zod-validation.pipe';
import { JwtAuthGuard } from '../../common/guards/jwt-auth.guard';
import { CurrentUser, AuthenticatedUser } from '../../common/decorators/current-user.decorator';

@Controller('signin')
@UseGuards(JwtAuthGuard)
export class SigninController {
  constructor(private readonly signinService: SigninService) {}

  @Post('store')
  @UsePipes(new ZodValidationPipe(StoreSignInRequestSchema))
  async signInStore(
    @Body() dto: StoreSignInRequestDto,
    @CurrentUser() user: AuthenticatedUser,
  ) {
    const result = await this.signinService.handleStoreSignIn(user.id, dto);
    return {
      code: 200,
      data: result,
    };
  }

  @Post('video-progress')
  @UsePipes(new ZodValidationPipe(VideoProgressReportSchema))
  async reportVideo(
    @Body() dto: VideoProgressReportDto,
    @CurrentUser() user: AuthenticatedUser,
  ) {
    const result = await this.signinService.reportVideoProgress(user.id, dto);
    return {
      code: 200,
      data: result,
    };
  }
}
