/**
 * @author jimBoYz Ni ChOy!!!
 */

// Custom Modal
const modalEl = document.getElementById('customAlertModal');
const modal = new bootstrap.Modal(modalEl);
const titleEl = document.getElementById('customModalTitle');
const bodyEl = document.getElementById('customModalBody');
const headerEl = document.getElementById('customModalHeader');
const footerEl = document.getElementById('customModalFooter');

// Session Modal
const bodyMsg = document.getElementById("s-desc-msg");

export function showDialog(type, message) {
    // Reset header + footer
    headerEl.className = "modal-header";
    footerEl.innerHTML = "";

    // Style + Title by type
    switch(type) {
        case 'info':
            headerEl.classList.add("bg-info","text-white");
            titleEl.textContent = "Information";
            break;
        case 'warning':
            headerEl.classList.add("bg-warning","text-dark");
            titleEl.textContent = "Warning";
            break;
        case 'error':
            headerEl.classList.add("bg-danger","text-white");
            titleEl.textContent = "Error";
            break;
        default:
            titleEl.textContent = "Message";
    }

    bodyEl.textContent = message;

    // Add OK button
    let okBtn = document.createElement("button");
    okBtn.className = "btn btn-primary";
    okBtn.textContent = "OK";
    okBtn.setAttribute("data-bs-dismiss","modal");
    footerEl.appendChild(okBtn);

    modal.show();
}

export function confirmDialog(message, callback) {
    headerEl.className = "modal-header bg-secondary text-white";
    titleEl.textContent = "Confirmation";
    bodyEl.textContent = message;
    footerEl.innerHTML = "";

    let cancelBtn = document.createElement("button");
    cancelBtn.className = "btn btn-secondary";
    cancelBtn.textContent = "Cancel";
    cancelBtn.setAttribute("data-bs-dismiss","modal");
    cancelBtn.onclick = () => callback(false);

    let okBtn = document.createElement("button");
    okBtn.className = "btn btn-success";
    okBtn.textContent = "OK";
    okBtn.setAttribute("data-bs-dismiss","modal");
    okBtn.onclick = () => callback(true);

    footerEl.appendChild(cancelBtn);
    footerEl.appendChild(okBtn);

    modal.show();
}

export function session_expired_dialog(message) {
    // Update: (12-16-2025 TUE. 5:27 PM) Remove bootstrap session modal.
    // document.body.classList.add("js");
    // let session_modal = new bootstrap.Modal(document.getElementById("session-modal-js"));
    // session_modal.show();
    
    // bodyMsg.textContent = message;

    // document.getElementById("s-btn-ok").addEventListener("click", ()=> {
    //     session_modal.hide();
    //     document.body.classList.remove("js");
    //     window.location.href = "login.php";
    // })

    const modal = document.getElementById("sessionExpiredModalCustom");
    const msg = document.getElementById("sessionExpiredMessage");
    const okBtn = document.getElementById("sessionExpiredOkBtn");

    msg.textContent = message;
    modal.style.display = "flex"; // Show modal

    okBtn.onclick = () => {
        modal.style.display = "none"; // Hide modal
        window.location.href = "login.php"; // Redirect to login
    };
}

let show_entry = null;
export function showAddEntry() {
    show_entry = new bootstrap.Modal(document.getElementById("add-user-modal"));
    show_entry.show();
}

export function hideAddEntry() {
    if (show_entry) {
        show_entry.hide();
    }
}

let show_update_entry = null;
export function showUpdateEntry() {
    show_update_entry = new bootstrap.Modal(document.getElementById("update-user-modal"));
    show_update_entry.show();
}

export function hideUpdateEntry() {
    if(show_update_entry) {
        show_update_entry.hide();
    }
}

let modalUserAccount = null;
export function showAddUserAccount() {
    modalUserAccount = new bootstrap.Modal(document.getElementById("add-user-account-modal"));
    modalUserAccount.show();
}

export function hideAddUserAccount() {
    if(modalUserAccount) {
        modalUserAccount.hide();
    }
}