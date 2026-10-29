const net = require('net');

const client = net.createConnection({ host: '172.28.119.191', port: 5432 }, () => {
  console.log('CONNECTED TO 172.28.119.191:5432 SUCCESS!');
  client.end();
});

client.on('error', (err) => {
  console.error('ERROR connecting to 172.28.119.191:5432:', err);
});
