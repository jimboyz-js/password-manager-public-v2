/**
 * @author jimBoYz Ni ChOy!!!
 */
export function generateRandomString(length, isInclude=false) {
    let chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

    if(isInclude) {
        chars += "!@#$%^&*()_+[]{}<>?,./";
    }

    let result = "";
    for(let i = 0; i < length; i++) {
        result += chars.charAt(Math.floor(Math.random() * chars.length));
    }

    return result;
}

export function generateSecureRandomString(length) {
    const uppercase = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    const lowsercase = "abcdefghijklmnopqrstuvwxyz";
    const numbers = "0123456789";
    const symbols = "!@#$%^&*()_+[]{}<>?,./";

    // const chars = uppercase + lowsercase + numbers + symbols;
    const chars = uppercase + lowsercase + numbers;
    const array = new Uint32Array(length);
    window.crypto.getRandomValues(array);

    let result = "";
    for(let i = 0; i < length; i++) {
        result += chars[array[i] % chars.length];
    }

    return result;
}