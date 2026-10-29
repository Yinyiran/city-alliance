import { Directive, DirectiveBinding } from 'vue';
import { useAuthStore } from '../stores/auth';

export const permissionDirective: Directive = {
  mounted(el: HTMLElement, binding: DirectiveBinding) {
    const { value } = binding;
    const authStore = useAuthStore();

    if (value && Array.isArray(value) && value.length > 0) {
      const requiredPermissions = value;
      const hasPermission =
        authStore.permissions.includes('*') ||
        requiredPermissions.some((perm) =>
          authStore.permissions.includes(perm),
        );

      if (!hasPermission) {
        el.parentNode && el.parentNode.removeChild(el);
      }
    }
  },
};
