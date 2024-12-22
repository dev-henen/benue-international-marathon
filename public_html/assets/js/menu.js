let links = document.getElementById("menuLinks");
let search = document.getElementById("menuSearch");

function menu() {
    if(links.style.display == "block") {
        links.style.display = "none";
    } else {
        links.style.display = "block";
    }
}
function searchBox() {
    if(search.style.display == "flex") {
        search.style.display = "none";
    } else {
        search.style.display = "flex";
    }
}


try {
    let slideIndex = 1;
    showSlides(slideIndex);
} catch(e) {}

function plusSlides(n) {
  showSlides(slideIndex += n);
}

function currentSlide(n) {
  showSlides(slideIndex = n);
}

function showSlides(n) {
  let i;
  let slides = document.getElementsByClassName("mySlides");
  let dots = document.getElementsByClassName("dot");
  if (n > slides.length) {slideIndex = 1}
  if (n < 1) {slideIndex = slides.length}
  for (i = 0; i < slides.length; i++) {
    slides[i].style.display = "none";
  }
  for (i = 0; i < dots.length; i++) {
    dots[i].className = dots[i].className.replace(" active", "");
  }
  slides[slideIndex-1].style.display = "block";
  dots[slideIndex-1].className += " active";
}
let oldScroll = 0;
window.onscroll = () => {
    let y = window.scrollY;
    if(y > oldScroll) {
        let all = document.querySelectorAll("section");
        for(let i = 0; i < all.length; i++) {
            if(isElementVisible(all[i])) {
                all[i].classList.add("fromBottom");
            } else {
                //all[i].classList.remove("fromBottom");
            }
        }
    }
    oldScroll = y;
}

function isElementVisible(el) {
    var rect     = el.getBoundingClientRect(),
        vWidth   = window.innerWidth || document.documentElement.clientWidth,
        vHeight  = window.innerHeight || document.documentElement.clientHeight,
        efp      = function (x, y) { return document.elementFromPoint(x, y) };     

    if (rect.right < 0 || rect.bottom < 0 
            || rect.left > vWidth || rect.top > vHeight)
        return false;

    return (
          el.contains(efp(rect.left,  rect.top + 100))
    );
}