/**
 * @author jimBoYz Ni ChOy!!!
 */
export let inactivityTime = (limit = 2 * 60 * 1000, onLogoutMessage) => {
    let timer;

    function resetTimer() {
      clearTimeout(timer);
      timer = setTimeout(logout, limit);
    }

    async function logout() {
      await fetch("./api/reset_session_api.php", {
            method: "POST",
            credentials: "include"
        })
        .then(res => res.json())
        .then(data => {
            console.log(data.message);

            // Avoid Duplication
            if(data.success) {
                if(typeof onLogoutMessage === "function") {
                    // custom callback from the calling page
                    onLogoutMessage(data.message);
                    return;
                }
                // default behavior for other pages
                alert(data.message);
                window.location.href = "login.php";
            }

        })
        .catch(err => {
            console.error(err);
            if(typeof onLogoutMessage === "function") {
                onLogoutMessage(err);
                return;
            }
            alert(err);
            window.location.href = "login.php";
        });
      
    }

    // activity events
    window.onload = resetTimer;
    document.onmousemove = resetTimer;
    document.onkeydown = resetTimer;
    document.onclick = resetTimer;
    document.onscroll = resetTimer;
};
