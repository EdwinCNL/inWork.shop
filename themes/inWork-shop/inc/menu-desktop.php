<header class="nav-header">
    <nav id="navbar" class="navbar__container">
        <div class="navbar__container-center">
            <?php
                include ("menu-logo.php");
            ?>
            <div class="nav__block nav__block-right">

                <?php if(!wp_is_mobile()) { include("navbar-toggles.php"); } ?>

                <div class="navbar-toggler" type="button" data-toggle="collapse" onclick="menuToggle()" data-target="#navigation" aria-controls="navigation" aria-expanded="false" aria-label="Toggle navigation">
                    <svg id="nav-icon" data-name="logo-edwincornelisse" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 20">
                        <circle id="Ellipse_3" data-name="Ellipse 1" class="cls-1" cy="14.68" r="3.6" cx="34"></circle>
                        <circle id="Ellipse_4" data-name="Ellipse 1" class="cls-1" cy="14.68" r="3.6" cx="20"></circle>
                        <circle id="Ellipse_5" data-name="Ellipse 3" class="cls-1" cy="14.68" r="3.6" cx="6"></circle>
                    </svg>
                    <div id="navigation" class="collapse__nav-container" >
                        <?php
                        wp_nav_menu(
                            array(
                                'menu' => 'primary',
                                'container' => '',
                                'theme_location' => 'primary',
                                'items_wrap' => '<ul id="" class="nav-buttons__container">%3$s</ul>'
                            )
                        )
                        ?>
                    </div>
                </div>
            </div>
        </div>
	</nav>
</header>