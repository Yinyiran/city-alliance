import { spawn } from 'child_process';
import postgres from 'postgres';
import * as crypto from 'crypto';

interface DbxRow {
  [key: string]: string | null;
}

function createDbxClient() {
  const proc = spawn('node', ['C:\\nvm4w\\nodejs\\node_modules\\@dbx-app\\mcp-server\\bin\\dbx-mcp-server.js']);
  let buffer = '';
  let msgId = 1;
  const pending = new Map<number, (val: any) => void>();

  proc.stdout.on('data', (d) => {
    buffer += d.toString();
    const lines = buffer.split('\n');
    buffer = lines.pop() || '';
    for (const line of lines) {
      if (!line.trim()) continue;
      try {
        const msg = JSON.parse(line);
        if (pending.has(msg.id)) {
          const resolve = pending.get(msg.id)!;
          pending.delete(msg.id);
          resolve(msg);
        }
      } catch (e) {}
    }
  });

  function send(method: string, params: any = {}) {
    const id = msgId++;
    return new Promise<any>((resolve) => {
      pending.set(id, resolve);
      proc.stdin.write(JSON.stringify({ jsonrpc: '2.0', id, method, params }) + '\n');
    });
  }

  async function init() {
    await send('initialize', {
      protocolVersion: '2024-11-05',
      capabilities: {},
      clientInfo: { name: 'legacy-migrator', version: '1.0' },
    });
    proc.stdin.write(JSON.stringify({ jsonrpc: '2.0', method: 'notifications/initialized', params: {} }) + '\n');
  }

  async function query(sql: string, max_rows = 1000): Promise<DbxRow[]> {
    const res = await send('tools/call', {
      name: 'dbx_execute_query',
      arguments: {
        connection_name: 'cmyx-db',
        sql,
        max_rows,
        cell_char_limit: 4000,
      },
    });

    const text = res.result?.content?.[0]?.text || '';
    return parseMarkdownTable(text);
  }

  function close() {
    proc.kill();
  }

  return { init, query, close };
}

function parseMarkdownTable(markdown: string): DbxRow[] {
  const lines = markdown
    .split('\n')
    .map((l) => l.trim())
    .filter((l) => l.startsWith('|') && l.endsWith('|'));
  if (lines.length < 2) return [];

  const headers = lines[0]
    .slice(1, -1)
    .split('|')
    .map((h) => h.trim());
  const rows: DbxRow[] = [];

  for (let i = 2; i < lines.length; i++) {
    const cells = lines[i]
      .slice(1, -1)
      .split('|')
      .map((c) => c.trim());
    const obj: DbxRow = {};
    headers.forEach((h, idx) => {
      obj[h] = cells[idx] !== undefined ? cells[idx] : null;
    });
    rows.push(obj);
  }
  return rows;
}

function hashPassword(password: string): string {
  return crypto.createHash('sha256').update(password + '_city_alliance_salt').digest('hex');
}

async function runMigration() {
  console.log('====================================================');
  console.log('🚀 开始执行从 cmyx-db (MySQL) 到 PostgreSQL 17 的数据全量迁移');
  console.log('====================================================');

  const pg = postgres('postgres://city_admin:city_secret_pass_2026@127.0.0.1:5432/city_alliance');
  const dbx = createDbxClient();
  await dbx.init();

  try {
    // 0. 准备 RBAC 基础内置角色
    console.log('\n[Phase 0] 校验并初始化 RBAC 角色体系...');
    const defaultRoles = [
      { code: 'SUPER_ADMIN', name: '超级管理员', tenantType: 'PLATFORM', perms: ['*'] },
      {
        code: 'CITY_AGENT',
        name: '城市分公司代理',
        tenantType: 'CITY',
        perms: ['city:read', 'city:write', 'shop:read', 'shop:write', 'trade:read', 'settle:read'],
      },
      {
        code: 'SHOP_OWNER',
        name: '商家主理人',
        tenantType: 'SHOP',
        perms: [
          'shop:read',
          'shop:write',
          'store:read',
          'store:write',
          'goods:read',
          'goods:write',
          'trade:read',
          'trade:write',
          'member:read',
          'settle:read',
        ],
      },
      {
        code: 'STORE_MANAGER',
        name: '门店店长',
        tenantType: 'STORE',
        perms: ['store:read', 'clerk:read', 'clerk:write', 'trade:verify', 'shift:manage'],
      },
      {
        code: 'STORE_CLERK',
        name: '门店核销员',
        tenantType: 'STORE',
        perms: ['trade:verify', 'shift:manage'],
      },
      {
        code: 'SUPPLIER',
        name: '供应链供货商',
        tenantType: 'SUPPLY',
        perms: ['supply:goods:manage'],
      },
    ];

    const roleMap = new Map<string, string>();
    for (const r of defaultRoles) {
      const existing = await pg`SELECT id FROM roles WHERE code = ${r.code}`;
      if (existing.length > 0) {
        roleMap.set(r.code, existing[0].id);
      } else {
        const inserted = await pg`
          INSERT INTO roles (id, code, name, tenant_type, permissions, created_at)
          VALUES (gen_random_uuid(), ${r.code}, ${r.name}, ${r.tenantType}, ${r.perms}, NOW())
          RETURNING id
        `;
        roleMap.set(r.code, inserted[0].id);
      }
    }
    console.log(`✅ RBAC 角色准备完毕，共 ${roleMap.size} 个系统角色`);

    // 1. 迁移 website -> 组织租户 (PLATFORM & CITY)
    console.log('\n[Phase 1] 抽取并迁移 website 表 (平台总部与城市分公司)...');
    const legacyWebsites = await dbx.query('SELECT site_id, title, status FROM website');
    console.log(`从 cmyx-db 读取到 ${legacyWebsites.length} 个 website 记录`);

    const cityTenantMap = new Map<string, string>();
    let platformTenantId = '';

    for (const w of legacyWebsites) {
      const siteId = w.site_id || '0';
      const isPlatform = siteId === '0';
      const code = isPlatform ? 'PLATFORM_HQ' : `CITY_${siteId}`;
      const name = isPlatform ? '城盟优选平台总部' : (w.title || `城市分公司-${siteId}`);
      const type = isPlatform ? 'PLATFORM' : 'CITY';

      const existing = await pg`SELECT id FROM tenants WHERE code = ${code}`;
      let tenantId: string;
      if (existing.length > 0) {
        tenantId = existing[0].id;
        await pg`UPDATE tenants SET name = ${name}, status = 'ACTIVE', updated_at = NOW() WHERE id = ${tenantId}`;
      } else {
        const inserted = await pg`
          INSERT INTO tenants (id, name, code, type, parent_id, status, created_at, updated_at)
          VALUES (gen_random_uuid(), ${name}, ${code}, ${type}, ${isPlatform ? null : platformTenantId || null}, 'ACTIVE', NOW(), NOW())
          RETURNING id
        `;
        tenantId = inserted[0].id;
      }

      if (isPlatform) {
        platformTenantId = tenantId;
      } else {
        cityTenantMap.set(siteId, tenantId);
      }
    }
    console.log(`✅ 平台租户与 ${cityTenantMap.size} 个城市分公司租户迁移成功`);

    // 2. 迁移 user 表 -> users, user_roles
    console.log('\n[Phase 2] 抽取并迁移 user 表 (各端管理员与主理人账号)...');
    const legacyUsers = await dbx.query('SELECT uid, username, password, status, site_id, app_module FROM user');
    console.log(`从 cmyx-db 读取到 ${legacyUsers.length} 个用户记录`);

    const userMap = new Map<string, string>(); // username -> userId
    const seenUsernames = new Set<string>();

    for (const u of legacyUsers) {
      let rawUsername = (u.username || '').trim();
      if (!rawUsername) rawUsername = `user_${u.uid}`;

      // 处理多端可能存在的重复用户名
      let targetUsername = rawUsername;
      if (seenUsernames.has(targetUsername)) {
        targetUsername = `${rawUsername}_${u.app_module || 'u'}`;
      }
      seenUsernames.add(targetUsername);

      const appModule = (u.app_module || '').toLowerCase();
      let assignedTenantId: string | null = null;
      let assignedRoleCode = 'SHOP_OWNER';

      if (appModule === 'admin') {
        assignedTenantId = platformTenantId;
        assignedRoleCode = 'SUPER_ADMIN';
      } else if (appModule === 'city') {
        assignedTenantId = cityTenantMap.get(u.site_id || '') || platformTenantId;
        assignedRoleCode = 'CITY_AGENT';
      } else if (appModule === 'store') {
        assignedRoleCode = 'STORE_CLERK';
      } else {
        assignedRoleCode = 'SHOP_OWNER';
      }

      const existingUser = await pg`SELECT id FROM users WHERE username = ${targetUsername}`;
      let userId: string;
      const status = u.status === '1' ? 'ACTIVE' : 'DISABLED';
      const passwordHash = u.password || hashPassword('123456');

      if (existingUser.length > 0) {
        userId = existingUser[0].id;
        await pg`
          UPDATE users
          SET password_hash = ${passwordHash}, nickname = ${rawUsername}, tenant_id = ${assignedTenantId}, status = ${status}, updated_at = NOW()
          WHERE id = ${userId}
        `;
      } else {
        const inserted = await pg`
          INSERT INTO users (id, username, password_hash, nickname, tenant_id, status, created_at, updated_at)
          VALUES (gen_random_uuid(), ${targetUsername}, ${passwordHash}, ${rawUsername}, ${assignedTenantId}, ${status}, NOW(), NOW())
          RETURNING id
        `;
        userId = inserted[0].id;
      }

      userMap.set(targetUsername, userId);
      userMap.set(rawUsername, userId);

      // 绑定 RBAC 角色关联
      const roleId = roleMap.get(assignedRoleCode);
      if (roleId) {
        await pg`DELETE FROM user_roles WHERE user_id = ${userId}`;
        await pg`INSERT INTO user_roles (user_id, role_id) VALUES (${userId}, ${roleId})`;
      }
    }
    console.log(`✅ ${legacyUsers.length} 个用户及 RBAC 角色权限绑定成功`);

    // 3. 迁移 shop 表 -> tenants (SHOP) & shops
    console.log('\n[Phase 3] 抽取并迁移 shop 表 (品牌商家与主理人)...');
    const legacyShops = await dbx.query(
      'SELECT site_id, site_name, username, website_id, logo, telephone, mobile FROM shop'
    );
    console.log(`从 cmyx-db 读取到 ${legacyShops.length} 个商家记录`);

    const shopMap = new Map<string, string>(); // site_id -> shops.id

    for (const s of legacyShops) {
      const siteId = s.site_id || '';
      const shopName = s.site_name || `商家_${siteId}`;
      const tenantCode = `SHOP_${siteId}`;

      // 确定所属城市分公司
      let parentCityId: string | null = null;
      if (s.website_id && s.website_id !== '-1' && cityTenantMap.has(s.website_id)) {
        parentCityId = cityTenantMap.get(s.website_id)!;
      } else {
        parentCityId = platformTenantId;
      }

      // 确定所属主理人用户
      let ownerUserId = userMap.get((s.username || '').trim());
      if (!ownerUserId) {
        // 创建该商家专属的主理人用户
        const ownerUsername = s.username?.trim() || `shop_user_${siteId}`;
        const newOwner = await pg`
          INSERT INTO users (id, username, password_hash, nickname, status, created_at, updated_at)
          VALUES (gen_random_uuid(), ${ownerUsername}, ${hashPassword('123456')}, ${shopName}, 'ACTIVE', NOW(), NOW())
          ON CONFLICT (username) DO UPDATE SET updated_at = NOW()
          RETURNING id
        `;
        ownerUserId = newOwner[0].id as string;
        userMap.set(ownerUsername, ownerUserId);

        const shopRoleId = roleMap.get('SHOP_OWNER');
        if (shopRoleId && ownerUserId) {
          await pg`INSERT INTO user_roles (user_id, role_id) VALUES (${ownerUserId}, ${shopRoleId}) ON CONFLICT DO NOTHING`;
        }
      }

      if (!ownerUserId) continue;

      // 创建或更新商家组织租户
      const existingTenant = await pg`SELECT id FROM tenants WHERE code = ${tenantCode}`;
      let shopTenantId: string;
      if (existingTenant.length > 0) {
        shopTenantId = existingTenant[0].id;
        await pg`UPDATE tenants SET name = ${shopName}, parent_id = ${parentCityId ?? null}, updated_at = NOW() WHERE id = ${shopTenantId}`;
      } else {
        const insertedTenant = await pg`
          INSERT INTO tenants (id, name, code, type, parent_id, status, created_at, updated_at)
          VALUES (gen_random_uuid(), ${shopName}, ${tenantCode}, 'SHOP', ${parentCityId ?? null}, 'ACTIVE', NOW(), NOW())
          RETURNING id
        `;
        shopTenantId = insertedTenant[0].id;
      }

      // 关联 user 的 tenant_id
      await pg`UPDATE users SET tenant_id = ${shopTenantId} WHERE id = ${ownerUserId}`;

      // 创建或更新 shops 表
      const logoUrl = s.logo || '/upload/default/shop/shop_logo.png';
      const existingShop = await pg`SELECT id FROM shops WHERE tenant_id = ${shopTenantId}`;
      let shopId: string;
      if (existingShop.length > 0) {
        shopId = existingShop[0].id;
        await pg`
          UPDATE shops
          SET name = ${shopName}, logo_url = ${logoUrl}, city_id = ${parentCityId ?? null}, owner_user_id = ${ownerUserId}, updated_at = NOW()
          WHERE id = ${shopId}
        `;
      } else {
        const insertedShop = await pg`
          INSERT INTO shops (id, tenant_id, name, logo_url, city_id, owner_user_id, sales_multiplier, status, created_at, updated_at)
          VALUES (gen_random_uuid(), ${shopTenantId}, ${shopName}, ${logoUrl}, ${parentCityId ?? null}, ${ownerUserId}, '1.00', 'ACTIVE', NOW(), NOW())
          RETURNING id
        `;
        shopId = insertedShop[0].id;
      }

      shopMap.set(siteId, shopId);
    }
    console.log(`✅ ${shopMap.size} 个商家与租户数据迁移成功`);

    // 4. 迁移 store 表 -> stores
    console.log('\n[Phase 4] 抽取并迁移 store 表 (同城实体门店与空间坐标)...');
    const legacyStores = await dbx.query(
      'SELECT store_id, store_name, telphone, site_id, address, longitude, latitude, status FROM store'
    );
    console.log(`从 cmyx-db 读取到 ${legacyStores.length} 个门店记录`);

    let storeMigratedCount = 0;
    const defaultShopId = shopMap.values().next().value;

    for (const st of legacyStores) {
      const storeName = st.store_name || `同城门店_${st.store_id}`;
      const shopId = (st.site_id && shopMap.get(st.site_id)) || defaultShopId;
      if (!shopId) continue;

      const phone = st.telphone || '13800000000';
      const address = st.address || '同城服务门店';
      const lat = st.latitude && !isNaN(Number(st.latitude)) && Number(st.latitude) !== 0 ? st.latitude : '23.1291000';
      const lng = st.longitude && !isNaN(Number(st.longitude)) && Number(st.longitude) !== 0 ? st.longitude : '113.2644000';
      const status = st.status === '1' ? 'OPEN' : 'CLOSED';

      const existingStore = await pg`SELECT id FROM stores WHERE shop_id = ${shopId} AND name = ${storeName}`;
      if (existingStore.length === 0) {
        await pg`
          INSERT INTO stores (id, shop_id, name, phone, address, latitude, longitude, status, created_at, updated_at)
          VALUES (gen_random_uuid(), ${shopId}, ${storeName}, ${phone}, ${address}, ${lat}, ${lng}, ${status}, NOW(), NOW())
        `;
        storeMigratedCount++;
      }
    }
    console.log(`✅ 实体门店迁移成功 (新增/同步 ${storeMigratedCount} 处同城网点)`);

    // 5. 迁移 member 表 -> members
    console.log('\n[Phase 5] 抽取并迁移 member 表 (会员档案与资产余额)...');
    const legacyMembers = await dbx.query(
      'SELECT member_id, mobile, username, nickname, realname, id_card, job_type, point, balance, site_id, source_member FROM member'
    );
    console.log(`从 cmyx-db 读取到 ${legacyMembers.length} 个会员记录`);

    const memberIdToPostgresId = new Map<string, string>();
    let memberMigratedCount = 0;

    for (const m of legacyMembers) {
      const memberIdStr = m.member_id || '0';
      const memberCode = String(memberIdStr).padStart(6, '0');
      const mobile = m.mobile?.trim() || null;
      const username = mobile || `member_${memberIdStr}`;
      const nickname = m.nickname || m.realname || `会员${memberCode}`;

      // 确保对应的用户登录身份存在
      const existingUser = await pg`SELECT id FROM users WHERE username = ${username}`;
      let memberUserId: string;
      if (existingUser.length > 0) {
        memberUserId = existingUser[0].id;
      } else {
        const insertedUser = await pg`
          INSERT INTO users (id, username, password_hash, nickname, mobile, status, created_at, updated_at)
          VALUES (gen_random_uuid(), ${username}, ${hashPassword('123456')}, ${nickname}, ${mobile}, 'ACTIVE', NOW(), NOW())
          RETURNING id
        `;
        memberUserId = insertedUser[0].id;
      }

      // 绑定商家归属
      const bindShopId = (m.site_id && shopMap.get(m.site_id)) || null;
      const points = Math.floor(Number(m.point) || 0);
      const balance = isNaN(Number(m.balance)) ? '0.00' : Number(m.balance).toFixed(2);
      const realName = m.realname || null;
      const idCardNo = m.id_card || null;
      const employmentType = m.job_type || '其它';

      const existingMember = await pg`SELECT id FROM members WHERE member_code = ${memberCode}`;
      let pgMemberId: string;
      if (existingMember.length > 0) {
        pgMemberId = existingMember[0].id;
        await pg`
          UPDATE members
          SET points = ${points}, balance = ${balance}, real_name = ${realName}, id_card_no = ${idCardNo}, employment_type = ${employmentType}, bind_shop_id = ${bindShopId}, updated_at = NOW()
          WHERE id = ${pgMemberId}
        `;
      } else {
        const inserted = await pg`
          INSERT INTO members (id, member_code, user_id, level, points, balance, real_name, id_card_no, employment_type, bind_shop_id, status, created_at, updated_at)
          VALUES (gen_random_uuid(), ${memberCode}, ${memberUserId}, 'NORMAL', ${points}, ${balance}, ${realName}, ${idCardNo}, ${employmentType}, ${bindShopId}, 'ACTIVE', NOW(), NOW())
          RETURNING id
        `;
        pgMemberId = inserted[0].id;
        memberMigratedCount++;
      }
      memberIdToPostgresId.set(memberIdStr, pgMemberId);
    }

    // 建立终身锁客引荐人关联 (inviter_id)
    for (const m of legacyMembers) {
      if (m.source_member && m.source_member !== '0' && memberIdToPostgresId.has(m.source_member)) {
        const myPgId = memberIdToPostgresId.get(m.member_id || '');
        const parentPgId = memberIdToPostgresId.get(m.source_member);
        if (myPgId && parentPgId) {
          await pg`UPDATE members SET inviter_id = ${parentPgId} WHERE id = ${myPgId}`;
        }
      }
    }
    console.log(`✅ ${legacyMembers.length} 名会员及终身锁客关系迁移成功`);

    // 6. 迁移 goods 表 -> goods_spu, goods_sku
    console.log('\n[Phase 6] 抽取并迁移 goods 表 (商品SPU与规格矩阵SKU)...');
    const legacyGoods = await dbx.query(
      'SELECT goods_id, goods_name, site_id, price, goods_image, goods_state FROM goods'
    );
    console.log(`从 cmyx-db 读取到 ${legacyGoods.length} 个商品记录`);

    let spuMigratedCount = 0;
    for (const g of legacyGoods) {
      const goodsId = g.goods_id || '';
      const goodsName = g.goods_name || `商品_${goodsId}`;
      const shopId = (g.site_id && shopMap.get(g.site_id)) || defaultShopId;
      if (!shopId) continue;

      const price = isNaN(Number(g.price)) ? '0.00' : Number(g.price).toFixed(2);
      const coverImage = g.goods_image || '/upload/default/goods.png';
      const status = g.goods_state === '1' ? 'ON_SALE' : 'OFF_SALE';

      // 避免重复迁移
      const existingSpu = await pg`SELECT id FROM goods_spu WHERE shop_id = ${shopId} AND name = ${goodsName}`;
      let spuId: string;
      if (existingSpu.length > 0) {
        spuId = existingSpu[0].id;
      } else {
        const insertedSpu = await pg`
          INSERT INTO goods_spu (id, shop_id, name, trade_type, cover_image, base_price, status, created_at, updated_at)
          VALUES (gen_random_uuid(), ${shopId}, ${goodsName}, 'EXPRESS', ${coverImage}, ${price}, ${status}, NOW(), NOW())
          RETURNING id
        `;
        spuId = insertedSpu[0].id;
        spuMigratedCount++;

        // 自动初始化默认规格 SKU 矩阵
        await pg`
          INSERT INTO goods_sku (id, spu_id, sku_code, spec_values, price, stock, created_at, updated_at)
          VALUES (gen_random_uuid(), ${spuId}, ${`SKU_${goodsId}`}, '{}'::jsonb, ${price}, 999, NOW(), NOW())
        `;
      }
    }
    console.log(`✅ 商品库迁移成功 (新增/同步 ${spuMigratedCount} 款商品SPU与SKU矩阵)`);

    console.log('\n====================================================');
    console.log('🎉 历史数据全量迁移顺利完成！');
    console.log('====================================================');
  } catch (err: any) {
    console.error('❌ 迁移执行异常中断:', err);
    throw err;
  } finally {
    dbx.close();
    await pg.end();
  }
}

runMigration().catch((e) => {
  console.error(e);
  process.exit(1);
});
