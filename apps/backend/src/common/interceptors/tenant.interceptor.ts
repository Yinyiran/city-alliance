import {
  Injectable,
  NestInterceptor,
  ExecutionContext,
  CallHandler,
} from '@nestjs/common';
import { Observable } from 'rxjs';
import { DataScopeHelper } from '../helpers/data-scope.helper';

@Injectable()
export class TenantInterceptor implements NestInterceptor {
  intercept(context: ExecutionContext, next: CallHandler): Observable<any> {
    const request = context.switchToHttp().getRequest();
    const user = request.user;

    // 将五级多租户组织拓扑与数据范围注入请求上下文
    if (user) {
      const dataScope = DataScopeHelper.resolveDataScope(user);
      request.dataScope = dataScope;
      request.tenantContext = {
        tenantId: user.tenantId,
        tenantType: user.tenantType,
        shopId: user.shopId,
        storeId: user.storeId,
        cityId: user.cityId,
        isPlatform: dataScope.isAll,
        dataScope: dataScope.scope,
      };
    }

    return next.handle();
  }
}
