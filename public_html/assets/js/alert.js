document.body.onload = () => {
    markAsNoFeature();
};
window.addEventListener("click", (event) => {
    let alerts = document.getElementsByClassName("alert");
    for (let i = 0; i < alerts.length; i++) {
        if (event.target == alerts[i] && alerts[i].style.display != "none") {
            alerts[i].style.display = "none";
        } else {
            let closer = alerts[i].getElementsByTagName("span")[0];
            if(event.target == closer) {
                alerts[i].style.display = "none";
            }
        }
    }
});

function noFeatureAlert() {
    document.getElementById("noFeatureAlert").style.display = "block";
    return false;
}
function markAsNoFeature() {
    let a = document.getElementsByTagName("a");
    for(let i = 0; i < a.length; i++) {
        let aURL = a[i].getAttribute("href");
        let aOnclick = a[i].getAttribute("onclick");
        if(aURL == null || aURL == "" || aURL == "#" || aURL == "javascript:void(0)" && aOnclick == null) {
            a[i].setAttribute("onclick", "return noFeatureAlert();");
        }
    }
}