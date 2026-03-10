/**
 * @author jimBoYz Ni ChOy!!!
 */

// Web Crypto utilities for browser

const _enc = new TextEncoder();
const _dec = new TextDecoder();

// -- helpers: base64
function toBase64(arrayBuffer) {
  const bytes = new Uint8Array(arrayBuffer);
  let binary = '';
  for (let i = 0; i < bytes.byteLength; i++) {
    binary += String.fromCharCode(bytes[i]);
  }
  return btoa(binary);
}
function fromBase64(b64) {
  const binary = atob(b64);
  const len = binary.length;
  const bytes = new Uint8Array(len);
  for (let i = 0; i < len; i++) bytes[i] = binary.charCodeAt(i);
  return bytes.buffer;
}

// -- derive AES-GCM key from password using PBKDF2
async function deriveKey(password, salt, iterations = 100000, hash = 'SHA-256') {
  // password: string
  // salt: ArrayBuffer (Uint8Array preferred)
  const passKey = await crypto.subtle.importKey(
    'raw',
    _enc.encode(password),
    'PBKDF2',
    false,
    ['deriveKey']
  );

  const key = await crypto.subtle.deriveKey(
    {
      name: 'PBKDF2',
      salt: salt,
      iterations: iterations,
      hash: hash
    },
    passKey,
    { name: 'AES-GCM', length: 256 },
    false,
    ['encrypt', 'decrypt']
  );

  return key; // CryptoKey for AES-GCM
}

// -- encrypt plaintext with password (returns base64 string containing salt:iv:cipher)
export async function encrypt(plaintext, password) {
  // generate random 16-byte salt and 12-byte iv
  const salt = crypto.getRandomValues(new Uint8Array(16));
  const iv = crypto.getRandomValues(new Uint8Array(12)); // 96-bit IV recommended for AES-GCM

  const key = await deriveKey(password, salt.buffer);

  const ciphertext = await crypto.subtle.encrypt(
    {
      name: 'AES-GCM',
      iv: iv
    },
    key,
    _enc.encode(plaintext)
  );

  // encode parts as base64 and join with colon
  const payload = [
    toBase64(salt.buffer),
    toBase64(iv.buffer),
    toBase64(ciphertext)
  ].join(':');

  return payload;
}

// -- decrypt payload produced by encrypt()
export async function decrypt(payloadBase64, password) {
  // payload format: base64(salt):base64(iv):base64(ciphertext)
  const parts = payloadBase64.split(':');
  if (parts.length !== 3) throw new Error('Invalid payload format');

  const salt = fromBase64(parts[0]);
  const iv = fromBase64(parts[1]);
  const ciphertext = fromBase64(parts[2]);

  const key = await deriveKey(password, salt);

  const plainBuffer = await crypto.subtle.decrypt(
    {
      name: 'AES-GCM',
      iv: new Uint8Array(iv)
    },
    key,
    ciphertext
  );

  return _dec.decode(plainBuffer);
}

// -- compute SHA-256 hash of a string, return base64
async function sha256Base64(message) {
  const digest = await crypto.subtle.digest('SHA-256', _enc.encode(message));
  return toBase64(digest);
}

// Exported convenience wrapper for the specific passphrase "my-secret-key"
const DEFAULT_PASSPHRASE = 'my-secret-key';
export async function encryptWithDefaultKey(plaintext) {
  return encrypt(plaintext, DEFAULT_PASSPHRASE);
}
export async function decryptWithDefaultKey(payload) {
  return decrypt(payload, DEFAULT_PASSPHRASE);
}
export async function hashWithDefaultKey(message) {
  // hashing does not use the passphrase, but provided for convenience
  return sha256Base64(message);
}

function hexFromBytes(bytes) {
    return Array.from(new Uint8Array(bytes)).map(b => b.toString(16).padStart(2, '0')).join('');
}

/* ---------- Owner hash (public id) ---------- */
export async function ownerHash(username) {
    const enc = new TextEncoder();
    const h = await crypto.subtle.digest('SHA-256', enc.encode(username.toLowerCase()));
    return hexFromBytes(h);
}