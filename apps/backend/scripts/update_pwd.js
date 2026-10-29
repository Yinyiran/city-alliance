const postgres = require('postgres');
const crypto = require('crypto');

const sql = postgres('postgresql://city_admin:city_secret_pass_2026@127.0.0.1:5432/city_alliance');

function hashPassword(password) {
  return crypto.createHash('sha256').update(password + '_city_alliance_salt').digest('hex');
}

async function updatePassword() {
  const pwdHash = hashPassword('Abc@123456');
  await sql`
    UPDATE users
    SET password_hash = ${pwdHash}
    WHERE username = 'admin'
  `;
  console.log('Password hash updated for admin user! ✨');
  await sql.end();
}

updatePassword().catch(console.error);
