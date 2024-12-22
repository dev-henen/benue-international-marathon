function validateForm() {

    let surname = document.getElementsByName("surname")[0];
    let firstName = document.getElementsByName("firstName")[0];
    let genderfemale = document.getElementsByName("gender")[0];
    let genderMale = document.getElementsByName("gender")[1];
    let bloodGroup = document.getElementsByName("bloodGroup")[0];
    let birthday = document.getElementsByName("birthday")[0];
    let nationality = document.getElementsByName("nationality")[0];
    let stateOfOrigin = document.getElementsByName("stateOfOrigin")[0];
    let stateOfResidence = document.getElementsByName("stateOfResidence")[0];
    let email = document.getElementsByName("email")[0];
    let phoneNumber = document.getElementsByName("phoneNumber")[0];
    let contactAddress = document.getElementsByName("contactAddress")[0];
    let myHeight = document.getElementsByName("myHeight")[0];
    let myWeight = document.getElementsByName("myWeight")[0];
    let emergencyPhoneNumber = document.getElementsByName("emergencyPhoneNumber")[0];
    let confirm = document.getElementsByName("confirm")[0];
    let passport = document.getElementsByName("passport")[0];

    let errorLine1 = document.getElementById("errorLine1");
    let errorLine2 = document.getElementById("errorLine2");
    let errorLine3 = document.getElementById("errorLine3");
    let errorLine4 = document.getElementById("errorLine4");
    let errorLine5 = document.getElementById("errorLine5");
    let errorLine6 = document.getElementById("errorLine6");
    let errorLine7 = document.getElementById("errorLine7");
    let errorLine8 = document.getElementById("errorLine8");
    let errorLine9 = document.getElementById("errorLine9");
    let errorLine10 = document.getElementById("errorLine10");
    let errorLine11 = document.getElementById("errorLine11");
    let errorLine12 = document.getElementById("errorLine12");
    let errorPhoto = document.getElementById("errorPhoto");

    errorLine1.innerText = "";
    errorLine2.innerText = "";
    errorLine3.innerText = "";
    errorLine4.innerText = "";
    errorLine5.innerText = "";
    errorLine6.innerText = "";
    errorLine7.innerText = "";
    errorLine8.innerText = "";
    errorLine9.innerText = "";
    errorLine10.innerText = "";
    errorLine11.innerText = "";
    errorLine12.innerText = "";
    errorPhoto.innerText = "";

    if(surname.value == null || surname.value.trim() == "") {
        errorLine1.innerText = "Please enter your surname.";
        errorLine1.style.textAlign = "left";
        window.location.href = "#errorLine1";
        return false;
    } else if(surname.value.length > 15 || surname.value.length < 2) {
        errorLine1.innerText = "Name must not be less 2 characters in length " + 
                               "and must not be greater than 15 characters in length.";
        errorLine1.style.textAlign = "left";
        window.location.href = "#errorLine1";
        return false;
    } else if(!/^[A-Za-z]*$/.test(surname.value)) {
        errorLine1.innerText = "Sorry only letters are allowed.";
        errorLine1.style.textAlign = "left";
        window.location.href = "#errorLine1";
        return false;
    }

    if(firstName.value == null || firstName.value.trim() == "") {
        errorLine1.innerText = "Please enter your firstname.";
        errorLine1.style.textAlign = "right";
        window.location.href = "#errorLine1";
        return false;
    } else if(firstName.value.length > 15 || firstName.value.length < 2) {
        errorLine1.innerText = "Name must not be less 2 characters in length " + 
                               "and must not be greater than 15 characters in length.";
        errorLine1.style.textAlign = "right";
        window.location.href = "#errorLine1";
        return false;
    } else if(!/^[A-Za-z]*$/.test(firstName.value)) {
        errorLine1.innerText = "Sorry only letters are allowed.";
        errorLine1.style.textAlign = "right";
        window.location.href = "#errorLine1";
        return false;
    }

    if(!genderMale.checked && !genderfemale.checked) {
        errorLine2.innerText = "What is your gender?";
        window.location.href = "#errorLine2";
        return false;
    }

    if(bloodGroup.value.toUpperCase() != "A" &&
        bloodGroup.value.toUpperCase() != "A+" &&
        bloodGroup.value.toUpperCase() != "A-" &&
        bloodGroup.value.toUpperCase() != "B" &&
        bloodGroup.value.toUpperCase() != "B+" &&
        bloodGroup.value.toUpperCase() != "B-" &&
        bloodGroup.value.toUpperCase() != "AB" &&
        bloodGroup.value.toUpperCase() != "AB+" &&
        bloodGroup.value.toUpperCase() != "AB-" &&
        bloodGroup.value.toUpperCase() != "O" &&
        bloodGroup.value.toUpperCase() != "O+" &&
        bloodGroup.value.toUpperCase() != "O-") {
            errorLine3.innerText = "Please specify a valid blood group.";
            window.location.href = "#errorLine3";
            return false;
        }

        if(birthday.value.split("-").length < 3 && birthday.value.split("/").length < 3) {
            errorLine4.innerText = "Please select or enter a valid date of birth.";
            window.location.href = "#errorLine4";
            return false;
        } else {
            let birthdayToArray = (birthday.value.split("-").length >= 3) ? birthday.value.split("-") : birthday.value.split("/");
            let numArr = [];
            let maxAge = 18;
            for(let i = 0; i < birthdayToArray.length; i++) {
                numArr.push(Number(birthdayToArray[i]));
            }
            let yearOfBirth = Math.max(...numArr);
            let numOfYears = Math.abs(new Date().getFullYear() - yearOfBirth);
            if(numOfYears < maxAge) {
                errorLine4.innerText = "Sorry you must be " + maxAge + "+ to join this race.";
                window.location.href = "#errorLine4";
                return false;
            }
        }

        if(nationality.value == null || nationality.value.trim() == "") {
            errorLine5.innerText = "What is your nationality?";
            window.location.href = "#errorLine5";
            return false;
        }

        if(stateOfOrigin.value == null || stateOfOrigin.value.trim() == "") {
            errorLine6.innerText = "Please specify your state of origin.";
            errorLine6.style.textAlign = "left";
            window.location.href = "#errorLine6";
            return false;
        }

        if(stateOfResidence.value == null || stateOfResidence.value.trim() == "") {
            errorLine6.innerText = "Please specify your state of residence.";
            errorLine6.style.textAlign = "right";
            window.location.href = "#errorLine6";
            return false;
        }

        if(email.value == null || email.value.trim() == "") {
            errorLine7.innerText = "Please enter your email address.";
            window.location.href = "#errorLine7";
            return false;
        } else if(!/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/.test(email.value)) {
            errorLine7.innerText = "Please provide a valid email address.";
            window.location.href = "#errorLine7";
            return false;
        }

        if(phoneNumber.value == null || phoneNumber.value.trim() == "") {
            errorLine8.innerText = "Please enter your phone number.";
            window.location.href = "#errorLine8";
            return false;
        } else if(phoneNumber.value.length > 15 || phoneNumber.length < 4 || !/^[0-9+]*$/.test(phoneNumber.value)) {
            errorLine8.innerText = "Please enter a valid phone number.";
            window.location.href = "#errorLine8";
            return false;
        }

        if(contactAddress.value == null || contactAddress.value.trim() == "") {
            errorLine9.innerText = "Please enter your contact address.";
            window.location.href = "#errorLine9";
            return false;
        }

        if(myHeight.value == null || myHeight.value == "") {
            errorLine10.innerText = "Please enter your Height.";
            errorLine10.style.textAlign = "left";
            window.location.href = "#errorLine10";
            return false;
        } else if(!/^[0-9.]*$/.test(myHeight.value)) {
            errorLine10.innerText = "Please enter a valid Height.";
            errorLine10.style.textAlign = "left";
            window.location.href = "#errorLine10";
            return false;
        }

        if(myWeight.value == null || myWeight.value == "") {
            errorLine10.innerText = "Please enter your Weight.";
            errorLine10.style.textAlign = "right";
            window.location.href = "#errorLine10";
            return false;
        } else if(!/^[0-9.]*$/.test(myWeight.value)) {
            errorLine10.innerText = "Please enter a valid Weight.";
            errorLine10.style.textAlign = "right";
            window.location.href = "#errorLine10";
            return false;
        }

        if(emergencyPhoneNumber.value == null || emergencyPhoneNumber.value.trim() == "") {
            errorLine11.innerText = "Please enter an emergency phone number.";
            window.location.href = "#errorLine11";
            return false;
        } else if(emergencyPhoneNumber.value.length > 15 || emergencyPhoneNumber.length < 4 || !/^[0-9+]*$/.test(emergencyPhoneNumber.value)) {
            errorLine11.innerText = "Please enter a valid emergency phone number.";
            window.location.href = "#errorLine11";
            return false;
        }

        if(passport.files[0] == "undefined" || passport.files[0] == null || passport.files[0] == "") {
            errorPhoto.innerText = "Please upload your recent passport.";
            window.location.href = "#errorPhoto";
            return false;
        }

        if(!confirm.checked) {
            errorLine12.innerText = "Please confirm you have read and accept our T & C";
            window.location.href = "#errorLine12";
            return false;
        }
}