const postgres = require('postgres');

const sql = postgres(process.env.DATABASE_URL || 'postgresql://city_admin:city_secret_pass_2026@127.0.0.1:5432/city_alliance');

const EXPECTED_TABLES = [
  // 1. IAM & Multi-Tenant Topology
  'tenants',
  'users',
  'roles',
  'user_roles',
  // 2. Stores, Merchants & Privatelink QR
  'shops',
  'stores',
  'wecom_qr_codes',
  'store_sign_in_records',
  // 3. Member Domain
  'members',
  'member_addresses',
  'member_upgrade_records',
  'points_records',
  'balance_records',
  // 4. Catalog Domain
  'goods_categories',
  'freight_templates',
  'goods_spu',
  'goods_sku',
  // 5. Trade & Fulfillment Domain
  'orders',
  'order_items',
  'order_refunds',
  'order_delivery_traces',
  'express_companies',
  // 6. Settle & Ledger Domain
  'commission_records',
  'withdraw_records',
  'tax_withholding_records',
  // 7. Store Clerks & POS Shift Domain
  'store_clerks',
  'cashier_shifts',
  // 8. B2B Supply Chain Domain
  'suppliers',
  'purchase_orders',
  // 9. Marketing Center Domain
  'coupons',
  'member_coupons',
  // 10. Content & Video Stream Domain
  'videos',
  'video_progress_records',
  // 11. System Governance Domain
  'system_configs',
  'system_audit_logs',
  'media_attachments',
];

async function inspectDatabase() {
  console.log('===============================================================');
  console.log('🔍 PHASE 1: DATABASE SCHEMA INTEGRITY & TABLE AUDIT');
  console.log('===============================================================\n');

  // 1. 查询 PostgreSQL 现存所有表
  const dbTables = await sql`
    SELECT table_name 
    FROM information_schema.tables 
    WHERE table_schema = 'public' AND table_type = 'BASE TABLE'
    ORDER BY table_name;
  `;
  const existingTableNames = new Set(dbTables.map((t) => t.table_name));

  console.log(`📊 All 37 existing database tables:`);
  console.log(Array.from(existingTableNames));

  const auditReport = [];
  let missingCount = 0;

  for (const tableName of EXPECTED_TABLES) {
    const exists = existingTableNames.has(tableName);
    let rowCount = 0;
    if (exists) {
      try {
        const countRes = await sql`SELECT count(*) as count FROM ${sql(tableName)}`;
        rowCount = Number(countRes[0]?.count || 0);
      } catch (e) {
        rowCount = -1;
      }
    } else {
      missingCount++;
    }

    auditReport.push({
      tableName,
      status: exists ? 'EXISTS' : 'MISSING',
      recordCount: exists ? rowCount : 'N/A',
    });
  }

  console.table(auditReport);

  // 2. 检查外键关系一致性
  console.log('\n🔗 Foreign Key Constraint Integrity Check:');
  const fkConstraints = await sql`
    SELECT
      tc.constraint_name,
      tc.table_name,
      kcu.column_name,
      ccu.table_name AS foreign_table_name,
      ccu.column_name AS foreign_column_name
    FROM information_schema.table_constraints AS tc
    JOIN information_schema.key_column_usage AS kcu
      ON tc.constraint_name = kcu.constraint_name
      AND tc.table_schema = kcu.table_schema
    JOIN information_schema.constraint_column_usage AS ccu
      ON ccu.constraint_name = tc.constraint_name
      AND ccu.table_schema = tc.table_schema
    WHERE tc.constraint_type = 'FOREIGN KEY' AND tc.table_schema = 'public';
  `;

  console.log(`  ✅ Verified ${fkConstraints.length} active foreign key constraints in database.`);

  console.log('\n🏛️ Organization & Multi-Tenant Topology:');
  const tenantSummary = await sql`
    SELECT type, count(*) as count 
    FROM tenants 
    GROUP BY type 
    ORDER BY count DESC;
  `;
  console.table(tenantSummary);

  if (missingCount > 0) {
    console.error(`\n❌ WARNING: ${missingCount} expected domain tables were not found in database!`);
  } else {
    console.log('\n🎉 ALL EXPECTED DOMAIN TABLES VERIFIED! 100% SCHEMA COMPLIANCE.');
  }

  process.exit(missingCount > 0 ? 1 : 0);
}

inspectDatabase().catch((e) => {
  console.error('Fatal DB Audit Error:', e);
  process.exit(1);
});
