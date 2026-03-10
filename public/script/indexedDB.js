/**
 * @author jimBoYz Ni ChOy!!!
 */

export const SecureDB = {
  async open() {
    return new Promise((resolve, reject) => {
      const request = indexedDB.open("mixedDB", 1);
      request.onupgradeneeded = (e) => {
        const db = e.target.result;
        if (!db.objectStoreNames.contains("plain")) db.createObjectStore("plain", { keyPath: "id" });
        if (!db.objectStoreNames.contains("secure")) db.createObjectStore("secure", { keyPath: "id" });
      };
      request.onsuccess = () => resolve(request.result);
      request.onerror = () => reject(request.error);
    });
  },

  // 📝 Save plain text
  async savePlain(id, text) {
    const db = await this.open();
    const tx = db.transaction("plain", "readwrite");
    tx.objectStore("plain").put({ id, text });
  },

  // 🔐 Save encrypted object
  async saveSecure(id, obj) {
    const db = await this.open();
    const enc = await encryptJSON(obj);
    const tx = db.transaction("secure", "readwrite");
    tx.objectStore("secure").put({ id, data: enc });
  },

  // 📖 Read plain text
  async readPlain(id) {
    const db = await this.open();
    const tx = db.transaction("plain", "readonly");
    const req = tx.objectStore("plain").get(id);
    return new Promise((resolve) => (req.onsuccess = () => resolve(req.result?.text || null)));
  },

  // 🔓 Read encrypted data
  async readSecure(id) {
    const db = await this.open();
    const tx = db.transaction("secure", "readonly");
    const req = tx.objectStore("secure").get(id);
    return new Promise((resolve) => {
      req.onsuccess = async () => {
        resolve(req.result ? await decryptJSON(req.result.data) : null);
      };
    });
  },

  // ❌ Delete plain text entry
  async deletePlain(id) {
    const db = await this.open();
    const tx = db.transaction("plain", "readwrite");
    tx.objectStore("plain").delete(id);
    return tx.complete;
  },

  // 🔒 Delete encrypted entry
  async deleteSecure(id) {
    const db = await this.open();
    const tx = db.transaction("secure", "readwrite");
    tx.objectStore("secure").delete(id);
    return tx.complete;
  }
};

// --- webcrypto-utils.js ---
const _enc = new TextEncoder();
const _dec = new TextDecoder();

function toBase64(buf) {
  return btoa(String.fromCharCode(...new Uint8Array(buf)));
}
function fromBase64(b64) {
  return Uint8Array.from(atob(b64), c => c.charCodeAt(0)).buffer;
}

async function deriveKey(password, salt) {
  const keyMaterial = await crypto.subtle.importKey(
    "raw",
    _enc.encode(password),
    "PBKDF2",
    false,
    ["deriveKey"]
  );
  return crypto.subtle.deriveKey(
    { name: "PBKDF2", salt, iterations: 100000, hash: "SHA-256" },
    keyMaterial,
    { name: "AES-GCM", length: 256 },
    false,
    ["encrypt", "decrypt"]
  );
}

async function encrypt(text, password) {
  const salt = crypto.getRandomValues(new Uint8Array(16));
  const iv = crypto.getRandomValues(new Uint8Array(12));
  const key = await deriveKey(password, salt);
  const data = _enc.encode(text);
  const cipher = await crypto.subtle.encrypt({ name: "AES-GCM", iv }, key, data);
  return [toBase64(salt), toBase64(iv), toBase64(cipher)].join(":");
}

async function decrypt(payload, password) {
  const [saltB64, ivB64, dataB64] = payload.split(":");
  const salt = new Uint8Array(fromBase64(saltB64));
  const iv = new Uint8Array(fromBase64(ivB64));
  const data = fromBase64(dataB64);
  const key = await deriveKey(password, salt);
  const plainBuf = await crypto.subtle.decrypt({ name: "AES-GCM", iv }, key, data);
  return _dec.decode(plainBuf);
}

async function encryptJSON(obj) {
  const DEFAULT_KEY = await SecureDB.readPlain("temp-key") ?? "my-secret-key";
  return encrypt(JSON.stringify(obj), DEFAULT_KEY);
}
async function decryptJSON(payload) {
  const DEFAULT_KEY = await SecureDB.readPlain("temp-key") ?? "my-secret-key";
  const text = await decrypt(payload, DEFAULT_KEY);
  return JSON.parse(text);
}

// Clear All Data
export async function clearAllData() {
  const db = await SecureDB.open();
  const tx1 = db.transaction("plain", "readwrite");
  tx1.objectStore("plain").clear();
  const tx2 = db.transaction("secure", "readwrite");
  tx2.objectStore("secure").clear();
  console.log("🧹 All data cleared");
}

// Example Usage
// await clearAllData();
