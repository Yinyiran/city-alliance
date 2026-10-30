import { Directive, DirectiveBinding } from 'vue';
import { useAuthStore } from '../stores/auth';

export const permissionDirective: Directive = {
  mounted(el: HTMLElement, binding: DirectiveBinding) {
    const { value } = binding;
    const authStore = useAuthStore();

    if (value) {
      const requiredPermissions: string[] = Array.isArray(value) ? value : [value];
      const userPerms: string[] = authStore.permissions || [];

      const hasPermission =
        authStore.currentRole === 'SUPER_ADMIN' ||
        userPerms.includes('*') ||
        requiredPermissions.some((perm) => {
          if (userPerms.includes(perm)) return true;
          const prefix = perm.split(':')[0] + ':*';
          return userPerms.includes(prefix);
        });

      if (!hasPermission) {
        el.parentNode && el.parentNode.removeChild(el);
      }
    }
  },
};
