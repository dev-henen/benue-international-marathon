const showHeadLines = document.getElementById("showHeadLines");
const headLine = document.getElementById("headLine");
let time = 100;
let news = "The first full international marathon to be held in Benue State is the Benue International Marathon (BIM).";
let moreNews = [news, "Accreditation would be given to journalists that wanted to cover the event.", "The Benue International Marathon (BIM) strives to be the one-of-a-kind event that everyone looks forward to, bringing spectators to Nigeria to see a world-class event, including runners, the international media, and tourists. Because running, jogging, and exercise in general have such enormous health advantages, the Benue International Marathon strives to promote unity and improve the general well-being of Benue inhabitants."];
var x = 0;
var loop = 0;
function startShowingHeadLines() {
    if(x < news.length) {
        showHeadLines.innerText += news.charAt(x);
    }
    x++;
    writeNews = setTimeout(startShowingHeadLines, time);
    if(x >= news.length) {
        clearTimeout(writeNews);
        setTimeout(startDeletingHeadLines, 1000);
    }
    headLine.scrollLeft += 50;
}
startShowingHeadLines();
function startDeletingHeadLines() {
    if(x != 0) {
        showHeadLines.innerText = news.slice(0, x);
        x--;
    }
    deleteNews = setTimeout(startDeletingHeadLines, time);
    if(x == 0) {
        clearTimeout(deleteNews);
        showHeadLines.innerText = "";
        loop++;
        if(loop >= moreNews.length) {
            loop = 0;
        }
        news = moreNews[loop];
        startShowingHeadLines();
    }
}