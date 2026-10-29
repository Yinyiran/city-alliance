import { defineConfig } from 'drizzle-kit';

export default defineConfig({
  schema: './src/infrastructure/drizzle/schema/index.ts',
  out: './drizzle',
  dialect: 'postgresql',
  dbCredentials: {
    url: process.env.DATABASE_URL || 'postgresql://city_admin:city_secret_pass_2026@127.0.0.1:5432/city_alliance',
  },
  verbose: true,
  strict: true,
});
