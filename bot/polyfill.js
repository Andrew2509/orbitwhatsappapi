import { webcrypto } from 'node:crypto';

if (typeof globalThis.crypto === 'undefined' || !globalThis.crypto.subtle) {
    globalThis.crypto = webcrypto;
    console.log('Web Crypto API polyfilled.');
}
