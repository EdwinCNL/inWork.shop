<!DOCTYPE html>
<html lang="en">
<head>

	<!-- Meta -->
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="Blog Site Template">
	<meta name="author" content="https://youtube.com/FollowAndrew">
	<link rel="shortcut icon" href="/wp-content/themes/ecornelisse/assets/images/edwincornelisse-fav.png">


	<?php
	wp_head();

    if(wp_is_mobile()) {
	    edwinCornelissee_register_mobileStyle();
    }
    ?>

</head>

<body>

<?php

    if(wp_is_mobile()) {
        include("inc/menu-mobile.php");
    }
    elseif(!wp_is_mobile()) {
        include("inc/menu-desktop.php");
    }

?>