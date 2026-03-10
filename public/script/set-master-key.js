/**
 * @author jimBoYz Ni ChOy!!!
 */
import { SecureDB } from "./indexedDB.js";
import { getParam } from "./param.js";
import { encrypt, hashWithDefaultKey } from "./web-crypto.js";

document.addEventListener("keydown", async (e)=> {
    if(e.key === "F7") {
        e.preventDefault();

        try {
            const indexdb = await SecureDB.readSecure("key");
            const tempKey = await SecureDB.readPlain("temp-key");
            const key = setKey();

            const encypted_key = await encrypt(key, tempKey);
            const key_hash = await hashWithDefaultKey(key);
            const paramId = getParam("id");

            saveKeyFromFetchUrl(key_hash, paramId, encypted_key, indexdb);

        } catch (err) {
            console.error(err);
            alert(err);
        }
        
    }
})

// Add user key
document.addEventListener("keydown", async (e)=> {
    if(e.key === "F9") {
        e.preventDefault();

        try {
            const key = setKey();
            const key_hash = await hashWithDefaultKey(key);
            const paramId = getParam("m_id");

            if(key === "") {
                return;
            }

            if(!key) {
                return;
            }

            const res = await fetch("./api/master_key_conf_api.php", {
                method: "POST",
                headers: {"Content-Type": "application/json"},
                credentials: "include",
                body: JSON.stringify({key: key_hash, id: paramId})
            })

            const data = await res.json();

            if(data.success) {
                window.location.reload();
            }

            alert(data.message);

        } catch (err) {
            console.error(err);
            alert(err);
            // return;
        }
        
    }
})

export function setKey() {

    // Enter master key to switch custom key
    const key = prompt("Enter your master key");
    try {

        if(key === "") {
            alert("Invalid key");
            return null;
        }

        if(!key) {
            return null;
        }

        return key;

    } catch(err) {
        console.error(err);
        alert(err);
        return null;
    }   
}

export const saveKeyFromFetchUrl = async (key_hash, paramId, encrypted_key, indexdb={}, url="./api/get_master_key_api.php") => {

    const username_hash = await hashWithDefaultKey(indexdb.username);
    const res = await fetch(url, {
        method: "POST",
        credentials: "include",
        headers: {"Content-Type": "application/json"},
        body: JSON.stringify({key: key_hash, id: paramId, username_hash})
    })

    const data = await res.json();

    if(data.success) {
        await SecureDB.saveSecure("key", {
            name: indexdb.name,
            username: indexdb.username,
            key: encrypted_key
        })
        window.location.reload();
    } else {
        alert(data.message);
    }

    return data.success;
}