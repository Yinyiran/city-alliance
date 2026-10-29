import { drizzle } from 'drizzle-orm/postgres-js';
import postgres from 'postgres';
import * as schema from './schema/index';

export const DRIZZLE_DB = 'DRIZZLE_DB';

export type DrizzleDatabase = ReturnType<typeof drizzle<typeof schema>>;

export const drizzleProvider = {
  provide: DRIZZLE_DB,
  useFactory: () => {
    const connectionString =
      process.env.DATABASE_URL ||
      'postgresql://city_admin:city_secret_pass_2026@127.0.0.1:5432/city_alliance';
    const client = postgres(connectionString, { max: 20 });
    return drizzle(client, { schema });
  },
};
