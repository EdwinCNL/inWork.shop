
function sliderNext() {
    // Get current slide
    let setID = document.getElementsByClassName("slider__box-active")[0].id;
    let setNumber = setID.replace('slider-', '');
    setNumber++;

    // Check if it's the last slide
    if (setNumber == 3) {
        document.getElementById("slider-next").classList.add("slider__button-not-active");
        document.getElementById("slider-next").removeAttribute("onclick");
    }

    // Check if not it's the first slide
    if (setNumber != 1){
        document.getElementById("slider-back").classList.remove("slider__button-not-active");
        document.getElementById("slider-back").setAttribute("onclick", "sliderBack()");
    }

    // Create the ID of the next slide
    let slider = "slider-";
    let activeClass = slider.concat(setNumber);

    // Add the active state to the selected slide
    document.getElementById(activeClass).classList.add("slider__box-active");

    //Remove active state of formal slide
    document.getElementById(setID).classList.remove("slider__box-active");

};

function sliderBack() {
    let setID = document.getElementsByClassName("slider__box-active")[0].id;
    let setNumber = setID.replace('slider-','');
    setNumber--;

    if (setNumber == 1) {
        document.getElementById("slider-back").classList.add("slider__button-not-active");
        document.getElementById("slider-back").removeAttribute("onclick");
    }
    if (setNumber != 3) {
        document.getElementById("slider-next").classList.remove("slider__button-not-active");
        document.getElementById("slider-next").setAttribute("onclick", "sliderNext()");
    }

    let slider = "slider-";
    let activeClass = slider.concat(setNumber);

    document.getElementById(activeClass).classList.add("slider__box-active");
    document.getElementById(setID).classList.remove("slider__box-active");

};

