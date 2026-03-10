/**
 * @author jimBoYz Ni ChOy!!!
 */
import { showDialog, session_expired_dialog, showAddUserAccount} from "./custom-modal.js";
import { userAccountHandler } from "./data-handler.js";
import { SecureDB } from "./indexedDB.js";
import { inactivityTime } from "./reset_session.js";
import { decrypt, hashWithDefaultKey } from "./web-crypto.js";

let currentPage = 1;

const tempKey = await SecureDB.readPlain("temp-key");

async function loadData(page) {
    const indexdb = await SecureDB.readSecure("key");
    const key = await decrypt(indexdb.key, tempKey);
    let loadingModal = new bootstrap.Modal(document.getElementById("spinnerLoading"));
    loadingModal.show();

    // Set to lower case because in data-handler.js:293, 294 and 298 for exact searching.
    let search = document.getElementById('search').value.toLowerCase();
    search = search !== "" ? await hashWithDefaultKey(search) || search !== null : "";
    const key_hash = await hashWithDefaultKey(key);
    
    const formData = new URLSearchParams();
    formData.append("page", page);
    formData.append("search", search);
    formData.append("key", key_hash);

    await fetch(`./api/getUserDataApi.php`, {
        method: "POST",
        headers: {"Content-Type": "application/x-www-form-urlencoded"},
        credentials: "include",
        body: formData
    })
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
            
            const firstname = await decrypt(row.firstname, key);
            const lastname = await decrypt(row.lastname, key);
            const username = await decrypt(row.username, key);
            
            tr.innerHTML = `<td>${row.id}</td><td>
                            ${firstname}</td><td>
                            ${lastname}</td><td>
                            ${username}</td><td class="text-center">
                            ${row.device_terminal}</td><td class = "text-center">
                            ${row.ip}</td><td>
                            ${to12HourFormat(row.dateRegistered)}</td>
                            <td class='text-center'>
                                <button class="action-btn text-primary" title="Edit" type="button" id="btn-edit" onclick="editData(${row.id}, '${firstname}', '${lastname}', '${username}')">
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

// Initial load
loadData(currentPage);

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
    if (confirm("Are you sure you want to delete this data?")) {
        
        const parts = rowId.split('-');
        const id = parts[1];
        
        fetch('./api/delete-account-api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            // body: 'id=' + encodeURIComponent(id) + '&table_name=' + encodeURIComponent('users')
            body: new URLSearchParams({
                id,
                table_name: "users"
            }) // The same as above (commented) but using URLSearchParams is "Automatic encoding" Less string concatenation = fewer bugs.
        })
        .then(response => response.json())
        .then(data => {

            if(data.success) {
                row.remove();
            }
            alert(data.message);
            // alert(`ID: ${id} was deleted successfully!`);
            // Optionally refresh or update table without full reload
            // location.reload(); // OR use JS to remove row from DOM
        })
        .catch(error => {
            console.error('Error:', error);
            alert(error);
        });
    }
}

// Update Data
function editData(id, firstname, lastname, username) {

    const form = document.getElementById("signupForm");
    form.reset();
    form.dataset.mode = "edit";
    document.getElementById("btn-add-update").textContent = "Update";
    
    // Master key field use as old password in edit or udpate section. See data-handler.js:269
    let oldPass = document.getElementById("master-key");
    document.getElementById("firstname").value = firstname;
    document.getElementById("lastname").value = lastname;
    const uname = document.getElementById("username");
    uname.value = username;
    uname.setAttribute("readonly", "readonly");
    document.getElementById("label-key").textContent = "Old Password";
    oldPass.type = "password";
    document.getElementById("add-user-account-modal-label").textContent = "Update User Account";
    document.getElementById("label-password").textContent = "New Password";
    // See popup-modal.php:218 and navbar.js:97
    // Leave this empty if you do not want to change your password.
    document.getElementById("icon").title = "Leave this field empty if you do not wish to change your password.";
    
    showAddUserAccount();

    form.onsubmit = async (e) => {
        e.preventDefault()
        let form2 = e.target;

        if(!form2.checkValidity()) {
            e.preventDefault();
            form2.reportValidity();

            return;
        }
        await userAccountHandler(form.dataset.mode);
    }
}

function refreshTable() {
    // Refresh or Realods the entire page

    // Clear field
    document.getElementById('search').value = "";
    
    // Refresh only the table data
    loadData(1);
}

document.getElementById("search").addEventListener("keydown", (event) => {

    // Older browsers use event.keyCode === 13, but event.key is the modern standard.
    if(event.key === "Enter") {
        loadData(1);
    }
})


// SESSION TRACKER
// 10 mins of inactivity
inactivityTime(10 * 60 * 1000, (message)=> {
    session_expired_dialog(message);
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

document.getElementById("btn-refresh").addEventListener('click', refreshTable);
document.getElementById('btn-search').addEventListener('click', ()=>loadData(1));

window.togglePassword = togglePassword;
window.deleteRow = deleteRow;
window.editData = editData;