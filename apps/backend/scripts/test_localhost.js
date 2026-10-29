const net = require('net');

const client = net.createConnection({ host: 'localhost', port: 5432 }, () => {
  console.log('CONNECTED TO:', client.remoteAddress, client.remotePort);
  client.end();
});

client.on('error', (err) => {
  console.error('ERROR:', err);
});
