<header class="nav-header">
    <nav id="navbar" class="navbar__container">
        <div class="navbar__container-center">
            <?php
                include ("menu-logo.php");
            ?>
            <div class="nav__block nav__block-right">

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