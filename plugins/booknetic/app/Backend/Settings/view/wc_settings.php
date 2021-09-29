<?php
namespace BookneticApp\Frontend\view;

use BookneticApp\Providers\Helper;

defined( 'ABSPATH' ) or die();

do_action( 'admin_enqueue_scripts' );
//do_action( 'admin_print_styles' );
do_action( 'admin_print_scripts' );
?>

<link rel="stylesheet" href="<?php print Helper::assets('css/payment_gateways_settings_wc.css', 'Settings')?>">
<link rel="stylesheet" href="<?php print Helper::assets('css/settings.css', 'Settings')?>">
<script type="application/javascript" src="<?php print Helper::assets('js/payment_gateways_settings_wc.js', 'Settings')?>"></script>

<div class="m_header clearfix">
	<div class="m_head_title float-left"><?php print bkntc__('Settings')?> <span class="badge badge-warning row_count">1</span></div>
	<div class="m_head_actions float-right">
		<a href="<?php print admin_url( 'admin.php?page=booknetic&module=settings&setting=payment_gateways' )?>" class="btn btn-lg btn-outline-secondary"><img src="<?php print Helper::icon('back.svg')?>" class="mr-2"> <?php print bkntc__('GO BACK')?></a>
		<button type="button" class="btn btn-lg btn-success settings-save-btn"><i class="fa fa-check pr-2"></i> <?php print bkntc__('SAVE CHANGES')?></button>
	</div>
</div>

<div class="settings-light-portlet woocommerce_fileds">
	<div class="ms-title">
		<?php print bkntc__('Payments')?>
		<span class="ms-subtitle"><?php print bkntc__('Payment methods')?></span>
		<span class="ms-subtitle"><?php print esc_html( $parameters['title'] )?></span>
	</div>
	<div class="ms-content">
		<form method="POST" class="wc_pg_options_form" enctype="multipart/form-data">
			<?php
			print $parameters['inputs'];
			wp_nonce_field( 'woocommerce-settings' );
			?>
			<input type="hidden" name="bkntc_save_settings" value="ok">
		</form>
	</div>
</div>

<?php
do_action( 'admin_print_footer_scripts' );
do_action( 'customize_controls_print_footer_scripts' );
