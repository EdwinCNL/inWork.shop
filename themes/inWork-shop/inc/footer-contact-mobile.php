<div class="mobile__footer">
	<h4>Contact info</h4>
	<div id="description-98" class="project__description project__description-mobile">
		<p>U kunt altijd contact met mij opnemen. Ik praat liever via e-mail, vooral omdat we misschien een paar tijdzones verwijderd zijn.</p>
		<p></p>
		<div id="containerOverlay" class="content__container-overlay content__container-overlay-mobile">
			<div class="content__container-button">
				<p id="showMore-98" onclick="showMore(1, 98)" class="show-more__button active">Show info</p>
				<p id="showLess-98" onclick="showMore(2 ,98)" class="show-more__button">Hide info</p>
			</div>
		</div>
	</div>
	<h4>Show and tell</h4>
	<div id="description-99" class="project__description project__description-mobile">
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
		<div id="containerOverlay" class="content__container-overlay content__container-overlay-mobile">
			<div class="content__container-button">
				<p id="showMore-99" onclick="showMore(1, 99)" class="show-more__button active">Show projects</p>
				<p id="showLess-99" onclick="showMore(2 ,99)" class="show-more__button">Hide projects</p>
			</div>
		</div>
	</div>
</div>
