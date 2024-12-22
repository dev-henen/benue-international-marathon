let nextMatchDays = document.getElementById("nextMatchDays");
let nextMatchHours = document.getElementById("nextMatchHours");
let nextMatchMinutes = document.getElementById("nextMatchMinutes");
let nextMatchSeconds = document.getElementById("nextMatchSeconds");

let queryString = document.currentScript.src.substring(document.currentScript.src.indexOf("?"));
let urlParams = new URLSearchParams(queryString);
console.log(urlParams.get("nextMatch"));

const nextMatchMS = Date.parse(urlParams.get("nextMatch"));
const countDownDate = new Date(nextMatchMS).getTime();
var x = setInterval(() => {
   var now = new Date().getTime();
   var distance = countDownDate - now;
   var days = Math.floor(distance / (1000 * 60 * 60 * 24));
   var hours = Math.floor(distance % (1000 * 60 * 60 * 24) / (1000 * 60 * 60));
   var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
   var seconds = Math.floor((distance % (1000 * 60)) / 1000);

   nextMatchDays.innerText = days + "d";
   nextMatchHours.innerText = hours + "h";
   nextMatchMinutes.innerText = minutes + "m";
   nextMatchSeconds.innerText = seconds + "s";
    
   if(distance <= 0) {
       nextMatchDays.innerText = "000d";
       nextMatchHours.innerText = "00h";
       nextMatchMinutes.innerText = "00m";
       nextMatchSeconds.innerText = "00s";
   }
}, 1000);