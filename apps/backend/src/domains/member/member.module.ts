import { Module } from '@nestjs/common';
import { MemberService } from './member.service';
import { MemberController } from './member.controller';
import { SettleModule } from '../settle/settle.module';
import { AuthModule } from '../iam/auth.module';

@Module({
  imports: [AuthModule, SettleModule],
  controllers: [MemberController],
  providers: [MemberService],
  exports: [MemberService],
})
export class MemberModule {}
