/**
 * @author jimBoYz Ni ChOy!!!
 */

import { setParam } from './param.js';
import { inactivityTime } from './reset_session.js';

fetch('./api/api_auth_check_session.php', {credentials: 'include' })
.then(res => res.json())
.then(data => {
    if (!data.authenticated) {
        window.location.href = 'login.php';
        return;
    } 
    document.getElementById('email').textContent = data.email;
    document.getElementById('expire_time').textContent = data.expire_time;

    resendCode(data.resend_timer, data.resend_available);
    
})
.catch(err => {
    console.error(err);
    window.location.href = 'login.php';
});

document.getElementById("authForm").addEventListener("submit", async (e)=> {
    e.preventDefault();

    let form = e.target;

    if(!form.checkValidity()) {
        e.preventDefault();
        form.reportValidity();
        return;
    }

    const formData = new FormData(form);

    // Clear
    form.querySelectorAll(".is-invalid").forEach(element => {
        element.classList.remove("is-invalid");
    });
    form.querySelectorAll(".invalid-feedback").forEach(element => {
        element.textContent = "";
    });

    await fetch('./api/auth_api.php', {
        method: "POST",
        credentials: "include",
        body: formData
    })
    .then(res => res.json())
    .then( async data => {
        
        if(data.success) {
            if(data.page_view === "dashboard_login") {
                window.location.href = `dashboard.php?${setParam("page", "dashboard")}`;

            } else if (data.page_view === "change_password") {
                window.location.href = "forgot.php";

            } else if(data.page_view === "create_account") {
                window.location.href = "sign-up.php";

            } else {
                alert("Something went wrong! Try to refresh the page.");
                location.href = "login.php";
            }

        } else {
            if(data.message === "No code to verify.") {
                alert(data.message);
            } else {
                let input = document.querySelector(`[name="verification-code"]`);
                let errorDiv = document.getElementById("error-code");
                if(input && errorDiv) {
                    input.classList.add("is-invalid");
                    errorDiv.textContent = data.message;
                }
            }
        }
    })
    .catch(err => {
        console.error(err);
        alert("Server error. Please try again later.");
    })


})

function resendCode(expiresAt, isResendAvailable) {

    const timerEl = document.getElementById("timer");
            
    if (isResendAvailable) {
        timerEl.textContent = "Resend";
        timerEl.classList.add('resend');
        timerEl.addEventListener('click', async ()=> {// async (await)
           await resendCodeVerification();
        });

    } else {
        const countdown = setInterval(() => {
            const now = Math.floor(Date.now() / 1000); // current UNIX time
            const remaining = expiresAt - now;
            if (remaining <= 0) {
                clearInterval(countdown);
                timerEl.textContent = "Resend";
                timerEl.classList.add('resend');
                timerEl.addEventListener('click', ()=> {
                    resendCodeVerification();
                });

                return;
            }

            const minutes = String(Math.floor(remaining / 60)).padStart(2, '0');
            const seconds = String(remaining % 60).padStart(2, '0');
            timerEl.textContent = `${minutes}:${seconds}`;
        }, 1000);
    }
}

async function resendCodeVerification() {
    
    let loadingModal = new bootstrap.Modal(document.getElementById("spinnerLoading"));
    loadingModal.show();
    
    await fetch(`./api/resend_code_api.php?v=1.1`, {
        credentials: "include"
    })
    .then(res => res.json())
    .then(data => {
        loadingModal.hide();

        if(data.email_sent) {
            window.location.reload();
        } else {
            alert(data.message);
        }
    })
    .catch(err => {
        console.error(err);
        alert("Server error. Please try again later.");
        loadingModal.hide();
    })
}

inactivityTime(3 * 60 * 1000);