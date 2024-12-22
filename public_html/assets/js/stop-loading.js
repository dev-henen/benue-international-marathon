function stopLoading() {
    document.getElementById("appLoader").style.display = "none";
}
var load = setInterval(() => {
    if(document.readyState === 'complete') {
        stopLoading();
        clearInterval(load);
    }
}, 500);