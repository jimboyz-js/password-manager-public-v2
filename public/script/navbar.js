/**
 * @author jimBoYz Ni ChOy!!!
 * @Date updated: Dec. 04, 2025 THU. 3:49 PM
 */
import { getParam, setParam } from "../script/param.js";
import { addEntryData, userAccountHandler } from "./data-handler.js";
import { showAddEntry, showAddUserAccount} from "./custom-modal.js";
import { SecureDB } from "./indexedDB.js";

function menu() {

    if (getParam("page") === "dashboard") {
        return "Admin";
    }

    return "Dashboard"
}

function getTitlePageNavAttrb() {
    // Current page [dashboard]
    if (getParam("page") === "dashboard") {
        return "Go to admin dashboard"
    }
    
    return "Go to dashboard";
}

function menuLinkOption() {
    const param = getParam("page");
    // Preferred: Done by dashboard.php and admin.php
    // getCurrentUrl({page: param});
    if (param === "admin") {
        return `dashboard.php?${setParam("page", "dashboard")}`;
    }

    // setId(param);
    return `admin.php?${setParam("page", "admin")}`;
}

function setId(page) {
    const add_entry = document.querySelector('.add-entry');
    if (page === "dashboard") {
        return add_entry.id = "add-account";
    }

    return add_entry.id = "add-user-account";
}

function getId() {
    return document.querySelector(".add-entry").id;
}

const menuOption = document.getElementById("page-link");
menuOption.textContent = menu();
menuOption.setAttribute("href", menuLinkOption());
menuOption.title = getTitlePageNavAttrb();

document.getElementById("add-user-account").addEventListener("click", add);

// Add Data Entry
function add() {
    if(getParam("page") === "dashboard") {
        
        const form = document.getElementById("add-user-form");
        form.reset();
        form.dataset.mode = "add";
        document.getElementById("add-user-modal-label").textContent = "Add Entry";
        document.getElementById("btn-add-update").textContent = "Add";
        // const el = document.querySelector(".con");
        // el.style.display = "none";

        // Checkbox to show or hide password in Add or Update Modal
        // Instead of declaring outside from function, it is recommended to declare here for resetting the checkbox UI.
        // See dashboard.js
        const inputPass = document.getElementById("password");
        const showPass = document.getElementById("show-password");
        inputPass.type = showPass.checked ? "text" : "password";

        showPass.onchange = function() {
            inputPass.type = this.checked ? 'text' : 'password';
        }
        
        showAddEntry();
        addEntryData();

    } else if(getParam("page") === "admin") {
        const form = document.getElementById("signupForm");
        // Clear
        // Clear this line instead calling this lines of code from: userAccountHandler(form.dataset.mode); and pass the form as arguments. Before, I did this but I remove.
        form.querySelectorAll(".is-invalid").forEach(element => {
            element.classList.remove("is-invalid");
        });
        form.querySelectorAll(".invalid-feedback").forEach(element => {
            element.textContent = "";
        });
        form.reset();
        form.dataset.mode = "add";
        document.getElementById("btn-add-update").textContent = "Add";
        document.getElementById("add-user-account-modal-label").textContent = "Add User Account";
        const key = document.getElementById("master-key");
        document.getElementById("label-key").textContent = "Key";
        key.type = "text";
        document.getElementById("label-password").textContent = "Password";
        document.getElementById("username").removeAttribute("readonly");
        // See popup-modal.php:218 and admin.js:165
        // If you leave this field empty the login key will use to encrypt this data.
        document.getElementById("icon").title = "If you leave this field empty, the login key will be used to encrypt this data.";
        
        showAddUserAccount();
        form.onsubmit = async e=> {
            e.preventDefault();

            let form2 = e.target;
            if(!form2.checkValidity()) {
                e.preventDefault();
                form2.reportValidity();
                return;
            }

            await userAccountHandler(form.dataset.mode);
        }

    } else {
        alert("Something went wrong. Please try again.");
        return;
    }
}

// Logout
document.getElementById("logout").addEventListener("click", async ()=> {
    
    let loadingModal = new bootstrap.Modal(document.getElementById("loadingModal"));
    loadingModal.show();

    const res = await fetch("./api/logout-api.php", {
        method: "POST",
    });

    const data = await res.json();
    loadingModal.hide();
    if(data.success) {
        await SecureDB.deleteSecure('key');
        await SecureDB.deletePlain('temp-key');
        window.location.href = "login.php";
    } else {
        alert(data.message);
    }
});