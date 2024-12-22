function openLogger() {
    let logger = document.getElementById("login-panel");
    if(document.cookie.indexOf("login-user") >= 0 && document.cookie.indexOf("login-key") >= 0) {
        window.location.assign("/accounts/my-account/");
    }
    if(logger.style.display == "block"){
        logger.style.display = "none";
    } else {
        logger.style.display = "block";
    }
}

function login() {
    let email = document.getElementsByName("login-email")[0];
    let error1 = document.getElementById("login-error1");
    let loginOtp = document.getElementById("login-otp");
    let loginOtpButton = document.getElementById("login-otp-button");
    let loginOtpConfirmButton = document.getElementById("login-otp-confirm-button");

    if(email.value == "" || email.value == null) {
        error1.innerText = "Please enter your admin email address";
        return;
    }
    //Email
    if(email.value == null || email.value.trim() == "") {
        error1.innerText = "Please enter your email address";
        window.location.href = "#error1";
        return false;
    } else if(email.value.length > 150) {
        error1.innerText = "Sorry we don't permit such email address";
        window.location.href = "#error1";
        return false;
    } else if(!/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/.test(email.value)) {
        error1.innerText = "Invalid email address";
        window.location.href = "#error1";
        return false;
    } else { 
        error1.innerText = "";
        let emailValue = new String(email.value);
        const xmlHttpRequest = new XMLHttpRequest();
        xmlHttpRequest.onreadystatechange = function() {
            if(this.readyState == 4 && this.status == 200) {
                loginOtpButton.style.display = "none";
                loginOtp.style.display = "initial";
                loginOtpConfirmButton.style.display = "initial";
            } else {
                error1.innerText = this.responseText;
            }
        }
        xmlHttpRequest.open("POST", "/accounts/login-access.php");
        xmlHttpRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xmlHttpRequest.send("admin-login=0&email="+emailValue);
    }
}

function confirmLogin() {
    let email = document.getElementsByName("login-email")[0];
    let otp = document.getElementById("login-otp-value");
    let error1 = document.getElementById("login-error1");
    let error2 = document.getElementById("login-error2");

    if(email.value == "" || email.value == null) {
        error1.innerText = "Please enter your admin email address";
        return;
    }
    //Email
    if(email.value == null || email.value.trim() == "") {
        error1.innerText = "Please enter your email address";
        window.location.href = "#error1";
        return false;
    } else if(email.value.length > 150) {
        error1.innerText = "Sorry we don't permit such email address";
        window.location.href = "#error1";
        return false;
    } else if(!/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/.test(email.value)) {
        error1.innerText = "Invalid email address";
        window.location.href = "#error1";
        return false;
    } else { 
        error1.innerText = "";
        let emailValue = new String(email.value);
        const xmlHttpRequest = new XMLHttpRequest();
        xmlHttpRequest.onreadystatechange = function() {
            if(this.readyState == 4 && this.status == 200) {
                error2.innerText = "";
                email.value = "";
                otp.value = "";
                error1.value = "";
                error2.value = "";
                window.location.replace("/accounts/my-account/");
            } else {
                error2.innerText = "Wrong code";
            }
        }
        xmlHttpRequest.open("POST", "/accounts/login-access.php");
        xmlHttpRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xmlHttpRequest.send("confirm-login=0&email="+emailValue+"&login-otp="+otp.value);
    }
}