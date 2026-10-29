const Redis = require('ioredis');

async function testRedis() {
  console.log('Testing Redis connection on 127.0.0.1:6379 with password...');
  const redis = new Redis({
    host: '127.0.0.1',
    port: 6379,
    password: 'city_redis_secret_2026',
  });

  try {
    const pong = await redis.ping();
    console.log('Redis Ping Response:', pong);
    await redis.set('test:key', 'hello_city_alliance_redis_6379');
    const val = await redis.get('test:key');
    console.log('Redis SET/GET Value:', val);
    await redis.del('test:key');
    console.log('Redis 6379 test PASSED SUCCESSFULLY! ✨');
  } catch (err) {
    console.error('Redis test failed:', err);
    process.exit(1);
  } finally {
    redis.disconnect();
  }
}

testRedis();
