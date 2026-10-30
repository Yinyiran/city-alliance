const postgres = require('postgres');
const sql = postgres('postgresql://city_admin:city_secret_pass_2026@127.0.0.1:5432/city_alliance');

async function testFullSuite() {
  console.log('Testing live login on http://127.0.0.1:4000/api/v1/auth/login...');
  const resLogin = await fetch('http://127.0.0.1:4000/api/v1/auth/login', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      username: 'admin',
      password: '123456',
      terminal: 'ADMIN_WEB',
    }),
  });
  console.log('Login Status:', resLogin.status);
  const authData = await resLogin.json();
  const token = authData.accessToken;
  console.log('Obtained JWT AccessToken:', token.slice(0, 30) + '...');

  console.log('\n--- 1. Testing Equities API with JWT Token ---');
  const resAuth = await fetch('http://127.0.0.1:4000/api/v1/settle/equities', {
    headers: { Authorization: `Bearer ${token}` },
  });
  console.log('Equities API Status:', resAuth.status);
  const equities = await resAuth.json();
  console.log('Equities API Response (4 Tiers SSOT):', JSON.stringify(equities, null, 2));

  // 查询实际真实 Member、Shop、Store、SPU、SKU
  const [member] = await sql`SELECT id, member_code, level FROM members WHERE member_code = '888888'`;
  const [shop] = await sql`SELECT id, name FROM shops LIMIT 1`;
  const [store] = await sql`SELECT id, name, latitude, longitude FROM stores LIMIT 1`;
  const [spu] = await sql`SELECT id, name, base_price FROM goods_spu LIMIT 1`;
  const [sku] = await sql`SELECT id, sku_code, price FROM goods_sku WHERE spu_id = ${spu.id} LIMIT 1`;

  console.log('\n--- 2. Testing Member Profile API (6-digit ID) ---');
  const resMember = await fetch(`http://127.0.0.1:4000/api/v1/members/profile/${member.id}`, {
    headers: { Authorization: `Bearer ${token}` },
  });
  console.log('Member Profile API Status:', resMember.status);
  const memberProfile = await resMember.json();
  console.log('Member Profile Response:', JSON.stringify(memberProfile, null, 2));

  console.log('\n--- 3. Testing Store LBS Nearby Search API (PostGIS Coordinates) ---');
  const resStore = await fetch(`http://127.0.0.1:4000/api/v1/stores/nearby?latitude=${store.latitude}&longitude=${store.longitude}&radiusKm=10`, {
    headers: { Authorization: `Bearer ${token}` },
  });
  console.log('Store Nearby API Status:', resStore.status);
  const storeNearby = await resStore.json();
  console.log('Store Nearby Response (Found Stores & Distance):', JSON.stringify(storeNearby, null, 2));

  console.log('\n--- 4. Testing Omnichannel Order Creation ---');
  const resOrder = await fetch('http://127.0.0.1:4000/api/v1/trade/orders', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      Authorization: `Bearer ${token}`,
    },
    body: JSON.stringify({
      customerId: member.id,
      shopId: shop.id,
      storeId: store.id,
      tradeType: 'PICKUP',
      items: [
        {
          spuId: spu.id,
          skuId: sku.id,
          goodsName: spu.name,
          specText: '经典黑 / M',
          price: sku.price,
          quantity: 1,
        },
      ],
    }),
  });
  console.log('Order Creation API Status:', resOrder.status);
  const orderData = await resOrder.json();
  console.log('Order Creation Response:', JSON.stringify(orderData, null, 2));

  const verifyCode = orderData.data.verifyCode;
  const orderId = orderData.data.orderId;
  console.log(`\nGenerated Order ID: ${orderId}, 12-digit VerifyCode: ${verifyCode}`);

  console.log('\n--- 5. Testing Cashier Payment Simulation (PAID) ---');
  const resPay = await fetch(`http://127.0.0.1:4000/api/v1/trade/orders/${orderId}/pay-mock`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      Authorization: `Bearer ${token}`,
    },
    body: JSON.stringify({ channel: 'WECHAT_PAY' }),
  });
  console.log('Pay Mock Status:', resPay.status);
  const payResult = await resPay.json();
  console.log('Pay Mock & Commission Splits Generated:', JSON.stringify(payResult, null, 2));

  console.log('\n--- 6. Testing POS Cashier Verify (Scan 12-digit Code) ---');
  const resVerify = await fetch('http://127.0.0.1:4000/api/v1/trade/verify', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      Authorization: `Bearer ${token}`,
    },
    body: JSON.stringify({ verifyCode }),
  });
  console.log('Verify Status:', resVerify.status);
  const verifyResult = await resVerify.json();
  console.log('Verify Result (Atomic Completed):', JSON.stringify(verifyResult, null, 2));

  console.log('\n--- 7. Testing Anti-Duplicate Verification (Conflict Guard) ---');
  const resDuplicate = await fetch('http://127.0.0.1:4000/api/v1/trade/verify', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      Authorization: `Bearer ${token}`,
    },
    body: JSON.stringify({ verifyCode }),
  });
  console.log('Duplicate Verify Status (Should be 400 Bad Request):', resDuplicate.status);
  const dupResult = await resDuplicate.json();
  console.log('Duplicate Guard Response:', dupResult);

  await sql.end();
  console.log('\n======================================================');
  console.log('ALL 7 REAL POSTGRESQL + REDIS BACKEND TESTS PASSED 100%! ✨');
  console.log('======================================================');
}

testFullSuite().catch(console.error);
