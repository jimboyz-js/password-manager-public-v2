import { getParamFromScript } from "./param.js";

/**
 * @author jimBoYz Ni ChOy!!!
 */
(function() {
    const user_auth = document.querySelector(".user-auth");

    if(user_auth) {
        
        user_auth.addEventListener("click", async () => {
            
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

            const mode = getParamFromScript("mode", 'script[src*="user-auth.js"]');

            // Version 2.2.2 the method is change from GET to POST see user_auth.php
            // await fetch(`./api/user-auth-api.php?id=${user_auth.id}&mode=${encodeURIComponent(mode)}`
            await fetch(`./api/user-auth-api.php`, {
                method: "POST",
                headers: {"Content-Type": "application/json"},
                body: JSON.stringify({id: user_auth.id, mode}),
                credentials: "include",
            })
            .then(res => res.json())
            .then(data => {
                clearInterval(interval);
                bar.style.width = "100%";
                bar.textContent = "100%";

                setTimeout(() => {
                    loadingModal.hide();
                    if(data.success) {
                        window.location.href = "auth-page.php";
                        return;
                    }
                    alert(data.message);

                }, 500);
            })
            .catch(err => {
                clearInterval(interval);
                loadingModal.hide();
                console.error(err);
                alert(err);
            })
        })
    }
})();
