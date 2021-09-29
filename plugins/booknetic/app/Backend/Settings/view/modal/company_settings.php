<?php
namespace BookneticApp\Frontend\view;

use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Date;
use BookneticApp\Providers\Permission;

defined( 'ABSPATH' ) or die();

?>
<div id="booknetic_settings_area">
	<link rel="stylesheet" href="<?php print Helper::assets('css/company_settings.css', 'Settings')?>">
	<script type="application/javascript" src="<?php print Helper::assets('js/company_settings.js', 'Settings')?>"></script>


	<div class="actions_panel clearfix">
		<button type="button" class="btn btn-lg btn-success settings-save-btn float-right"><i class="fa fa-check pr-2"></i> <?php print bkntc__('SAVE CHANGES')?></button>
	</div>

	<div class="settings-light-portlet">
		<div class="ms-title"><?php print bkntc__('Company details')?></div>
		<div class="ms-content">

			<div class="form-row">
				<div class="form-group col-md-12">
					<div class="company_image_img_div"><img src="<?php print Helper::profileImage(Helper::getOption('company_image', ''), 'Settings')?>" id="company_image_img"></div>
					<input type="file" id="company_image_input">
				</div>
			</div>

			<div class="form-row">
				<div class="form-group col-md-6">
					<label for="input_company_name"><?php print bkntc__('Company name')?>:</label>
					<input class="form-control" id="input_company_name" value="<?php print htmlspecialchars( Helper::getOption('company_name', '') )?>">
				</div>
				<div class="form-group col-md-6">
					<label for="input_company_address"><?php print bkntc__('Address')?>:</label>
					<input class="form-control" id="input_company_address" value="<?php print htmlspecialchars( Helper::getOption('company_address', '') )?>">
				</div>
			</div>

			<div class="form-row">
				<div class="form-group col-md-6">
					<label for="input_company_phone"><?php print bkntc__('Phone')?>:</label>
					<input class="form-control" id="input_company_phone" value="<?php print htmlspecialchars( Helper::getOption('company_phone', '') )?>">
				</div>
				<div class="form-group col-md-6">
					<label for="input_company_website"><?php print bkntc__('Website')?>:</label>
					<input class="form-control" id="input_company_website" value="<?php print htmlspecialchars( Helper::getOption('company_website', '') )?>">
				</div>
			</div>

			<?php if( ! Helper::isSaaSVersion() || Permission::getPermission('upload_logo_to_booking_panel') !== 'off' ):?>
			<div class="form-row">
				<div class="form-group col-md-6">
					<div class="form-control-checkbox">
						<label for="input_display_logo_on_booking_panel"><?php print bkntc__('Display a company logo on the  Booking panel')?>:</label>
						<div class="fs_onoffswitch">
							<input type="checkbox" class="fs_onoffswitch-checkbox" id="input_display_logo_on_booking_panel"<?php print Helper::getOption('display_logo_on_booking_panel', 'off')=='on'?' checked':''?>>
							<label class="fs_onoffswitch-label" for="input_display_logo_on_booking_panel"></label>
						</div>
					</div>
				</div>
			</div>
			<?php endif;?>

		</div>
	</div>
</div>