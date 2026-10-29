const postgres = require('postgres');

const sql = postgres('postgresql://city_admin:city_secret_pass_2026@127.0.0.1:5432/city_alliance');

async function main() {
  console.log('Testing PostgreSQL 17 connection...');
  const result = await sql`SELECT current_user, current_database(), version()`;
  console.log('PostgreSQL Connection Success:', result[0]);

  const ext = await sql`SELECT extname, extversion FROM pg_extension`;
  console.log('Active Extensions:', ext);

  const tables = await sql`SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'`;
  console.log('Existing Tables Count:', tables.length);
  console.log('Table Names:', tables.map(t => t.table_name));

  await sql.end();
}

main().catch((err) => {
  console.error('Connection failed:', err);
  process.exit(1);
});
