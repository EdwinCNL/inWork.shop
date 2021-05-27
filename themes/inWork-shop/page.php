<?php get_header(); ?>

<?php //This is the Explanation section?>
<section class="section-container">

    <?php  if (have_posts()) : while(have_posts()) : the_post();?>
        <?php the_content();?>
    <?php endwhile; endif;  ?>


</section>

<?php //This is the Partners section?>
<section class="section-container">

</section>

<?php get_footer(); ?>
