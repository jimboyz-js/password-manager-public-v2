/**
 * @author jimBoYz Ni ChOy!!!
 * This script handles adding and updating data
 * Nov. 17, 2025 7:15 PM Mon. this date only the file itself is change from add-data.js to data-handler.js
 */
import { hideAddEntry, hideAddUserAccount, showDialog } from "./custom-modal.js";
import { SecureDB } from "./indexedDB.js";
import { decrypt, encrypt, hashWithDefaultKey } from "./web-crypto.js";

/**
 * This function handle add and edit (update) data in the dashboard section (account entries)
 * Nov. 20, 2025 THU. 8:56PM
 */
export function addEntryData() {
    
    const form_data = document.getElementById("add-user-form");
    form_data.onsubmit = async function(e) {
        e.preventDefault();

        let form = e.target;

        if (!form.checkValidity()) {
            e.preventDefault();
            form.reportValidity();
            return;
        }

        const tempKey = await SecureDB.readPlain("temp-key");
        const indexdb = await SecureDB.readSecure("key");
        const key = await decrypt(indexdb.key, tempKey); // Decrypt the key because it was encrypted upon saving into indexDB unlike version 2.2-unfinish. See, login.js:34 and indexedDB.js:118
        
        // Bootstrap
        let loadingModal = new bootstrap.Modal(document.getElementById("loadingModal"));
        loadingModal.show();

        form.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));
        form.querySelectorAll(".invalid-feedback").forEach(el => el.textContent = "");

        const pass = document.getElementById("password").value;
        const account_for = document.getElementById("account_for").value.trim() || 'Untitled';

        const username = await encrypt(document.getElementById("username").value.trim(), key);
        const username_hash = await hashWithDefaultKey(document.getElementById("username").value.trim().toLowerCase()); // Hashing for searching username with lower case. Because with hash cannot perform wild cards in the back-end, that is why it is recommended to set it to lower case.
        const password = await encrypt(pass, key);
        const password_hash = await hashWithDefaultKey(pass); // Use for matching confirm to avoid sending plaintext to the server.
        // const confirm = await hashWithDefaultKey(document.getElementById("confirm").value);
        const title = await encrypt(account_for, key);
        // Use for searching data as a hash text. It is useful to avoid sending plain text to the server.
        // It is recommended to to set the title to lower case before it hash because it is easy to search using hashed text. Same text with case sensitive is different hash result.
        const title_hash = await hashWithDefaultKey(account_for.toLowerCase());
        const notes = await encrypt(document.getElementById("note").value, key);
        const addedBy = await encrypt(indexdb.username, key);
        
        const mode = form_data.dataset.mode;
        let id = form_data.dataset.accountId;

        if (mode === "add") {
            const res = await fetch("./api/save-entry-api.php", {
                method: "POST",
                credentials: "include",
                body: JSON.stringify({username, username_hash, password, password_hash, title, title_hash, added_by: addedBy, note: notes})
            });
            
            const data = await res.json();

            loadingModal.hide();

            if(data.success) {
                clearFields();
                showDialog("info", data.message);
                return;
            }

            showDialog("error", data.message);
       
        } else if (mode === "edit") {
            
            const res = await fetch("./api/update_entry_api.php?", {
                method: "POST",
                credentials: "include",
                body: JSON.stringify({username, username_hash, password, password_hash, title, title_hash, update_by: addedBy, note: notes, id, page: "dashboard"})
            });
            
            const data = await res.json();

            loadingModal.hide();

            if(data.success) {
                showDialog("info", data.message);
                hideAddEntry();
                return;
            }

            showDialog("error", data.message);
        } else {
            showDialog("error", "Something went wrong!");
        }
    }

}

/**
 * This function handle add and edit (update) data in the admin section (account entries)
 * Nov. 20, 2025 THU. 9:53PM
 */
export async function userAccountHandler(mode, isSignUp=false) {

    const tempKey = await SecureDB.readPlain("temp-key");
    const indexdb = await SecureDB.readSecure("key");
    // Use this for sign-up to avoid error in decryption if indexdb.key is empty (use fallback dummy).
    const loginKey = isSignUp ? "js-777" : await decrypt(indexdb.key, tempKey); // Decrypt the key because it was encrypted upon saving into indexDB unlike version 2.2-unfinish. See, login.js:34 and indexedDB.js:118
   
    let gradyllyProgressBar = await new bootstrap.Modal(document.getElementById("graduallyLoadingModal"));
    gradyllyProgressBar.show();

    let bar = document.getElementById("loadingBar");
    // reset before starting
    bar.style.width = "0%";
    bar.textContent = "0%";
    let progress = 0;
    let interval = setInterval(() => {
        if (progress < 90) { // stop at 90%, wait for fetch
            progress += 10;
            bar.style.width = progress + "%";
            bar.textContent = progress + "%";
        }
    }, 400);

    const fname = document.getElementById("firstname").value;
    const lname = document.getElementById("lastname").value;
    const uname = document.getElementById("username").value;
    const pass = document.getElementById("password").value;
    const confirm = document.getElementById("confirm-password").value;
    let key = document.getElementById("master-key").value.trim();// This field use as old password in edit section.

    /**
     * In the front-end there is an option whether the user create new account with the same or custom key.
     * If the the key is empty then the login key will use to encrypt the data.
     */
    key = key !== "" ? key : loginKey;
    
    // If "edit" (update) then the loginKey from indexDB will use to encrypt the data. This happen because I intentionally reuse the UI (add and update).
    let oldPassDesc = false;
    if(mode === "edit") {
        oldPassDesc = document.getElementById("master-key").value ? true : false;
        key = loginKey;
    }
    
    const firstname_hash = await hashWithDefaultKey(fname.trim().toLowerCase()); // Set to lower case for better performance search.
    const lastname_hash = await hashWithDefaultKey(lname.trim().toLowerCase());
    const firstname = await encrypt(fname, key);
    const lastname = await encrypt(lname, key);
    const username = await encrypt(uname, key);
    const username_hash = await hashWithDefaultKey(uname.trim().toLowerCase());// Hashing for searching/update(forgot.js) username with lower case. Because with hash cannot perform wild cards in the back-end, that is why it is recommended to set it to lower case.
    const password = pass !== "" ? await hashWithDefaultKey(pass) : "";
    const confirm_pass = await hashWithDefaultKey(confirm);
    const key_hash = await hashWithDefaultKey(key);
    const old_pass = await hashWithDefaultKey(document.getElementById("master-key").value.trim());

    let url;
    let obj;
    let isPass = pass || oldPassDesc ? true : false; // Use this in the back-end to verify the query whether password is include to update or not. 

    if(mode === "add") {
        obj = {firstname, lastname, username, firstname_hash, lastname_hash, username_hash, password, confirm: confirm_pass, key_hash}
        url = "./api/register-api.php";
    } else if(mode === "edit") {
        obj = {
            firstname,
            lastname,
            firstname_hash,
            lastname_hash,
            password,
            username_hash,
            page: "admin",
            isPass,
            confirm: confirm_pass,
            old_password: old_pass
        }
        url = "./api/update_entry_api.php";
    }

    await fetch(url, {
        method: "POST",
        credentials: "include",
        body: JSON.stringify(obj)
    })
    .then(res => res.json())
    .then(data => {
        clearInterval(interval);
        bar.style.width = "100%";
        bar.textContent = "100%";

        setTimeout(() => {
            gradyllyProgressBar.hide();

            // Clear the input fields
            if(data.status === "success") {
                clearFields();
                hideAddUserAccount();
            }

            if(data.errors) {
                for(let field in data.errors) {
                    let input = document.querySelector(`[name="${field}"]`);
                    const errorDiv = document.getElementById(`error-${field}`);
                    if(input && errorDiv) {
                        input.classList.add('is-invalid');
                        errorDiv.textContent = data.errors[field];
                    }
                }
                return;
            }

            alert(data.message);
            // showDialog("info", data.message);

        }, 500); // short delay so user sees 100%
    })
    .catch(err => {
        alert(err);
        clearInterval(interval);
        gradyllyProgressBar.hide();
    })
}

function clearFields() {

    if(document.getElementById("username")) {
        document.getElementById("username").value = "";
    }

    if(document.getElementById("password")) {
        document.getElementById("password").value = "";
    }

    if(document.getElementById("confirm")) {
        document.getElementById("confirm").value = "";
    }

    if(document.getElementById("account_for")) {
        document.getElementById("account_for").value = "";
    }

    if(document.getElementById("note")) {
        document.getElementById("note").value = "";
    }

    if(document.getElementById("firstname")) {
        document.getElementById("firstname").value = "";
    }

    if(document.getElementById("lastname")) {
        document.getElementById("lastname").value = "";
    }

    if(document.getElementById("confirm-password")) {
        document.getElementById("confirm-password").value = "";
    }
}
