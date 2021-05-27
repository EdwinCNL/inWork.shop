
function sliderNext() {
    // Get current slide
    let setID = document.getElementsByClassName("slider__box-active")[0].id;
    let setNumber = setID.replace('slider-', '');
    setNumber++;

    let dotID = document.getElementsByClassName("slider__dot-active")[0].id;
    let dot = "dot-";
    let activeDot = dot.concat(setNumber);

    document.getElementById(activeDot).classList.add("slider__dot-active");
    document.getElementById(dotID).classList.remove("slider__dot-active");

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

    let dotID = document.getElementsByClassName("slider__dot-active")[0].id;
    let dot = "dot-";
    let activeDot = dot.concat(setNumber);
    console.log(activeDot);

    document.getElementById(activeDot).classList.add("slider__dot-active");
    document.getElementById(dotID).classList.remove("slider__dot-active");

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

function sliderDot(number) {
    let dotNumber = number;
    let sliderID = document.getElementsByClassName("slider__box-active")[0].id;
    let dotID = document.getElementsByClassName("slider__dot-active")[0].id;

    let dot = "dot-";
    let activeDot = dot.concat(dotNumber);

    if (dotID != activeDot) {

        document.getElementById(activeDot).classList.add("slider__dot-active");
        document.getElementById(dotID).classList.remove("slider__dot-active");

        let slider = "slider-";
        let activeClass = slider.concat(number);

        document.getElementById(activeClass).classList.add("slider__box-active");
        document.getElementById(sliderID).classList.remove("slider__box-active");

        let backButton = document.getElementById("slider-back");
        let checkBackButton = backButton.classList.contains("slider__button-not-active");

        if (dotNumber == 1) {
            backButton.classList.add("slider__button-not-active");
            backButton.removeAttribute("onclick");
        } else if (checkBackButton || dotNumber != 1) {
            backButton.classList.remove("slider__button-not-active");
            backButton.setAttribute("onclick", "sliderBack()");
        }

        let nextButton = document.getElementById("slider-next");
        let checkNextButton = nextButton.classList.contains("slider__button-not-active");

        if (dotNumber == 3) {
            nextButton.classList.add("slider__button-not-active");
            nextButton.removeAttribute("onclick");
        } else if (checkNextButton || dotNumber != 3) {
            nextButton.classList.remove("slider__button-not-active");
            nextButton.setAttribute("onclick", "sliderNext()");
        }

    }

}

