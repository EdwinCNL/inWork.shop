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
    <header class="nav__container-fixed">
        <section class="nav__container">
            <div class="nav__container-left">
                <a href="<?php get_home_url() ?>"><h1>LOGO</h1></a>
            </div>
            <nav class="nav__container-right">
                <?php include "include/navigation.php" ?>
            </nav>
        </section>
    </header>

