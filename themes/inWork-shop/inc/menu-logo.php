    <?php if(!is_front_page() and wp_is_mobile()) {  ?>
		<div class="nav__block nav__block-left">
			<a class="logo__container" href="https://www.edwincornelisse.nl/#edwincornelisse-projects" alt="go back to projects">
				<svg id="ec-go-back" class="logo " data-name="logo-edwincornelisse" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
                    <path d="M22,15.6H10.1l6.3-6.3c0.3-0.3,0.3-0.8,0-1.1s-0.8-0.3-1.1,0l-7.6,7.6c-0.1,0.1-0.1,0.2-0.2,0.2c-0.1,0.2-0.1,0.4,0,0.6
	c0,0.1,0.1,0.2,0.2,0.2c0,0,0,0,0,0l7.6,7.6c0.1,0.1,0.3,0.2,0.5,0.2s0.4-0.1,0.5-0.2c0.3-0.3,0.3-0.8,0-1.1l-6.3-6.3H22
	c0.4,0,0.8-0.3,0.8-0.7S22.4,15.6,22,15.6z"/>
                    <path d="M15.9,30.8c-8,0-14.5-6.5-14.5-14.5S7.9,1.8,15.9,1.8c8,0,14.5,6.5,14.5,14.5S23.9,30.8,15.9,30.8z M15.9,3.3
	c-7.2,0-13,5.8-13,13s5.8,13,13,13c7.2,0,13-5.8,13-13S23.1,3.3,15.9,3.3z"/></svg>
				<div class="brand-name">Terug</div>
			</a>
		</div>
    <?php } else { ?>
        <div class="nav__block nav__block-left">
            <a class="logo__container" href="https://www.edwincornelisse.nl/" alt="logo-edwincornelisse">
                <svg id="ec-logo" class="logo " data-name="logo-edwincornelisse" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 29.35">
                    <circle id="Ellipse_2" data-name="Ellipse 1" class="cls-1" cx="18.24" cy="14.68" r="3.71"/>
                    <circle id="Ellipse_1" data-name="Ellipse 3" class="cls-1" cx="8.1" cy="14.68" r="3.71"/>
                    <path class="Cirkel" d="M28.87,10.92a14.68,14.68,0,1,0,0,7.51,3.82,3.82,0,0,0,0-7.51Zm-14.19,17A13.21,13.21,0,1,1,27.34,11a3.82,3.82,0,0,0,0,7.45A13.23,13.23,0,0,1,14.68,27.88ZM29.2,16.79a2.31,2.31,0,0,1-1,.23,2.73,2.73,0,0,1-.5-.05,2.35,2.35,0,0,1,0-4.59,2.73,2.73,0,0,1,.5-.05,2.31,2.31,0,0,1,1,.23,2.35,2.35,0,0,1,0,4.23Z"/>
                </svg>
                <div class="brand-name">Edwin Cornelisse</div>
            </a>
        </div>
    <?php } ?>
