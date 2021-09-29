<?php
namespace BookneticApp\Frontend\view;

use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Date;

defined( 'ABSPATH' ) or die();

?>
<div id="booknetic_settings_area">
	<link rel="stylesheet" href="<?php print Helper::assets('css/customer_panel_settings.css', 'Settings')?>">
	<script type="application/javascript" src="<?php print Helper::assets('js/customer_panel_settings.js', 'Settings')?>"></script>

	<div class="actions_panel clearfix">
		<button type="button" class="btn btn-lg btn-success settings-save-btn float-right"><i class="fa fa-check pr-2"></i> <?php print bkntc__('SAVE CHANGES')?></button>
	</div>

	<div class="settings-light-portlet">
		<div class="ms-title">
			<?php print bkntc__('Front-end panels')?>
			<span class="ms-subtitle"><?php print bkntc__('Customer Panel')?></span>
		</div>
		<div class="ms-content">

			<form class="position-relative">
                <?php if ( ! Helper::isSaaSVersion() ) : ?>
				<div class="form-row enable_disable_row">

					<div class="form-group col-md-2">
						<input id="input_customer_panel_enable" type="radio" name="input_customer_panel_enable" value="off"<?php print Helper::getOption('customer_panel_enable', 'off',false)=='off'?' checked':''?>>
						<label for="input_customer_panel_enable"><?php print bkntc__('Disabled')?></label>
					</div>
					<div class="form-group col-md-2">
						<input id="input_customer_panel_disable" type="radio" name="input_customer_panel_enable" value="on"<?php print Helper::getOption('customer_panel_enable', 'off',false)=='on'?' checked':''?>>
						<label for="input_customer_panel_disable"><?php print bkntc__('Enabled')?></label>
					</div>

				</div>
                <?php endif; ?>

				<div id="customer_panel_settings_area">

					<div class="form-row">
                        <?php if ( ! Helper::isSaaSVersion() ) : ?>
						<div class="form-group col-md-6">
							<label for="input_customer_panel_page_id"><?php print bkntc__('Page of Customer Panel')?>:</label>
							<select class="form-control" id="input_customer_panel_page_id">
								<?php foreach ( get_pages() AS $page ) : ?>
								<option value="<?php print esc_html($page->ID)?>"<?php print Helper::getOption('customer_panel_page_id', '') == $page->ID ? ' selected' : ''?>><?php print esc_html(empty($page->post_title) ? '-' : $page->post_title)?></option>
								<?php endforeach; ?>
							</select>
						</div>
                        <?php endif;?>
							<div class="form-group col-md-6">
								<label for="input_timeslot_length"><?php print bkntc__('Time restriction to change appointments')?>:</label>
								<select class="form-control" id="input_time_restriction_to_make_changes_on_appointments">
									<?php foreach ( [1,2,3,4,5,10,12,15,20,25,30,35,40,45,50,55,60,90,120,180,240,300] AS $minute ) :?>
										<option value="<?php print $minute?>"<?php print Helper::getOption('time_restriction_to_make_changes_on_appointments', '5')==$minute ? ' selected':''?>><?php print Helper::secFormat($minute*60)?></option>
									<?php endforeach;?>
								</select>
							</div>
					</div>

					<div class="form-row">
						<div class="form-group col-md-6">
							<div class="form-control-checkbox">
								<label for="input_customer_panel_allow_reschedule"><?php print bkntc__('Allow customers to reschedule their appointments')?>:</label>
								<div class="fs_onoffswitch">
									<input type="checkbox" class="fs_onoffswitch-checkbox" id="input_customer_panel_allow_reschedule"<?php print Helper::getOption('customer_panel_allow_reschedule', 'on')=='on'?' checked':''?>>
									<label class="fs_onoffswitch-label" for="input_customer_panel_allow_reschedule"></label>
								</div>
							</div>
						</div>
					</div>

					<div class="form-row">
						<div class="form-group col-md-6">
							<div class="form-control-checkbox">
								<label for="input_customer_panel_allow_cancel"><?php print bkntc__('Allow customers to cancel their appointments')?>:</label>
								<div class="fs_onoffswitch">
									<input type="checkbox" class="fs_onoffswitch-checkbox" id="input_customer_panel_allow_cancel"<?php print Helper::getOption('customer_panel_allow_cancel', 'on')=='on'?' checked':''?>>
									<label class="fs_onoffswitch-label" for="input_customer_panel_allow_cancel"></label>
								</div>
							</div>
						</div>
					</div>

					<div class="form-row">
						<div class="form-group col-md-6">
							<div class="form-control-checkbox">
								<label for="input_customer_panel_allow_delete_account"><?php print bkntc__('Allow customers to delete their account')?>:</label>
								<div class="fs_onoffswitch">
									<input type="checkbox" class="fs_onoffswitch-checkbox" id="input_customer_panel_allow_delete_account"<?php print Helper::getOption('customer_panel_allow_delete_account', 'on')=='on'?' checked':''?>>
									<label class="fs_onoffswitch-label" for="input_customer_panel_allow_delete_account"></label>
								</div>
							</div>
						</div>
					</div>

				</div>

			</form>

		</div>
	</div>
</div>