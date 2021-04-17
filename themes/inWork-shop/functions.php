<?php

// This adds dynamic the tile tag
function edwinCornelisse_theme_support()
{
	add_theme_support('title-tag');
}

	add_action('after_setup_theme', 'edwinCornelisse_theme_support');


// This creates the nav menu
function edwincornelisse_menus()
{
	$locations = array(
		'primary' => "Primary menu",
		'footer' => "Footer menu"
	);
	register_nav_menus($locations);
}

	add_action('init','edwincornelisse_menus');

// This adds the URL of the main stylesheet in the header
function edwinCornelisse_register_style()
{
	wp_enqueue_style('edwincornelisse-style', get_template_directory_uri() . "/style.css", array(), '1.0', 'all');
}

function edwinCornelissee_register_mobileStyle()
{
	wp_enqueue_style('edwincornlisse-mobilestyle', get_template_directory_uri() . "/style-mobile.css", array(), '1.0', 'screen', false );
}

	add_action('wp_enqueue_scripts', 'edwinCornelisse_register_style');

// This adds the URL of the main javascript file in the header
function edwinCornelisse_register_script()
{
	wp_enqueue_script('edwincornelisse-script', get_template_directory_uri() . "/assets/js/main.js", array(), '1.0', true);
}

	add_action('wp_enqueue_scripts', 'edwinCornelisse_register_script');

// This sets the ID for the about us page for the function in page.php
$aboutUsPageID = 15;

// [desktoponly] shortcode
add_shortcode('desktoponly', 'shailan_desktop_only_shortcode');
function shailan_desktop_only_shortcode($atts, $content = null){
	if( !wp_is_mobile() ){
		return wpautop( do_shortcode( $content ) );
	} else {
		return null;
	}
}

// [mobileonly] shortcode
add_shortcode('mobileonly', 'shailan_mobile_only_shortcode');
function shailan_mobile_only_shortcode($atts, $content = null){
	if( wp_is_mobile() ){
		return  wpautop( do_shortcode( $content ) );
	} else {
		return null;
	}
}
