<?php get_header();
if (admin_check()) { ?>

    <?php  //This is the Hero section?>
    <section class="section-container section-container__hero">
        <div class="section-content__container">
            <div class="hero__content">
                <h1 class="font-black ">Kleurt de toekomst</h1>
                <h3 class="sub-tile font-black">Super makkelijke de leukste <br /> workshops te boeken!</h3>
                <div class="button__container">
                    <a href="#booking">
                        <div class="in-btn in-btn__CTA btn__arrow-down" aria-label="button">Start met boeken</div>
                    </a>
                    <a href="#">
                        <div class="in-btn" aria-label="button">Wordt ook lid</div>
                    </a>
                </div>
            </div>
        </div>
        <div class="section-content__image">
            <img src="<?php site_url() ?>/wp-content/themes/inWork-shop/assets/images/kid-and-teacher.png" alt=""/>
        </div>
    </section>

<?php }; if(is_user_logged_in() || admin_check()) { //This is the Booking section?>
    <section id="booking" class="section-container section-container__booker">
        <div class="section-container__booker-container">
            <?php echo do_shortcode( '[booknetic]'); ?>
        </div>
    </section>

<?php }; if (admin_check()) { //This is the Explanation section?>
    <section class="section-container section-container__slider">
        <?php include "include/slider.php" ?>
    </section>

    <?php //This is the Partners section?>
    <section class="section-container section-container__partners">
        <?php include "include/partners.php" ?>
    </section>

<?php } get_footer(); ?>

