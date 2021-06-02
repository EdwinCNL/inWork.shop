<?php get_header();
if (admin_check()) { ?>

<?php  //This is the Hero section?>
<section class="section-container section-container__hero">
    <div class="section-container__slogan">
        <h1 class="font-white">Kleurt je dromen</h1>
        <h3 class="font-white">Met workshops, <br /> die je in 7 stappen boekt</h3>
    </div>
    <div class="hero__image-container">
        <div class="hero__image">
        </div>
    </div>
</section>

<?php //This is the Booking section?>
<section class="section-container section-container__booker">
    <div class="section-container__booker-container">
        <?php echo do_shortcode( '[booknetic]'); ?>
    </div>
</section>

<?php //This is the Explanation section?>
<section class="section-container section-container__slider">
    <?php include "include/slider.php" ?>
</section>

<?php //This is the Partners section?>
<section class="section-container section-container__partners">
    <?php include "include/partners.php" ?>
</section>

<?php } get_footer(); ?>

