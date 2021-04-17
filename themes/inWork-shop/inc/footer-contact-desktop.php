
<div class="content__container-left">
	<h4>Contact info</h4>
	<p>U kunt altijd contact met mij opnemen. Ik praat liever via e-mail, vooral omdat we misschien een paar tijdzones verwijderd zijn.</p>
	<p></p>
</div>
<div class="content__container-right">
	<h4>Show and tell</h4>
	<?php
	wp_nav_menu
	(
		array
		(
			'menu' => 'footer',
			'container' => '',
			'theme_location' => 'footer',
			'items_wrap' => '<ul id="" class="nav-footer__container">%3$s</ul>'
		)
	)
	?>
</div>
