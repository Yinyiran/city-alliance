const net = require('net');

const targets = ['127.0.0.1', 'localhost', '172.28.119.191'];

targets.forEach((host) => {
  const sock = net.createConnection({ host, port: 5432 }, () => {
    console.log(`SUCCESS connected to ${host}:5432`);
    sock.end();
  });
  sock.on('error', (err) => {
    console.log(`FAILED connected to ${host}:5432 - ${err.message}`);
  });
});
