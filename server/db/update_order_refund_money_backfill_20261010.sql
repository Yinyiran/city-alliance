-- 历史订单退款金额回补（order.refund_money 恒为 0 的存量数据修复）
-- 幂等脚本：可重复执行；执行前请先备份数据库
-- 日期：2026-10-10
--
-- 背景：
--   app/model/order/OrderRefund.php::orderRefundFinish 此前只更新 order_goods.refund_status，
--   从未回写 order.refund_money。而订单完成时的以下两处都依赖该字段：
--     - OrderCommon::orderComplete  会员累计消费金额 order_complete_money += order_money - refund_money
--     - OrderCommon::orderComplete  会员消费回馈积分 real_pay_money  = order_money - refund_money
--   refund_money 恒为 0 会导致「已退款金额被重复计入累计消费」与「积分多返」。
--   代码已修复（退款完成时按订单项汇总回写），本脚本用于回补修复前产生的历史数据。
--
-- 口径：order.refund_money = 该订单下 refund_status = 3（维权完成）的订单项 refund_real_money 之和
--
-- 执行前建议先执行第 1 段的预览 SQL 核对影响行数与金额。

-- ============ 1. 预览：受影响的订单与应回补金额 ============
SELECT
    o.order_id,
    o.order_no,
    o.order_money,
    o.refund_money AS current_refund_money,
    IFNULL(SUM(og.refund_real_money), 0) AS should_refund_money,
    COUNT(og.order_goods_id) AS refunded_items
FROM `order` o
INNER JOIN order_goods og ON og.order_id = o.order_id
WHERE og.refund_status = 3
GROUP BY o.order_id, o.order_no, o.order_money, o.refund_money
HAVING should_refund_money > o.refund_money;

-- ============ 2. 回补（确认预览结果无误后再执行） ============
UPDATE `order` o
INNER JOIN (
    SELECT order_id, SUM(IFNULL(refund_real_money, 0)) AS refund_money
    FROM order_goods
    WHERE refund_status = 3
    GROUP BY order_id
) t ON t.order_id = o.order_id
SET o.refund_money = ROUND(t.refund_money, 2)
WHERE o.refund_money < t.refund_money;

-- ============ 3. 校验：应无 remaining 差异 ============
SELECT COUNT(*) AS still_mismatch
FROM `order` o
INNER JOIN (
    SELECT order_id, SUM(IFNULL(refund_real_money, 0)) AS refund_money
    FROM order_goods
    WHERE refund_status = 3
    GROUP BY order_id
) t ON t.order_id = o.order_id
WHERE o.refund_money < t.refund_money;
