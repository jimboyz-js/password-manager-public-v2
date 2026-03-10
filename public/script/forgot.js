/**
 * @author jimBoYz Ni ChOy!!!
 */
import {inactivityTime} from './reset_session.js';
import { hashWithDefaultKey } from './web-crypto.js';

document.getElementById("forgotForm").addEventListener("submit", async (e)=> {
    e.preventDefault();

    let form = e.target;

    if(!form.checkValidity()) {
        e.preventDefault();
        form.reportValidity();
        return;
    }

    let loadingModal = new bootstrap.Modal(document.getElementById("graduallyLoadingModal"));
    loadingModal.show();

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

    try {

        const username = await hashWithDefaultKey(document.getElementById('username').value.trim().toLowerCase());
        const new_pass = document.getElementById('new-password').value !== "" ? await hashWithDefaultKey(document.getElementById('new-password').value) : "";
        const confirm = document.getElementById('confirm-password').value !== "" ? await hashWithDefaultKey(document.getElementById('confirm-password').value) : "";

        form.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));
        form.querySelectorAll(".invalid-feedback").forEach(el => el.textContent = "");

        const res = await fetch("./api/change_password_api.php", {
            method: "POST",
            headers: {"Content-Type" : "application/json"},
            credentials: "include",
            body: JSON.stringify({username, password: new_pass, confirm})
        });
        
        const data = await res.json();

        clearInterval(interval);
        bar.style.width = "100%";
        bar.textContent = "100%";

        setTimeout(() => {
            loadingModal.hide();
            if(data.success) {
                window.location.href = "login.php";
                return;
            }
            
            if(data.message === "Password doesn't match!" || data.message === "Username does not exist.") {
                
                for(let field in data.errors) {
                    let input = form.querySelector(`[name="${field}"]`);
                    let errorDiv = document.getElementById(`error-${field}`);
                    if (input && errorDiv) {
                        input.classList.add('is-invalid');
                        errorDiv.textContent = data.errors[field];
                    }
                }
                return;
            }

            alert(data.message);

        }, 500);

    } catch(err) {
        clearInterval(interval);
        loadingModal.hide();
        console.error(err);
        alert(err);
    }
})
// SESSION TIME TRACKER
inactivityTime();