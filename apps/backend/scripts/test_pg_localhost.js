const postgres = require(require.resolve('postgres', { paths: [process.cwd(), process.cwd() + '/apps/backend'] }));

const sql = postgres('postgresql://city_admin:city_secret_pass_2026@localhost:5432/city_alliance');

async function test() {
  console.log('Testing PostgreSQL connection with localhost...');
  const res = await sql`SELECT current_user, current_database(), version()`;
  console.log('PostgreSQL Connection SUCCESS with localhost!');
  console.log('User & DB:', res[0].current_user, '@', res[0].current_database);
  console.log('Version:', res[0].version);

  const extensions = await sql`SELECT extname, extversion FROM pg_extension`;
  console.log('Active Extensions:', extensions.map(e => `${e.extname} (${e.extversion})`).join(', '));

  await sql.end();
}

test().catch(err => {
  console.error('PostgreSQL Connection failed with localhost:', err);
  process.exit(1);
});
