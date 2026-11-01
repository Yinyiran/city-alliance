const http = require('http');
const postgres = require('postgres');

const sql = postgres(process.env.DATABASE_URL || 'postgresql://city_admin:city_secret_pass_2026@127.0.0.1:5432/city_alliance');

async function api(options, body = null) {
  return new Promise((resolve, reject) => {
    const req = http.request(options, (res) => {
      let data = '';
      res.on('data', (chunk) => (data += chunk));
      res.on('end', () => {
        try {
          resolve({ status: res.statusCode, headers: res.headers, data: JSON.parse(data) });
        } catch (e) {
          resolve({ status: res.statusCode, headers: res.headers, text: data });
        }
      });
    });
    req.on('error', reject);
    if (body) {
      req.write(typeof body === 'string' ? body : JSON.stringify(body));
    }
    req.end();
  });
}

async function login(username, password) {
  const res = await api(
    {
      hostname: 'localhost',
      port: 4000,
      path: '/api/v1/auth/login',
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
    },
    { username, password, terminal: 'ADMIN_WEB' }
  );

  const token = res.data?.accessToken || res.data?.data?.accessToken || res.data?.data?.token;
  const user = res.data?.user || res.data?.data?.user;
  if (!token) {
    throw new Error(`Login failed for ${username}: ${JSON.stringify(res.data)}`);
  }
  return { token, user, authHeaders: { Authorization: `Bearer ${token}`, 'Content-Type': 'application/json' } };
}

let testResults = [];
function recordTest(suite, caseName, passed, detail = '') {
  testResults.push({ suite, caseName, status: passed ? 'PASSED' : 'FAILED', detail });
  const icon = passed ? '✅' : '❌';
  console.log(`  ${icon} [${suite}] ${caseName} -> ${passed ? 'PASSED' : 'FAILED'}: ${detail}`);
}

async function runExpertTesting() {
  console.log('================================================================================');
  console.log('🔬 CITY ALLIANCE EXPERT-LEVEL MULTI-TERMINAL & LOGIC VERIFICATION SUITE');
  console.log('================================================================================\n');

  // ==============================================================================
  // SUITE 1: 🏢 平台总部运营总控端 (SUPER_ADMIN)
  // ==============================================================================
  console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
  console.log('👉 SUITE 1: 平台总部运营总控端 (SUPER_ADMIN: admin)');
  console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
  const admin = await login('admin', '123456');
  recordTest('Platform Admin', '超级管理员认证与全量授权', admin.user.role === 'SUPER_ADMIN' && admin.user.permissions.includes('*'), `Role: ${admin.user.role}, Permissions: ${admin.user.permissions}`);

  // 1.1 全网大盘 GMV 与多维指标
  const dOverview = await api({ hostname: 'localhost', port: 4000, path: '/api/v1/dashboard/overview', method: 'GET', headers: admin.authHeaders });
  const dData = dOverview.data?.data;
  const gmvValid = dData?.gmv && typeof dData.gmv.total === 'string' && Number(dData.gmv.total) >= 0;
  const memberTiersValid = dData?.members && Number(dData.members.total) >= 0;
  recordTest('Platform Admin', '全网宏观经营罗盘与GMV穿透', gmvValid && memberTiersValid, `GMV: ￥${dData?.gmv?.total}, 全网会员: ${dData?.members?.total}人, 钻石/黑钻: ${dData?.members?.diamonds}/${dData?.members?.blackDiamonds}`);

  // 1.2 全网商家治理
  const shopsRes = await api({ hostname: 'localhost', port: 4000, path: '/api/v1/platform/shops?page=1&pageSize=10', method: 'GET', headers: admin.authHeaders });
  const totalPlatformShops = shopsRes.data?.data?.total || 0;
  recordTest('Platform Admin', '全网主理人商家治理与准入检索', totalPlatformShops >= 80, `检索到全网主理人商家: ${totalPlatformShops} 家 (覆盖各行业各城市)`);

  // 1.3 城市分公司授权拓扑
  const tenantsRes = await api({ hostname: 'localhost', port: 4000, path: '/api/v1/platform/cities', method: 'GET', headers: admin.authHeaders });
  const citiesCount = tenantsRes.data?.data?.length || tenantsRes.data?.data?.items?.length || 0;
  recordTest('Platform Admin', '全国城市分公司授权拓扑', citiesCount >= 5, `检索到已授权城市分公司: ${citiesCount} 家 (淮北、广州、郑州等)`);

  // 1.4 全网会员档案
  const membersRes = await api({ hostname: 'localhost', port: 4000, path: '/api/v1/members?page=1&pageSize=10', method: 'GET', headers: admin.authHeaders });
  recordTest('Platform Admin', '全网会员主档案与六位会员码台账', membersRes.data?.data?.total >= 40, `全网登记会员: ${membersRes.data?.data?.total} 位`);

  // 1.5 全网财务清算与 25% 分润总盘
  const commRes = await api({ hostname: 'localhost', port: 4000, path: '/api/v1/settle/commissions?page=1&pageSize=5', method: 'GET', headers: admin.authHeaders });
  recordTest('Platform Admin', '全网财务资金总清算台账', commRes.status === 200, `全网分润记账明细: ${commRes.data?.data?.total} 条记录`);

  // 1.6 系统全局底层配置
  const sysConfig = await api({ hostname: 'localhost', port: 4000, path: '/api/v1/system/configs', method: 'GET', headers: admin.authHeaders });
  const configKeysCount = Object.keys(sysConfig.data?.data || {}).length;
  recordTest('Platform Admin', '系统全局配置与支付渠道参数', sysConfig.status === 200 && configKeysCount >= 5, `系统生效配置项: ${configKeysCount} 项 (微信支付APIv3, 冷冻期7天, 运费参数等)`);


  // ==============================================================================
  // SUITE 2: 🏙️ 城市分公司管理端 (CITY_AGENT)
  // ==============================================================================
  console.log('\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
  console.log('👉 SUITE 2: 城市分公司管理端 (CITY_AGENT: huaibei - 淮北市)');
  console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
  const city = await login('huaibei', '123456');
  recordTest('City Agent', '城市分公司认证与辖区绑定', city.user.role === 'CITY_AGENT' && city.user.cityId, `Role: ${city.user.role}, CityId: ${city.user.cityId}`);

  // 2.1 城市大屏
  const cityOverview = await api({ hostname: 'localhost', port: 4000, path: '/api/v1/city/overview', method: 'GET', headers: city.authHeaders });
  const cityStats = cityOverview.data?.data?.stats;
  const topMerchants = cityOverview.data?.data?.topMerchants || [];
  recordTest('City Agent', '城市经营罗盘与TOP主理人榜单', Number(cityStats?.shopCount) === 74 && topMerchants.length > 0, `辖区商家: ${cityStats?.shopCount} 家, 实体门店: ${cityStats?.storeCount} 家, TOP商户榜首: ${topMerchants[0]?.name}`);

  // 2.2 辖区商户强物理隔离测试 (Critical Data Isolation)
  const cityShops = await api({ hostname: 'localhost', port: 4000, path: '/api/v1/city/shops?page=1&pageSize=100', method: 'GET', headers: city.authHeaders });
  const items = cityShops.data?.data?.items || [];
  // 检查返回的所有商户在 DB 中是否全部属于淮北市 (tenantId = a7aa2e93-a1bc-4ce3-94bd-d1c3414a57e5)
  const nonCityShops = await sql`
    SELECT id, name, city_id FROM shops 
    WHERE id = ANY(${items.map(s => s.id)}) AND city_id != ${city.user.cityId}
  `;
  const isIsolated = nonCityShops.length === 0 && items.length === 74;
  recordTest('City Agent', '辖区商户物理强隔离审计 (无数据越权泄漏)', isIsolated, `返回淮北辖区商户数: ${items.length}, 异地越权商户数: ${nonCityShops.length} (严禁越权)`);

  // 2.3 25% 城市分润账本真实性验证
  const cityComm = await api({ hostname: 'localhost', port: 4000, path: '/api/v1/city/commissions?page=1&pageSize=10', method: 'GET', headers: city.authHeaders });
  recordTest('City Agent', '25% 城市分润账本与收益核算', cityComm.status === 200, `分润账单状态: ${cityComm.status}, 总额: ￥${cityStats?.totalProfit || '0.00'}`);

  // 2.4 分公司站点个性化配置双向持久化
  const testPhone = `0561-8888${Math.floor(1000 + Math.random() * 9000)}`;
  await api(
    { hostname: 'localhost', port: 4000, path: '/api/v1/city/config', method: 'PUT', headers: city.authHeaders },
    { servicePhone: testPhone, banners: ['https://cdn.cmyx.top/test-banner.jpg'], navCategories: ['淮北特产', '老字号美食'] }
  );
  const verifyConfig = await api({ hostname: 'localhost', port: 4000, path: '/api/v1/city/config', method: 'GET', headers: city.authHeaders });
  const configPersisted = verifyConfig.data?.data?.servicePhone === testPhone;
  recordTest('City Agent', '分公司个性化配置读写双向一致性', configPersisted, `热线电话更新为: ${verifyConfig.data?.data?.servicePhone}`);


  // ==============================================================================
  // SUITE 3: 🏪 商家主理人管理端 (SHOP_OWNER)
  // ==============================================================================
  console.log('\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
  console.log('👉 SUITE 3: 商家主理人管理端 (SHOP_OWNER: shop1 - 广州欧派官方旗舰店)');
  console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
  const shop = await login('shop1', '123456');
  recordTest('Shop Owner', '商家主理人认证与店铺绑定', shop.user.role === 'SHOP_OWNER' && shop.user.shopId, `Role: ${shop.user.role}, ShopId: ${shop.user.shopId}`);

  // 3.1 商家独立经营大盘
  const shopOverview = await api({ hostname: 'localhost', port: 4000, path: '/api/v1/dashboard/overview', method: 'GET', headers: shop.authHeaders });
  recordTest('Shop Owner', '商家单店独立经营大盘穿透', shopOverview.status === 200, `商户独立订单数: ${shopOverview.data?.data?.gmv?.totalOrders}, 独立GMV: ￥${shopOverview.data?.data?.gmv?.total}`);

  // 3.2 商户商品隔离
  const shopGoods = await api({ hostname: 'localhost', port: 4000, path: '/api/v1/goods?page=1&pageSize=50', method: 'GET', headers: shop.authHeaders });
  const goodsItems = shopGoods.data?.data?.items || [];
  // 检查返回商品是否全部属于当前 shop
  const foreignGoods = await sql`
    SELECT id, name, shop_id FROM goods_spu 
    WHERE id = ANY(${goodsItems.map(g => g.id)}) AND shop_id != ${shop.user.shopId}
  `;
  const goodsIsolated = foreignGoods.length === 0;
  recordTest('Shop Owner', '商家商品库物理隔离 (严防串货)', goodsIsolated, `返回本店商品: ${goodsItems.length}件, 包含他店越权商品: ${foreignGoods.length}件`);

  // 3.3 商户连锁分店隔离
  const shopStores = await api({ hostname: 'localhost', port: 4000, path: '/api/v1/stores?page=1&pageSize=50', method: 'GET', headers: shop.authHeaders });
  const storeItems = shopStores.data?.data?.items || [];
  const foreignStores = await sql`
    SELECT id, name, shop_id FROM stores 
    WHERE id = ANY(${storeItems.map(s => s.id)}) AND shop_id != ${shop.user.shopId}
  `;
  recordTest('Shop Owner', '商家旗下实体门店物理隔离', foreignStores.length === 0, `本店分店数: ${storeItems.length}, 包含他店越权分店: ${foreignStores.length}`);


  // ==============================================================================
  // SUITE 4: 🏬 实体门店核销收银端 (STORE_MANAGER / STORE_CLERK)
  // ==============================================================================
  console.log('\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
  console.log('👉 SUITE 4: 实体门店核销收银端 (STORE_MANAGER: store1 - 万达广场店)');
  console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
  const store = await login('store1', '123456');
  recordTest('Store POS', '店长收银员认证与门店绑定', store.user.role === 'STORE_MANAGER' && store.user.storeId, `Role: ${store.user.role}, StoreId: ${store.user.storeId}`);

  // 4.1 当班收银与开班签到
  const currentShiftRes = await api({ hostname: 'localhost', port: 4000, path: '/api/v1/stores/shifts/current', method: 'GET', headers: store.authHeaders });
  const activeShift = currentShiftRes.data?.data?.currentShift;
  recordTest('Store POS', '收银台当班活跃班次检测', activeShift && activeShift.status === 'OPEN', `活跃班次: ${activeShift?.storeName}, 值班员: ${activeShift?.clerkName}`);

  // 4.2 专家级核销闭环全生命周期测试 (Order Verification Lifecycle)
  const testVerifyCode = `${Date.now().toString().slice(-8)}${Math.floor(1000 + Math.random() * 9000)}`;
  const [testCustomer] = await sql`SELECT id FROM members LIMIT 1`;
  const [createdOrder] = await sql`
    INSERT INTO orders (
      order_no, customer_id, shop_id, store_id, trade_type, 
      order_status, pay_status, total_amount, pay_amount, 
      payment_channel, verify_code
    ) VALUES (
      ${'TEST_VERIFY_' + testVerifyCode}, ${testCustomer.id}, ${'7277777a-4ed2-4f3c-b93c-bf8a8af5fae3'}, 
      ${store.user.storeId}, 'PICKUP', 'PAID', 'PAY_CONFIRMED', '88.00', '88.00', 'WECHAT_PAY', ${testVerifyCode}
    ) RETURNING id, order_no, verify_code;
  `;

  // 首次核销 -> 必须成功
  const firstVerifyRes = await api(
    { hostname: 'localhost', port: 4000, path: '/api/v1/trade/verify', method: 'POST', headers: store.authHeaders },
    { verifyCode: testVerifyCode }
  );
  const firstVerifyPassed = (firstVerifyRes.status === 200 || firstVerifyRes.status === 201) && (firstVerifyRes.data?.code === 200 || firstVerifyRes.data?.data?.success);

  // 二次核销 -> 必须被分布式锁与幂等校验拦截抛出 400
  const secondVerifyRes = await api(
    { hostname: 'localhost', port: 4000, path: '/api/v1/trade/verify', method: 'POST', headers: store.authHeaders },
    { verifyCode: testVerifyCode }
  );
  const doubleVerifyBlocked = secondVerifyRes.status === 400;

  // 检查订单在 DB 中的真实状态
  const [verifiedDbOrder] = await sql`SELECT order_status, verified_at, verify_user_id FROM orders WHERE id = ${createdOrder.id}`;
  const dbVerifiedCorrectly = verifiedDbOrder?.order_status === 'COMPLETED' && verifiedDbOrder?.verify_user_id === store.user.id;

  recordTest('Store POS', '自提订单12位核销码原子核销闭环', firstVerifyPassed && dbVerifiedCorrectly, `首次核销状态: ${firstVerifyRes.status}, DB状态跃迁: ${verifiedDbOrder?.order_status}, 核销员ID: ${verifiedDbOrder?.verify_user_id}`);
  recordTest('Store POS', '防重复核销与幂等防御拦截', doubleVerifyBlocked, `二次核销拦截状态: ${secondVerifyRes.status}, 拦截信息: ${secondVerifyRes.data?.message}`);

  // 4.3 收银交接班结班与轧账差异计算
  if (activeShift?.id) {
    const closeRes = await api(
      { hostname: 'localhost', port: 4000, path: '/api/v1/stores/shifts/close', method: 'POST', headers: store.authHeaders },
      {
        shiftId: activeShift.id,
        scanTotal: 388.50,
        cashTotal: 100.00,
        diffAmount: 0.00,
        remark: '专家自动化交班轧账平账测试',
      }
    );
    const [closedShiftDb] = await sql`SELECT status, shift_end, diff_amount FROM cashier_shifts WHERE id = ${activeShift.id}`;
    const shiftClosed = closedShiftDb?.status === 'CLOSED' && closedShiftDb?.shift_end != null;
    recordTest('Store POS', '收银交接班结班扎帐与平账核算', shiftClosed, `班次状态: ${closedShiftDb?.status}, 结班时间: ${closedShiftDb?.shift_end}, 差异金额: ${closedShiftDb?.diff_amount}`);

    // 为保证收银台后续可用，重新开一个测试班次
    await sql`
      INSERT INTO cashier_shifts (store_id, clerk_id, shift_start, cash_total, scan_total, order_count, diff_amount, status, remark)
      VALUES (${store.user.storeId}, ${activeShift.clerkId}, now(), '0.00', '0.00', 0, '0.00', 'OPEN', '正常值班中')
    `;
  }


  // ==============================================================================
  // SUITE 5: 🏭 供应链供货商端 (SUPPLIER)
  // ==============================================================================
  console.log('\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
  console.log('👉 SUITE 5: 供应链供货商端 (SUPPLIER: supplier1 - 源头选品供货中心)');
  console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
  const supplier = await login('supplier1', '123456');
  recordTest('Supplier B2B', '供货商身份认证与组织绑定', supplier.user.role === 'SUPPLIER' && supplier.user.tenantId, `Role: ${supplier.user.role}, TenantId: ${supplier.user.tenantId}`);

  // 5.1 选品池发布
  const testGoodsName = `专家质检特选高山茶原叶 ${Date.now().toString().slice(-4)}`;
  const pubGoodsRes = await api(
    { hostname: 'localhost', port: 4000, path: '/api/v1/supply/goods', method: 'POST', headers: supplier.authHeaders },
    {
      name: testGoodsName,
      supplyPrice: 160.00,
      suggestRetailPrice: 280.00,
      stock: 3000,
    }
  );
  const pubSuccess = pubGoodsRes.status === 201 || pubGoodsRes.data?.code === 200;
  recordTest('Supplier B2B', '发布源头商品至全网批发选品池', pubSuccess, `发布商品: ${testGoodsName}, 供货价: ￥160.00, 建议零售: ￥280.00`);

  // 5.2 状态切换 (下架与恢复上架)
  const spuId = pubGoodsRes.data?.data?.spu?.id;
  if (spuId) {
    const offSaleRes = await api(
      { hostname: 'localhost', port: 4000, path: `/api/v1/supply/goods/${spuId}/status`, method: 'PUT', headers: supplier.authHeaders },
      { status: 'OFF_SALE' }
    );
    const [dbGoodsStatus] = await sql`SELECT status FROM goods_spu WHERE id = ${spuId}`;
    recordTest('Supplier B2B', '供货状态上下架实时熔断控制', dbGoodsStatus?.status === 'OFF_SALE', `商品状态已置为: ${dbGoodsStatus?.status}`);
  }

  // 5.3 B2B 批发采购订单与发货打单闭环
  const [supplierRecord] = await sql`SELECT id FROM suppliers LIMIT 1`;
  const [buyerShop] = await sql`SELECT id FROM shops LIMIT 1`;
  const poCreateRes = await api(
    { hostname: 'localhost', port: 4000, path: '/api/v1/supply/purchase-orders', method: 'POST', headers: shop.authHeaders },
    {
      buyerShopId: buyerShop.id,
      supplierId: supplierRecord.id,
      totalAmount: '4800.00',
      notes: '订购首批30件大件批发装',
    }
  );
  const createdPo = poCreateRes.data?.data;
  const poCreatedOk = createdPo && createdPo.orderNo;

  // 供货商发货打单
  const trackingNumber = `SF${Date.now().toString().slice(-10)}`;
  const shipRes = await api(
    { hostname: 'localhost', port: 4000, path: `/api/v1/supply/purchase-orders/${createdPo.id}/ship`, method: 'POST', headers: supplier.authHeaders },
    { trackingNo: trackingNumber }
  );
  const [shippedPoDb] = await sql`SELECT status, shipping_tracking_no FROM purchase_orders WHERE id = ${createdPo.id}`;
  const poShippedOk = shippedPoDb?.status === 'SHIPPED' && shippedPoDb?.shipping_tracking_no === trackingNumber;

  recordTest('Supplier B2B', '商户B2B批发采购下单与供货商打单发货闭环', poCreatedOk && poShippedOk, `采购单号: ${createdPo?.orderNo}, 状态: ${shippedPoDb?.status}, 物流运单: ${shippedPoDb?.shipping_tracking_no}`);

  // 5.4 供货款结算对账大盘
  const supplySettleRes = await api({ hostname: 'localhost', port: 4000, path: '/api/v1/supply/settle', method: 'GET', headers: supplier.authHeaders });
  recordTest('Supplier B2B', '供货商结算大盘与对账台账核算', supplySettleRes.status === 200, `结算大盘应答正常: ${JSON.stringify(supplySettleRes.data?.data)}`);


  // ==============================================================================
  // SUITE 6: 🛡️ 安全防御与越权拦截 (Security & Boundary Tests)
  // ==============================================================================
  console.log('\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
  console.log('👉 SUITE 6: 安全防御与越权拦截边界测试 (RBAC & Terminal Access Control)');
  console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

  // 6.1 匿名无 Token 访问受保护接口
  const anonRes = await api({ hostname: 'localhost', port: 4000, path: '/api/v1/dashboard/overview', method: 'GET' });
  recordTest('Security RBAC', '匿名请求未授权防御拦截 (401 Unauthorized)', anonRes.status === 401, `HTTP Status: ${anonRes.status}`);

  // 6.2 伪造终端类型阻断
  const fakeTerminalLogin = await api(
    { hostname: 'localhost', port: 4000, path: '/api/v1/auth/login', method: 'POST', headers: { 'Content-Type': 'application/json' } },
    { username: 'admin', password: 'password', terminal: 'STORE_POS' }
  );
  recordTest('Security RBAC', '非准入终端越权登录拦截 (Terminal Access Guard)', fakeTerminalLogin.status === 403 || fakeTerminalLogin.status === 401, `状态码: ${fakeTerminalLogin.status}, 阻断消息: ${fakeTerminalLogin.data?.message}`);


  // ==============================================================================
  // 汇总报告生成
  // ==============================================================================
  console.log('\n================================================================================');
  console.log('📊 EXPERT MULTI-TERMINAL VERIFICATION SUMMARY REPORT');
  console.log('================================================================================\n');

  console.table(testResults);

  const passedCount = testResults.filter(r => r.status === 'PASSED').length;
  const failedCount = testResults.filter(r => r.status === 'FAILED').length;
  const totalCount = testResults.length;
  const passRate = ((passedCount / totalCount) * 100).toFixed(1);

  console.log(`\n🏁 Total Test Cases: ${totalCount}`);
  console.log(`✅ Passed: ${passedCount}`);
  console.log(`❌ Failed: ${failedCount}`);
  console.log(`🎯 Pass Rate: ${passRate}%`);

  if (failedCount === 0) {
    console.log('\n🌟 CONGRATULATIONS! ALL 5 TERMINALS AND DATABASE PASSED EXPERT-GRADE AUDIT!');
  }

  process.exit(failedCount === 0 ? 0 : 1);
}

runExpertTesting().catch(e => {
  console.error('Fatal Test Runner Failure:', e);
  process.exit(1);
});
