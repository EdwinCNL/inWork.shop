<?php

function load_stylesheets()
{
    if(wp_is_mobile())
        {
            wp_register_style('stylesheet', get_template_directory_uri() . '/style-mobile.css', array(), false, 'all');
        }

    else
        {
            wp_register_style('stylesheet', get_template_directory_uri() . '/style.css', array(), false, 'all');
        }

    wp_enqueue_style('stylesheet');
}

add_action('wp_enqueue_scripts', 'load_stylesheets');

function load_js()
{
    wp_register_script('custom-js', get_template_directory_uri() . '/assets/js/main.js', '', 1, true);
    wp_enqueue_script('custom-js');
}
add_action('wp_enqueue_scripts', 'load_js');

add_theme_support('menus');

register_nav_menus(
    array(

        'top-menu' => __('Top Menu', 'theme'),
        'footer-menu' => __('Footer Menu', 'theme')

    )

);