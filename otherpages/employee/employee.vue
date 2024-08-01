<template>
	<view>
		<uv-sticky>
			<view class="p-t-30 p-b-38 p-r-64 p-l-64 acea-row row-around row-middle wbg ">
				<view v-for="(item,index) in list" :key="index" :class="['font-30','relative',check==index?'on':'']"
					@click="check=index">
					{{item}}
					<image src="@/static/index/icon3.png" v-if="check==index" mode=""></image>
				</view>
			</view>
			<view class="thead acea-row row-between" v-if="check==0">
				<view class="font-30">账户名</view>
				<view class="font-30">角色</view>
				<view class="font-30">操作</view>
			</view>
			<view class="thead acea-row row-between" v-else>
				<view class="font-30">角色</view>
				<view class="font-30">数据权限</view>
				<view class="font-30">操作</view>
			</view>
		</uv-sticky>
		<view class="" v-if="check==0">
			<view v-for="(item,index) in 10" :key="index" class="p-t-22 p-b-22 item">
				<view class="acea-row row-between ">
					<view class="font-30">账户名</view>
					<view class="role bor-10">角色</view>
					<view class="font-30 fcolor" @click="edit">编辑</view>
				</view>
				<view class="m-t-10 font-28 m-b-30 fcolor1">13087898312</view>
				<uv-line color="#1c274c1a"></uv-line>
			</view>
		</view>
		<view class="" v-else>
			<view v-for="(item,index) in 10" :key="index" class="p-t-22 p-b-22 item">
				<view class="acea-row row-between m-b-30">
					<view class="font-30">普通员工</view>
					<view class="role bor-10">全部</view>
					<view class="font-30 " @click="edit">
						<text class="m-r-20 fcolor4">删除</text>
						<text class="fcolor" @click="edit">编辑</text>
					</view>
				</view>
				<uv-line color="#1c274c1a"></uv-line>
			</view>
		</view>
		<view class="btm p-t-20 p-r-20 p-l-20 iphone-x acea-row row-between row-middle ">
			<view class="bor-64 font-28" v-if="check==0" @click="employee(1)">添加员工</view>
			<view class="bor-64 font-28" v-else>添加角色</view>
		</view>
		<uv-popup ref="popup1" :round="10" closeable>
			<view class="popup1">
				<view class="m-b-30 bor-20 font-28 fcolor2" @click="employee(2)">修改员工信息</view>
				<view class="bor-20 font-28 fcolor3">删除账号</view>
			</view>
		</uv-popup>
		<uv-popup ref="popup2" :round="10" closeable>
			<view class="popup2">
				<view class="font-32 font-bold p-t-32 p-b-22">{{is_edit==1?'添加员工':'编辑员工'}}</view>
				<view class="m-t-30 m-r-50 m-l-50">
					<uv-input placeholder="输入员工姓名" inputAlign="right" border="surround" :customStyle='customStyle'>
						<template v-slot:prefix>
							<text class="font-28 font-bold">姓名</text>
						</template>
					</uv-input>
				</view>
				<view class="m-t-30 m-r-50 m-l-50">
					<uv-input placeholder="输入员工姓名" inputAlign="right" border="surround" :customStyle='customStyle'>
						<template v-slot:prefix>
							<text class="font-28 font-bold">角色</text>
						</template>
					</uv-input>
				</view>
			</view>
		</uv-popup>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				list: ['员工管理', '角色管理'],
				check: 0,
				is_edit: 1,
				customStyle:'padding:30rpx;border:1rpx solid #DDDFE4;border-radius:20rpx'
			};
		},
		methods: {
			edit() {
				this.$refs.popup1.open('bottom')
			},
			employee(type) {
				this.is_edit = type
				this.$refs.popup2.open('bottom')
			},
		}
	}
</script>

<style lang="scss">
	page {
		padding-bottom: calc(env(safe-area-inset-bottom) + 120rpx);
	}

	page {
		background: #fff;
	}

	.wbg {
		background: #fff;
	}

	.fcolor {
		color: #FF4D1A;
	}

	.fcolor1 {
		color: #898FA3;
	}

	.fcolor2 {
		color: #5F6680;
	}

	.fcolor3 {
		color: #FF3333;
	}

	.fcolor4 {
		color: #606882;
	}

	.relative {
		position: relative;
	}

	view {
		color: #1C274C;
	}

	.popup1 {
		padding: 80rpx 40rpx;

		view {
			width: 100%;
			text-align: center;
			line-height: 100rpx;
			background: #F8F9FA;
		}
	}

	.popup2 {
		>view:first-child {
			text-align: center;
		}
	}

	.on {
		font-weight: bold;
		color: #FF4D1A;

		image {
			position: absolute;
			left: 50%;
			transform: translateX(-50%);
			width: 27rpx;
			height: 9rpx;
			bottom: -20rpx;

		}
	}

	.thead {
		background: #F6F7FB;
		padding: 22rpx 50rpx;
		box-sizing: border-box;

	}

	.item {
		padding: 22rpx 50rpx;

		.role {
			padding: 4rpx 12rpx;
			border: 1rpx solid #BBBEC9;
		}
	}

	.btm {
		position: fixed;
		width: 100vw;
		box-sizing: border-box;
		left: 0;
		bottom: 0;
		text-align: center;
		background: #fff;
		z-index: 999;
		box-shadow: 0 1rpx 0 0 #EBEBEB inset;

		>view {
			background: #FF4D19;
			line-height: 83rpx;
			width: 100%;
			margin-bottom: 20rpx;
			color: #fff;
		}
	}
</style>