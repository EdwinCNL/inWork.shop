<!DOCTYPE html>
<html lang="nl">
<head>
    <!-- Meta -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A booking platform for educational and cultural workshops">
    <meta name="author" content="www.edwincornelisse.nl">
    <?php wp_head(); ?>
</head>
<body class="frontpage">

<?php if (!is_user_logged_in()) { ?>
    <div class="underconsturction__cover">
        <div class="underconsturction__container">
            <img src="<?php echo site_url() . "/wp-content/themes/inWork-shop/assets/images/" ?>inWorkshop-logo.jpg">
            <h2 style="font-family: Helvetica; text-align: center;">Ik ben momenteel aan het testen.</h2>
            <div style="min-width:560px; margin-top: 48px; text-align:center;">
                <?php echo do_shortcode('[wp_login_form]'); ?>
            </div>
        </div>
    </div>
<?php } else {  ?>

    <header class="nav__container-holder">
        <section id="navContainer" class="nav__container">
            <div class="nav__container-left">
                <a href="<?php get_home_url() ?>">
                    <img src="<?php echo site_url() . "/wp-content/themes/inWork-shop/assets/images/" ?>inWorkshop-logo.png">
                </a>
            </div>
            <nav class="nav__container-right">
                <div id="menuBurger" class="nav__burger">
                    <div class="nav__burger-btn"></div>
                </div>
                <div id="navDropdown" class="nav__dropdown-container">
                    <?php include "include/navigation.php" ?>
                </div>
            </nav>
        </section>
    </header>
    <main>

<?php } ?>