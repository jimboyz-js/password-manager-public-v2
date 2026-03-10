/**
 * @author jimBoYz Ni ChOy!!!
 * 11-11-2025 TUE. 12:40 PM
 */

import { SecureDB } from "./indexedDB.js";
import { encrypt, hashWithDefaultKey } from "./web-crypto.js";
import { generateRandomString, generateSecureRandomString } from './random-string-gen.js';
import { getCurrentUrl } from './active-url-config.js';
import { setParam } from "./param.js";

document.getElementById("loginForm").addEventListener("submit", async (e)=> {
    e.preventDefault();

    let form = e.target;

    if(!form.checkValidity()) {
        e.preventDefault();
        form.reportValidity();
        return;
    }

    // Close the error message upon submitting...
    document.querySelector("#errorMsg").style.display = "none";

    const uname = document.getElementById("username").value;
    const username = await hashWithDefaultKey(uname);
    const password = await hashWithDefaultKey(document.getElementById("password").value);
    const master_key = document.getElementById("master-key").value;
    
    const formData = new FormData();
    formData.append("username", username);
    formData.append("password", password);
    formData.append("master-key", master_key !== "" ? await hashWithDefaultKey(master_key) : "");

    const temp_pass = generateSecureRandomString(17);
    const mkey = master_key !== "" ? await encrypt(master_key, temp_pass) : ""; // Double encrypt: see encryptJSON in indexedDB.js
    // Save credentials
    await SecureDB.savePlain('temp-key', temp_pass);
    await SecureDB.saveSecure("key", {
        name: "JS-777",
        username: uname,
        key: mkey
    });
    
    let loadingModal = new bootstrap.Modal(document.getElementById("loadingModal"));
    loadingModal.show();

    try {
        const res = await fetch("api/login_api.php", {
            method: "POST",
            body: formData
        });

        loadingModal.hide();
        const data = await res.json();
        
        if(data.success) {

            // For future use
            const k_param = generateRandomString(17);

            // Store the URL into the session (back-end)
            await getCurrentUrl({
                status: data.key_status,
                id: data.id,
                k: k_param,
                m_id: data.m_id
            });
            window.location.href = `auth-page.php?${setParam("id", data.id), setParam("m_id", data.m_id), setParam("k", encodeURIComponent(k_param))}`;

            return;
        }

        if(data.message === "Wrong username or password" || data.message === "Wrong username or key" || data.message === "Wrong password" || data.message === "Invalid master key") {
            document.querySelector("#errorMsg").style.display = "flex";
            document.getElementById("feedback").textContent = data.message;

            return;
        }

        alert(data.message);

    } catch (err) {
        loadingModal.hide();
        console.error(err);
        alert(err);
    }
})