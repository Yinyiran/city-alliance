import {
  Injectable,
  NestInterceptor,
  ExecutionContext,
  CallHandler,
} from '@nestjs/common';
import { Observable } from 'rxjs';
import { TenantType } from '@city-alliance/contracts';

@Injectable()
export class TenantInterceptor implements NestInterceptor {
  intercept(context: ExecutionContext, next: CallHandler): Observable<any> {
    const request = context.switchToHttp().getRequest();
    const user = request.user;

    // 将多租户边界数据注入请求上下文
    if (user && user.tenantType) {
      request.tenantContext = {
        tenantId: user.tenantId,
        tenantType: user.tenantType,
        isPlatform: user.tenantType === TenantType.PLATFORM,
        isCity: user.tenantType === TenantType.CITY,
        isShop: user.tenantType === TenantType.SHOP,
        isStore: user.tenantType === TenantType.STORE,
        isSupply: user.tenantType === TenantType.SUPPLY,
      };
    }

    return next.handle();
  }
}
