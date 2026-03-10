/**
 * @author jimBoYz Ni ChOy!!!
 */
import { addEntryData } from "./data-handler.js";
import { showDialog, confirmDialog, session_expired_dialog, showAddEntry } from "./custom-modal.js";
import { SecureDB } from "./indexedDB.js";
import { getParam } from "./param.js";
import { saveKeyFromFetchUrl, setKey } from "./set-master-key.js";
import { decrypt, encrypt, hashWithDefaultKey } from "./web-crypto.js";
import {inactivityTime} from './reset_session.js';

let currentPage = 1;

const indexdb = await SecureDB.readSecure("key");
const tempKey = await SecureDB.readPlain("temp-key");

if(!indexdb.key) {
    let key;
    let res;
    do {
        key = setKey();
        const key_hash = await hashWithDefaultKey(key);
        const encrypted_key = await encrypt(key, tempKey);
        const paramId = getParam("id");
        res = await saveKeyFromFetchUrl(key_hash, paramId, encrypted_key, indexdb);
    } while (!key || !key.trim() || !res);
    
}

async function loadData(page) {
    
    let loadingModal = new bootstrap.Modal(document.getElementById("spinnerLoading"));
    loadingModal.show();
    
    let search = document.getElementById('search').value.toLowerCase();// Set to lower case because in data-handler.js:43 and 50 for exact searching.
    search = search !== "" ? await hashWithDefaultKey(search) || search !== null : "";
    const key = await decrypt(indexdb.key, tempKey);
    
    await fetch(`./api/getDataApi.php?page=${page}&search=${encodeURIComponent(search)}`)
    .then(response => response.json())
    .then(data => {

        loadingModal.hide();

        if(!data.success) {
            alert(data.message);
            window.location.href = "login.php";
            return;
        }

        const tbody = document.querySelector('#table tbody');
        tbody.innerHTML = '';
        
        data.data.forEach(async row => {
            const tr = document.createElement('tr');
            tr.id = `row-${row.id}`;
            
            const username = await decrypt(row.username, key);
            const pass = await decrypt(row.password, key);
            const title = await decrypt(row.account_for, key);
            const my_note = await decrypt(row.note, key);
            const added_by = await decrypt(row.addedBy, key);
            
            tr.innerHTML = `<td>${row.id}</td><td>
                            ${username}</td><td>
                            <input type = "password" id = "pass-${row.id}" value="${pass}" readonly class = "form-control"/></td><td>
                            ${my_note}</td><td>
                            ${title}</td><td>
                            ${added_by}</td><td class = "text-center">
                            ${to12HourFormat(row.dateAdded)}</td>
                            <td class='text-center'>
                                <button class="action-btn" title="Show" type="button" onclick="togglePassword('pass-${row.id}', this)">
                                    <i class="fa fa-eye"></i>
                                </button>
                                <button class="action-btn text-primary" title="Edit" type="button" id="btn-edit" onclick="editData(${row.id}, '${username}', '${pass}', '${title}', '${my_note}')">
                                    <i class="fa-solid fa-pencil"></i>
                                </button>
                                <button class="action-btn text-danger" title="Delete" type="button" onclick="deleteRow('row-${row.id}')">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>`;
                            
            tbody.appendChild(tr);
            
        });

        if(data.data.length === 0) {
            const tr = document.createElement('tr');
            tr.innerHTML = `<td colspan="8" class="text-center">No results found</td>`;
            tbody.appendChild(tr);
        }

        // Pagination buttons
        const pagination = document.getElementById('pagination');
        pagination.innerHTML = '';
        for (let i = 1; i <= data.total_pages; i++) {
            const btn = document.createElement('button');
            btn.id = "btn-pagination";
            btn.className = "btn btn-secondary rounded-circle";
            btn.textContent = i;
            if (i === page) btn.disabled = true;
            btn.onclick = () => {
                currentPage = i;
                loadData(i);
            };
            pagination.appendChild(btn);
        }

    })
    .catch(error => {
        console.error('Error:', error);
        loadingModal.hide();
        showDialog("error", "Internal server error. Check the console for more info.");
    });
}

// Check if master key is valid
// Load only if master key is valid
if(indexdb.key) {
    // Initial load
    loadData(currentPage);
}

function togglePassword(inputId, btn) {
    const input = document.getElementById(inputId);
    const icon = btn.querySelector("i");

    if (input.type === "password") {
        input.type = "text";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
    } else {
        input.type = "password";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
    }
}

function deleteRow(rowId) {
    const row = document.getElementById(rowId);

    confirmDialog("Are you sure you want to delete this data?", (ok)=> {
        const parts = rowId.split('-');
        const id = parts[1];
        
        if(ok) {
            fetch('./api/delete-account-api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'id=' + encodeURIComponent(id) + '&table_name=' + encodeURIComponent('accounts')
            })
            .then(response => response.json())
            .then(data => {

                if(data.success) {
                    row.remove();
                }
                showDialog("info", data.message);
            })
            .catch(error => {
                console.error('Error:', error);
                alert(error);
            });
        }
    })
}

// Update Data
function editData(id, username, password, title, note) {

    let form = document.getElementById("add-user-form");
    form.reset();
    form.dataset.mode = "edit";
    form.dataset.accountId = id; //safe, modern and fast than setAttribute(...) HTML5
    document.getElementById("add-user-modal-label").textContent = "Update Entry";
    document.getElementById("btn-add-update").textContent = "Update";

    document.getElementById("username").value = username;
    const inputPass = document.getElementById("password");
    inputPass.value = password;
    // document.getElementById("confirm").value = confirm;
    document.getElementById("account_for").value = title;
    document.getElementById("note").value = note;
    
    // Checkbox to show or hide password in Add or Update Modal
    // Instead of declaring outside from function, it is recommended to declare here for resetting the checkbox UI.
    // See navbar.js
    const showPass = document.getElementById("show-password");
    inputPass.type = showPass.checked ? "text" : "password";

    showPass.onchange = function() {
        inputPass.type = this.checked ? 'text' : 'password';
    }
    
    // showUpdateEntry();
    showAddEntry();
    addEntryData();
}

function refreshTable() {
    // It refresh and reloads the page
    // Clear field
    document.getElementById('search').value = "";
    // Refresh Table Only
    if(indexdb.key) {
        loadData(currentPage);
    } else {
        window.location.reload();
    }
}

document.getElementById("search").addEventListener("keydown", (event) => {

    // Older browsers use event.keyCode === 13, but event.key is the modern standard.
    if(event.key === "Enter") {
        loadData(1);
    }
})

function to12HourFormat(mysqlDateTime) {
    // Convert MySQL DATETIME (string) to Date object
    const date = new Date(mysqlDateTime);

    // Options for 12-hour format with AM/PM
    return date.toLocaleString('en-US', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: 'numeric',
        minute: '2-digit',
        second: '2-digit',
        hour12: true
    });
}

// SESSION TRACKER
// 10 mins of inactivity time
inactivityTime(10 * 60 * 1000, (message)=> {
    session_expired_dialog(message);
})

document.getElementById("btn-refresh").addEventListener('click', refreshTable);
document.getElementById('btn-search').addEventListener('click', ()=>loadData(1));

window.togglePassword = togglePassword;
window.deleteRow = deleteRow;
window.editData = editData;