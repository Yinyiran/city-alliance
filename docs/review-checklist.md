# 微信提审 Checklist（商家端 + 用户端）

## 一、必做：创建测试账号

只改数据库，**不改表结构**。详见 `docs/merchant-applet-review-fix.sql`。

1. 查一个会员（商家端要求：是某店铺老板/员工 + 有手机号）
2. `php -r "echo password_hash('Abc@123456', PASSWORD_DEFAULT);"` 生成哈希
3. `UPDATE member SET password = '哈希' WHERE member_id = xxx;`

审核员在登录弹窗切到「账号密码登录」，用手机号 + 密码进入。

**上线后收回测试账号**（无需改代码，三选一）：

```sql
UPDATE member SET password = '' WHERE member_id = xxx;   -- 清空密码（推荐）
UPDATE member SET status = 0  WHERE member_id = xxx;   -- 停用
UPDATE member SET is_delete = 1 WHERE member_id = xxx; -- 删除
```

---

## 二、提审配置值

| 配置 | 建议 | 原因 |
|---|---|---|
| 提现 `min` | **0** | 微信规范 3.3 禁"不合理门槛"；填 0 时前端不展示该条 |
| 提现 `max` | 建议填（如 50000） | 0 为不限，审核实测大额被拒易给差评 |
| 手续费 `rate` | 如实 | 属必披露项 |

操作：后台「会员 → 提现设置」重新保存一次（`setConfig` 是整体覆盖 value）。

---

## 三、隐私合规（两端不同，别搞混）

| 小程序 | 定位 | 处理 |
|---|---|---|
| 商家端 | 零处调用 | 已清空 `permission` / `requiredPrivateInfos` / `requiredBackgroundModes` |
| 用户端 | **真实使用**（同城页 `getLocation`、切换城市 `chooseLocation`） | **保留** `requiredPrivateInfos`，仅删多余的 `requiredBackgroundModes` |

同步更新微信公众平台《隐私保护指引》。

---

## 四、部署后实测

后端 PHP 未经语法检查（本地无 PHP 环境），这是最大风险点。

### 通用
- [ ] 断网打开两端任意页面 → 不白屏，提示"网络异常"
- [ ] 账号密码登录能成功进入
- [ ] 密码错误 / 账号不存在 → 提示明确，不卡死
- [ ] 生产包无 vConsole / Eruda / debug 悬浮按钮

### 商家端
- [ ] 提现页规则区**首屏可见**，含 7 条（额度/次数/时间/到账/手续费/个税/条件）
- [ ] 结算卡提示**只出现一条**（原先会渲染两遍）
- [ ] 可提现金额显示 `0.00` 而非空白
- [ ] 输入金额 → 实时显示手续费 / 个税 / 预计到账
- [ ] `min=0.01`：输 0.01 可提交、输 0 被拒
- [ ] `min=0`：规则区**无**「最低提现金额」
- [ ] 未登录「我的」显示引导卡，不是空白
- [ ] 接口失败显示"重新加载"

### 用户端
- [ ] `min=0` 时不显示「最低提现金额」
- [ ] 4 个 tabBar 页（首页/同城/逛一逛/我的）断网均不白屏
- [ ] 「我的」token 失效后不展示过期数据
- [ ] 登录弹窗可切手机号 / 账号密码两个 Tab

---

## 五、审核备注建议

1. 本小程序为经营类小程序，支持**手机号快捷登录**与**账号密码登录**（员工可用手机号或员工编号登录）。
2. **提现规则**位于「我的 → 提现」页面首屏，含可提现额度、每日提现次数、提现时间、到账时间、手续费率、个税代扣说明，并提供输入金额实时试算。
3. 平台**不设最低提现门槛**，可按需分多次提现。
4. 用户协议、隐私政策可在登录弹窗内查看。

---

## 六、已修复的历史问题（供参考）

- 商家端 `shop/goods/addGoods.html` 报 `htmlentities() expects parameter 1 to be string, array given`
  → `app/shop/controller/Goods.php` 两处 `store_list` 改为 `json_encode()`，add/edit 都已修