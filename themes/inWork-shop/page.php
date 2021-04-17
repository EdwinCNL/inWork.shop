<?php

get_header();

if(have_posts()){
    while ( have_posts() ) {
        the_post();
        if(get_the_id() === $aboutUsPageID) {
            get_template_part( 'theme-template/content', 'about-me' );
        }
        else {
        get_template_part( 'theme-template/content', 'page' );
        }
    }
}

get_footer();

