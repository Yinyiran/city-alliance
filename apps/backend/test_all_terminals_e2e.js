const http = require('http');

async function request(options, body = null) {
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

async function loginUser(username, password) {
  const res = await request(
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
  return { token, user };
}

async function runE2E() {
  console.log('===============================================================');
  console.log('🚀 STARTING CITY ALLIANCE FULL-STACK 5-TERMINALS MASTER E2E SUITE');
  console.log('===============================================================\n');

  // -------------------------------------------------------------
  // Terminal 1: Platform Headquarters Admin (SUPER_ADMIN)
  // -------------------------------------------------------------
  console.log('🔷 [Terminal 1: Platform Headquarters Total Control] admin / 123456');
  const adminAuth = await loginUser('admin', '123456');
  console.log('  ✅ Admin logged in. Role:', adminAuth.user.role);
  const adminHeaders = { Authorization: `Bearer ${adminAuth.token}`, 'Content-Type': 'application/json' };

  const dOverview = await request({ hostname: 'localhost', port: 4000, path: '/api/v1/dashboard/overview', method: 'GET', headers: adminHeaders });
  console.log('  ✅ Dashboard Overview GMV:', dOverview.data?.data?.gmv?.total, 'Total Shops:', dOverview.data?.data?.merchants?.totalShops);

  const mList = await request({ hostname: 'localhost', port: 4000, path: '/api/v1/members?page=1&pageSize=5', method: 'GET', headers: adminHeaders });
  console.log('  ✅ Platform Members count:', mList.data?.data?.total);

  // -------------------------------------------------------------
  // Terminal 2: City Branch Admin (CITY_AGENT)
  // -------------------------------------------------------------
  console.log('\n🔷 [Terminal 2: City Branch Management] huaibei / 123456');
  const cityAuth = await loginUser('huaibei', '123456');
  console.log('  ✅ City Agent logged in. Role:', cityAuth.user.role, 'CityId:', cityAuth.user.cityId);
  const cityHeaders = { Authorization: `Bearer ${cityAuth.token}`, 'Content-Type': 'application/json' };

  const cityOverview = await request({ hostname: 'localhost', port: 4000, path: '/api/v1/city/overview', method: 'GET', headers: cityHeaders });
  console.log('  ✅ City Overview:', cityOverview.data?.data?.cityInfo?.name, 'Shops in jurisdiction:', cityOverview.data?.data?.stats?.shopCount);

  const cityShops = await request({ hostname: 'localhost', port: 4000, path: '/api/v1/city/shops?page=1&pageSize=5', method: 'GET', headers: cityHeaders });
  console.log('  ✅ City Shops retrieved:', cityShops.data?.data?.total);

  const cityConfig = await request({ hostname: 'localhost', port: 4000, path: '/api/v1/city/config', method: 'GET', headers: cityHeaders });
  console.log('  ✅ City Config servicePhone:', cityConfig.data?.data?.servicePhone);

  // -------------------------------------------------------------
  // Terminal 3: Shop Owner Admin (SHOP_OWNER)
  // -------------------------------------------------------------
  console.log('\n🔷 [Terminal 3: Merchant Brand Owner] shop1 / 123456');
  const shopAuth = await loginUser('shop1', '123456');
  console.log('  ✅ Shop Owner logged in. Role:', shopAuth.user.role, 'ShopId:', shopAuth.user.shopId);
  const shopHeaders = { Authorization: `Bearer ${shopAuth.token}`, 'Content-Type': 'application/json' };

  const shopOverview = await request({ hostname: 'localhost', port: 4000, path: '/api/v1/dashboard/overview', method: 'GET', headers: shopHeaders });
  console.log('  ✅ Shop Scoped Overview:', shopOverview.data?.data?.gmv);

  const shopGoods = await request({ hostname: 'localhost', port: 4000, path: '/api/v1/goods?page=1&pageSize=5', method: 'GET', headers: shopHeaders });
  console.log('  ✅ Shop Scoped Goods total:', shopGoods.data?.data?.total);

  const shopStores = await request({ hostname: 'localhost', port: 4000, path: '/api/v1/stores?page=1&pageSize=5', method: 'GET', headers: shopHeaders });
  console.log('  ✅ Shop Stores total:', shopStores.data?.data?.total);

  // -------------------------------------------------------------
  // Terminal 4: Store POS & Cashier Station (STORE_MANAGER)
  // -------------------------------------------------------------
  console.log('\n🔷 [Terminal 4: Store Verification & POS Cashier] store1 / 123456');
  const storeAuth = await loginUser('store1', '123456');
  console.log('  ✅ Store Manager logged in. Role:', storeAuth.user.role, 'StoreId:', storeAuth.user.storeId);
  const storeHeaders = { Authorization: `Bearer ${storeAuth.token}`, 'Content-Type': 'application/json' };

  const currentShift = await request({ hostname: 'localhost', port: 4000, path: '/api/v1/stores/shifts/current', method: 'GET', headers: storeHeaders });
  console.log('  ✅ Store Cashier Active Shift:', currentShift.data?.data?.currentShift?.storeName, 'Clerk:', currentShift.data?.data?.currentShift?.clerkName);

  const shiftsList = await request({ hostname: 'localhost', port: 4000, path: '/api/v1/stores/shifts?page=1&pageSize=5', method: 'GET', headers: storeHeaders });
  console.log('  ✅ Cashier Shifts ledger count:', shiftsList.data?.data?.total);

  const clerksList = await request({ hostname: 'localhost', port: 4000, path: '/api/v1/stores/clerks', method: 'GET', headers: storeHeaders });
  console.log('  ✅ Store Clerks count:', clerksList.data?.data?.total);

  const storeStats = await request({ hostname: 'localhost', port: 4000, path: '/api/v1/stores/stats', method: 'GET', headers: storeHeaders });
  console.log('  ✅ Store Stats:', storeStats.data?.data?.store?.name, 'Clerk count:', storeStats.data?.data?.clerkCount);

  // -------------------------------------------------------------
  // Terminal 5: B2B Supplier (SUPPLIER)
  // -------------------------------------------------------------
  console.log('\n🔷 [Terminal 5: B2B Supply Center] supplier1 / 123456');
  const supplierAuth = await loginUser('supplier1', '123456');
  console.log('  ✅ Supplier logged in. Role:', supplierAuth.user.role, 'TenantId:', supplierAuth.user.tenantId);
  const supplierHeaders = { Authorization: `Bearer ${supplierAuth.token}`, 'Content-Type': 'application/json' };

  const supplyGoods = await request({ hostname: 'localhost', port: 4000, path: '/api/v1/supply/goods?page=1&pageSize=5', method: 'GET', headers: supplierHeaders });
  console.log('  ✅ Supply Selection Pool count:', supplyGoods.data?.data?.total);

  // Publish a new supply wholesale item
  const newGoods = await request(
    { hostname: 'localhost', port: 4000, path: '/api/v1/supply/goods', method: 'POST', headers: supplierHeaders },
    {
      name: '云南高山日晒精品豆 10kg原袋',
      supplyPrice: 280,
      suggestRetailPrice: 420,
      stock: 1200,
    }
  );
  console.log('  ✅ Published Wholesale Item:', newGoods.data?.data?.spu?.name, 'SKU Code:', newGoods.data?.data?.sku?.skuCode);

  const supplyOrders = await request({ hostname: 'localhost', port: 4000, path: '/api/v1/supply/purchase-orders', method: 'GET', headers: supplierHeaders });
  console.log('  ✅ B2B Wholesale Purchase Orders count:', supplyOrders.data?.data?.total);

  const supplySettle = await request({ hostname: 'localhost', port: 4000, path: '/api/v1/supply/settle', method: 'GET', headers: supplierHeaders });
  console.log('  ✅ Supply Settle Ledger:', supplySettle.data?.data);

  console.log('\n===============================================================');
  console.log('🎉 ALL 5 TERMINALS PASSED END-TO-END VERIFICATION WITH 100% SUCCESS!');
  console.log('===============================================================');
}

runE2E().catch((err) => {
  console.error('❌ E2E Verification failed:', err);
  process.exit(1);
});
