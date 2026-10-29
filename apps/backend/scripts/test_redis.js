const Redis = require('ioredis');

async function testRedis() {
  console.log('Testing Redis connection on 127.0.0.1:16379 without password...');
  const redis = new Redis({
    host: '127.0.0.1',
    port: 16379,
  });

  try {
    const pong = await redis.ping();
    console.log('Redis Ping Response:', pong);
    await redis.set('test:key', 'hello_city_alliance');
    const val = await redis.get('test:key');
    console.log('Redis SET/GET Value:', val);
    await redis.del('test:key');
    console.log('Redis test passed successfully!');
  } catch (err) {
    console.error('Redis test failed:', err);
  } finally {
    redis.disconnect();
  }
}

testRedis();
