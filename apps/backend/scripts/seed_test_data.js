const postgres = require('postgres');
const crypto = require('crypto');

const sql = postgres('postgresql://city_admin:city_secret_pass_2026@127.0.0.1:5432/city_alliance');

function hashPassword(password) {
  return crypto.createHash('sha256').update(password + 'city_salt_2026').digest('hex');
}

async function seed() {
  console.log('Seeding initial data into PostgreSQL 17...');

  // 1. 创建平台租户
  const [tenant] = await sql`
    INSERT INTO tenants (name, code, type, status)
    VALUES ('城盟优选平台总部', 'PLATFORM_HQ', 'PLATFORM', 'ACTIVE')
    ON CONFLICT (code) DO UPDATE SET name = EXCLUDED.name
    RETURNING id, code
  `;
  console.log('Tenant:', tenant);

  // 2. 创建超级管理员账号
  const pwdHash = hashPassword('123456');
  const [adminUser] = await sql`
    INSERT INTO users (tenant_id, username, password_hash, nickname, mobile, status)
    VALUES (${tenant.id}, 'admin', ${pwdHash}, '平台总管理员', '13800000000', 'ACTIVE')
    ON CONFLICT (username) DO UPDATE SET password_hash = EXCLUDED.password_hash
    RETURNING id, username
  `;
  console.log('Admin User:', adminUser);

  // 3. 创建品牌商户与门店
  const [shop] = await sql`
    INSERT INTO shops (tenant_id, name, owner_user_id, logo_url, sales_multiplier, status)
    VALUES (${tenant.id}, '城盟精选旗舰店', ${adminUser.id}, 'https://img.city-alliance.com/logo.png', '1.5', 'ACTIVE')
    RETURNING id, name
  `;
  console.log('Shop:', shop);

  const [store] = await sql`
    INSERT INTO stores (shop_id, name, phone, address, latitude, longitude, status)
    VALUES (${shop.id}, '城盟优选（万达广场店）', '0755-88888888', '广东省深圳市南山区万达广场一楼 A01', '22.543099', '113.948739', 'OPEN')
    RETURNING id, name
  `;
  console.log('Store:', store);

  // 4. 创建 6 位测试会员 (钻石会员)
  const [member] = await sql`
    INSERT INTO members (member_code, user_id, level, bind_shop_id, bind_store_id, balance, points, is_real_name_verified)
    VALUES ('888888', ${adminUser.id}, 'DIAMOND', ${shop.id}, ${store.id}, '1000.00', 3000, true)
    ON CONFLICT (member_code) DO UPDATE SET level = EXCLUDED.level
    RETURNING id, member_code, level
  `;
  console.log('Member:', member);

  // 5. 创建商品 SPU 与 SKU
  const [spu] = await sql`
    INSERT INTO goods_spu (shop_id, name, trade_type, base_price, cover_image, status)
    VALUES (${shop.id}, '城盟特选高定卫衣', 'EXPRESS', '199.00', 'https://img.city-alliance.com/weiyi.png', 'ON_SALE')
    RETURNING id, name
  `;

  const [sku1] = await sql`
    INSERT INTO goods_sku (spu_id, sku_code, spec_values, price, stock)
    VALUES (${spu.id}, 'SKU_WY_BLK_M', ${JSON.stringify({ "颜色": "经典黑", "尺码": "M" })}, '199.00', 100)
    RETURNING id, sku_code, price
  `;
  const [sku2] = await sql`
    INSERT INTO goods_sku (spu_id, sku_code, spec_values, price, stock)
    VALUES (${spu.id}, 'SKU_WY_BLK_L', ${JSON.stringify({ "颜色": "经典黑", "尺码": "L" })}, '199.00', 100)
    RETURNING id, sku_code, price
  `;
  console.log('SPU & SKUs seeded:', spu.name, [sku1.sku_code, sku2.sku_code]);

  console.log('Seed completed successfully! ✨');
  await sql.end();
}

seed().catch(err => {
  console.error('Seed failed:', err);
  process.exit(1);
});
