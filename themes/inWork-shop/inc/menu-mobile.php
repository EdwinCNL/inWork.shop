<header class="nav-header">
    <nav id="navbar" class="navbar__container">
        <div class="navbar__container-center">
            <?php
                include ("menu-logo.php");
            ?>
            <div class="nav__block nav__block-right">
                <div class="navbar-toggler" type="button" data-toggle="collapse" onclick="menuToggle()" data-target="#navigation" aria-controls="navigation" aria-expanded="false" aria-label="Toggle navigation">
                    <svg id="nav-icon" data-name="logo-edwincornelisse" xmlns="http://www.w3.org/2000/svg" viewBox="0 2 32 29.35">
                        <circle id="Ellipse_3" data-name="Ellipse 1" class="cls-1" cx="28.38" cy="14.68" r="3.2"/>
                        <circle id="Ellipse_4" data-name="Ellipse 1" class="cls-1" cx="18.24" cy="14.68" r="3.2"/>
                        <circle id="Ellipse_5" data-name="Ellipse 3" class="cls-1" cx="8.1" cy="14.68" r="3.2"/>
                    </svg>
                </div>
            </div>
        </nav>
    </header>
    <div id="navigation" class="collapse__nav-container" >
        <?php
            wp_nav_menu(
                array(
                    'menu' => 'primary',
                    'container' => '',
                    'theme_location' => 'primary',
                    'items_wrap' => '<ul id="" class="nav-buttons__container" style="padding-left: 0px; margin-top: 0; margin-left: 16px;margin-right: 16px;">%3$s</ul>'
                )
            );
        ?>
    </div>
</div>