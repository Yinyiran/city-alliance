export default {
	computed: {
		Development() {
			return this.$store.state.Development;
		},
		//插件是否存在
		addonIsExit() {
			return this.$store.state.addonIsExit
		}
	}
}
