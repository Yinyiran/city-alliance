<template>
	<view>
		<view class="h-20"></view>
		<view class="p-28 items">
			<uv-input placeholder="请填写收货人姓名" v-model="address.name" border="none">
				<template v-slot:prefix>
					<view class="font-32 w-128">收货人</view>
				</template>
			</uv-input>
		</view>
		<view class="" style="height: 1rpx;"></view>
		<view class="p-28 items">
			<uv-input placeholder="请填写收货人手机号" v-model="address.mobile" tyep='number' maxlength="11" border="none">
				<template v-slot:prefix>
					<view class="font-32 w-128">手机号码</view>
				</template>
			</uv-input>
		</view>
		<view class="" style="height: 1rpx;"></view>
		<!-- <view class="p-28 items" @click="open"> -->
		<picker mode="multiSelector" :range="cloums" :value='pickerValue' range-key="name" @change="handleValueChange"
			@columnchange='columnchange'>
			<view class="p-28 items">
				<uv-input placeholder="请选择" v-model="deladdress" readonly border="none">
					<template v-slot:prefix>
						<view class="font-32 w-128">收货地区</view>
					</template>
					<template v-slot:suffix>
						<uv-icon name="arrow-right" size="18" color="#999999"></uv-icon>
					</template>
				</uv-input>
			</view>
		</picker>
		<view class="" style="height: 1rpx;"></view>
		<view class="p-28 items">
			<uv-input placeholder="请具体到小区/单元/门牌号" v-model="address.address" border="none">
				<template v-slot:prefix>
					<view class="font-32 w-128">详细地址</view>
				</template>
			</uv-input>
		</view>
		<view class="items font-32 acea-row row-middle row-between m-t-20 p-28">
			设为默认收货地址
			<uv-switch v-model="is_default" activeColor='#F55606'></uv-switch>
		</view>
		<view class="btms iphone-x" v-if="!address.id">
			<view class="" @click="memberaddressAdd">保存</view>
		</view>

		<view class="btms1 iphone-x" v-else>
			<view class="acea-row row-middle row-between">
				<view class="widths" @click="memberaddressDelete">删除</view>
				<view class="widths" @click="edits">保存</view>
			</view>
		</view>
		<uv-picker ref="picker" :columns="cloums" keyName="name" @change="change" @confirm="confirm">
		</uv-picker>
	</view>
</template>

<script>
	import {
    arealists,
    memberaddressAdd,
    memberaddressDelete,
    memberaddressEdt,
    memberaddressInfo
} from "@/common/js/api.js";
import validate from '@/common/js/validate.js';
	export default {
		data() {
			return {
				plist: [],
				clist: [],
				alist: [],
				defaultValue: [0, 0, 0],
				pickerValue: [0, 0, 0],
				address: {
					province_id: "",
					city_id: "",
					district_id: "",
					address: "",
					name: "",
					mobile: "",
					full_address: "",
					latitude: "",
					longitude: "",
					id: ''
				},
				is_default: true
			};
		},
		computed: {
			cloums() {
				return [this.plist, this.clist, this.alist]
			},
			deladdress() {
				if (!this.address.full_address) return
				let str = this.address.full_address.split('-').join('')
				return str
			},
		},
		async onLoad(res) {
			await this.getarealist()
			await this.getarealist1()
			await this.getarealist2()
			if (res.id) {
				this.address.id = res.id
				await this.memberaddressInfo()
			}
		},
		methods: {
			memberaddressDelete(){
				uni.showModal({
					content:'确定要删除当前地址？',
					success:res=>{
						if(res.confirm){
							let params={
								id:this.address.id
							}
							memberaddressDelete(params).then(res=>{
								uni.navigateBack()
							})
						}
					}
				})
			},
			edits(){
				let params = {
					...this.address,
					is_default: this.is_default ? 1 : 0
				}
				memberaddressEdt(params).then(res=>{
					uni.showToast({
						title:'修改成功'
					})
				})
			},
			handleValueChange(e) {
				let [index0, index1, index2] = e.detail.value;
				let [arr0, arr1, arr2] = this.cloums;
				let address = [arr0[index0], arr1[index1], arr2[index2]];
				console.log(address);
				this.address.full_address = '';
				this.address.full_address += address[0] != undefined ? address[0].name : '';
				this.address.province_id = address[0] != undefined ? address[0].id : '';
				this.address.city_id = address[1] != undefined ? address[1].id : '';
				this.address.district_id = address[2] != undefined ? address[2].id : '';
				this.address.full_address += address[1] != undefined ? '-' + address[1].name : '';
				this.address.full_address += address[2] != undefined ? '-' + address[2].name : '';
			},
			columnchange(e) {
				let col = e.detail.column;
				let row = e.detail.value;
				this.pickerValue[col] = row;
				switch (col) {
					case 0:
						// this.pickerValue = [row, 0, 0];
						//选择省，加载市、区县
						this.getarealist1()
						this.getarealist2()
						break;
					case 1:
						// this.pickerValue[col] = row;
						// this.pickerValue[2] = 0;
						//选择市，加载区县
						this.getarealist2()
						// this.districtArr = await this.getAreasAsync(this.cityArr[this.multiIndex[col]].value);
						break;
					case 2:
						break;
				}
			},

			async memberaddressInfo() {
				let params = {
					id: this.address.id
				}
				await memberaddressInfo(params).then(async res => {
					this.address = res.data
					let index1 = this.plist.findIndex(item => item.id == res.data.province_id)
					this.pickerValue[0]=index1
					await this.getarealist1()
					let index2 = this.clist.findIndex(item => item.id == res.data.city_id)
					this.pickerValue[1]=index2
					await this.getarealist2()
					let indexs = this.alist.findIndex(item => item.id == res.data.district_id)
					this.pickerValue=[index1,index2,indexs]
					console.log(this.pickerValue);
					this.is_default = res.data.is_default == 1 ? true : false
				})
			},
			change(e) {
				const {
					columnIndex,
					index,
					indexs
				} = e
				// 改变了省
				if (columnIndex === 0) {
					this.defaultValue = indexs
					this.$refs.picker.setIndexs([index, 0, 0], true)
					this.getarealist()
				} else if (columnIndex === 1) {
					indexs[2] = 0
					this.defaultValue = indexs
					this.$refs.picker.setIndexs(indexs, true)
					this.getarealist1()
				}
			},
			open() {
				// console.log(this.plist.findIndex(item=>item.id==this.address.province_id));
				this.$refs.picker.open()
			},
			async getarealist() {
				await arealists().then(res => {
					this.plist = res.data
				})
			},
			async getarealist1() {
				let params = {
					pid: this.plist[this.pickerValue[0]].id
				}
				await arealists(params).then(res => {
					this.clist = res.data
				})
			},
			async getarealist2() {
				let params = {
					pid: this.clist[this.pickerValue[1]].id
				}
				await arealists(params).then(res => {
					this.alist = res.data
				})
			},
			memberaddressAdd() {
				this.address.name = this.address.name.trim();
				this.address.mobile = this.address.mobile.trim();
				this.address.address = this.address.address.trim();
				var rule = [
					{
						name: 'name',
						checkType: 'required',
						errorMsg: '请输入姓名'
					},
					{
						name: 'mobile',
						checkType: 'required',
						errorMsg: '请输入手机号'
					},
					{
						name: 'mobile',
						checkType: 'phoneno',
						errorMsg: '请输入正确的手机号'
					},
					{
						name: 'full_address',
						checkType: 'required',
						errorMsg: '请选择省市区县'
					},
					{
						name: 'address',
						checkType: 'required',
						errorMsg: '详细地址不能为空'
					}
				];
				var checkRes = validate.check(this.address, rule);
				if(!checkRes){
					this.$util.showToast({
						title: validate.error
					});
					return false;
				}
				let params = {
					...this.address,
					is_default: this.is_default ? 1 : 0
				}
				memberaddressAdd(params).then(res => {
					uni.navigateBack()
				})
			}
		}
	}
</script>

<style lang="scss">
	.items {
		background: #fff;
	}

	.btms1 {
		position: fixed;
		bottom: 0;
		left: 0;
		background: #fff;
		text-align: center;
		width: 100vw;

		>view {
			margin: 20rpx 40rpx;

			.widths {
				width: calc((100% - 16rpx)/2);
				line-height: 96rpx;
				border-radius: 48rpx;
				font-weight: 600;
				font-size: 32rpx;
			}

			>view:last-child {
				background: linear-gradient(90deg, #F5AB18 0%, #F55606 100%);

				color: #FFFFFF;
			}

			>view:first-child {
				border-radius: 48rpx;
				border: 2rpx solid #DDDDDD;
				color: #555555;
			}
		}
	}

	.btms {
		position: fixed;
		bottom: 0;
		left: 0;
		background: #fff;
		text-align: center;
		width: 100vw;

		>view {
			margin: 20rpx 40rpx;
			line-height: 96rpx;
			background: linear-gradient(90deg, #F5AB18 0%, #F55606 100%);
			border-radius: 48rpx;
			font-weight: 600;
			font-size: 32rpx;
			color: #FFFFFF;
		}
	}
</style>