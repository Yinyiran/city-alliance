import { migrate } from 'drizzle-orm/postgres-js/migrator';
import { drizzle } from 'drizzle-orm/postgres-js';
import postgres from 'postgres';
import * as path from 'path';

async function runMigration() {
  const connectionString =
    process.env.DATABASE_URL ||
    'postgresql://city_admin:city_secret_pass_2026@127.0.0.1:5432/city_alliance';

  console.log('Connecting to PostgreSQL for migration...');
  const migrationClient = postgres(connectionString, { max: 1 });
  const db = drizzle(migrationClient);

  const migrationsFolder = path.resolve(__dirname, '../../../drizzle');
  console.log(`Running migrations from: ${migrationsFolder}`);

  try {
    await migrate(db, { migrationsFolder });
    console.log('PostgreSQL 17 Migrations applied successfully! ✨');
  } catch (err) {
    console.error('Migration failed:', err);
    process.exit(1);
  } finally {
    await migrationClient.end();
  }
}

runMigration();
