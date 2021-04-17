function menuToggle() {
    let navBlock = document.getElementById("navigation");
    navBlock.classList.toggle("show-nav");
};



let styleToggle = (callback) => {
    if (document.readyState != "loading") callback();
    else document.addEventListener("DOMContentLoaded", callback);
};

styleToggle (()=> {
    let toggleClassNL = document.getElementById("menu-item-weglot-26-nl");
    let toggleClassEN = document.getElementById("menu-item-weglot-26-en");

    if (window.location.href.indexOf("/en/") > -1) {
        toggleClassEN.classList.add("toggle-bold");
        toggleClassNL.classList.add("toggle-normal");
    }
    else {
        toggleClassEN.classList.add("toggle-normal");
        toggleClassNL.classList.add("toggle-bold");
    }
});

function showMore(x, y) {
    buttonPurpose = x;
    buttonNumber = y;
    let showMoreID = "showMore-".concat(buttonNumber);
    let getShowMore = document.getElementById(showMoreID);
    let showLessID = "showLess-".concat(buttonNumber);
    let getShowLess = document.getElementById(showLessID);
    let descriptionID = "description-".concat(buttonNumber);
    let projectContent = document.getElementById(descriptionID);
    if (buttonPurpose == 1) {
        getShowMore.classList.remove("active");
        getShowLess.classList.add("active");
        projectContent.classList.add("project__description-show");
    }
    else if(buttonPurpose == 2) {
        getShowMore.classList.add("active");
        getShowLess.classList.remove("active");
        projectContent.classList.remove("project__description-show");
    }

};


function GTSwitcher(x, y) {
    let SwitchNr = x;
    let AmountNr = y;
    let number = 1;
    while (number < AmountNr) {
        let SwitchID = "galleryToggler-".concat(number);
        let ViewID = "galleryView-".concat(number);
        let getSwitch = document.getElementById(SwitchID);
        let getView = document.getElementById(ViewID);

        if (getSwitch.classList.contains("active")) {
            getSwitch.classList.remove("active");
            getView.classList.remove("gt-gallery-active");
        }

        number++;
    };
    let SwitchID = "galleryToggler-".concat(SwitchNr);
    let ViewID = "galleryView-".concat(SwitchNr);
    document.getElementById(SwitchID).classList.add("active");
    document.getElementById(ViewID).classList.add("gt-gallery-active");
};
