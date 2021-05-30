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
<body>

<?php
$current_user = wp_get_current_user();
if (!user_can( $current_user, 'administrator' )) { ?>
    <div class="underconsturction__cover">
        <div class="underconsturction__container">
            <img src="<?php echo site_url() . "/wp-content/themes/inWork-shop/assets/images/" ?>logo-animation.gif">
            <h2 style="font-family: Helvetica; text-align: center;">Ik ben momenteel aan het testen.</h2>
        </div>
    </div>
<?php } ?>

    <header class="nav__container-holder">
        <section class="nav__container">
            <div class="nav__container-left">
                <a href="<?php get_home_url() ?>">
                    <img src="<?php echo site_url() . "/wp-content/themes/inWork-shop/assets/images/" ?>inWorkshop-small.gif">
                </a>
            </div>
            <nav class="nav__container-right">
                <div class="nav__burger" onclick="dropMenu()">
                    <hr>
                    <hr>
                    <hr>
                </div>
                <div id="navDropdown" class="nav__dropdown-container">
                    <?php include "include/navigation.php" ?>
                </div>
            </nav>
        </section>
    </header>
    <main>

