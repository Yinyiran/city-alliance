import { useAppStore } from '@/store';

export default {
	computed: {
		//全局主题样式
		themeStyleScore() {
			return useAppStore().themeStyle;
		},
		themeStyle() {
			return 'theme-' + useAppStore().themeStyle
		},
		//插件是否存在
		addonIsExit() {
			return useAppStore().addonIsExit
		},
		showToastValue() {
			return useAppStore().showToastValue
		},
		diySeckillInterval() {
			return useAppStore().diySeckillInterval
		}
	}
}
