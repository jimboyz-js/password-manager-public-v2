/**
 * @author jimBoYz Ni ChOy!!!
 * @Date 10-29-2025 WED 3:57 PM Docs updated
 * @param { Object } obj an JavaScript Object to parse status, id, and etc. url paramater instead relying hardcoded urlParams.append(...)
 * @Date updated version 11-13-2025 THU. 7:17 PM
 */
export async function getCurrentUrl(obj) {

    const urlParams = new URLSearchParams(obj);
    
    await fetch("./api/api_active_url.php", {// Updated: 12-03-2025 Written: 12-04-2025 THU. 1:40PM
        method: "POST",
        headers: {"Content-Type" : "application/x-www-form-urlencoded"},
        body: urlParams.toString(),
        credentials: "include"
    })
    .then(res => res.json())
    .then(data => {
        console.log(data);
    })
    .catch(err => {
        alert(`Failed: ${err}`);
        window.location.href = "login.php";
    })
}