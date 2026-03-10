/**
 * @author jimBoYz Ni ChOy!!!
 */

import { userAccountHandler } from "./data-handler.js";

document.getElementById("signupForm").addEventListener("submit", async (e)=> {
    e.preventDefault();

    let form = e.target;

    if(!form.checkValidity()) {
        e.preventDefault();
        form.reportValidity();

        return;
    }

    await userAccountHandler("add", true);
    
})

// SESSION TIME TRACKER
inactivityTime();