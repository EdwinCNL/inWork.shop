<?php
namespace BookneticApp\Frontend\view;

use BookneticApp\Integrations\WooCommerce\WCPaymentGateways;
use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Date;
use BookneticApp\Providers\Permission;

defined( 'ABSPATH' ) or die();

$gateways = [
	'stripe'		=>	[
		'title'			=>	bkntc__('Stripe'),
		'is_enabled'	=>	Helper::getOption('stripe_enable', 'off') == 'on'
	],
	'paypal'		=>	[
		'title'			=>	bkntc__('Paypal'),
		'is_enabled'	=>	Helper::getOption('paypal_enable', 'off') == 'on'
	],
	'local'			=>	[
		'title'			=>	bkntc__('Local payment'),
		'is_enabled'	=>	Helper::getOption('local_payment_enable', 'on') == 'on'
	],
	'woocommerce'	=>	[
		'title'			=>	Helper::isSaaSVersion() ? bkntc__('Other') : bkntc__('Woocommerce'),
		'is_enabled'	=>	Helper::getOption('woocommerce_enabled', 'off') == 'on'
	]
];

$gateways_order = Helper::getOption('payment_gateways_order', 'stripe,paypal,local,woocommerce');
$gateways_order = explode(',', $gateways_order);

if( !in_array( 'local', $gateways_order ) )
	$gateways_order[] = 'local';

if( !in_array( 'woocommerce', $gateways_order ) )
	$gateways_order[] = 'woocommerce';

if( Helper::isSaaSVersion() && Helper::getOption('allow_to_use_woocommerce_integration', 'off', false) == 'off' )
{
	unset( $gateways['woocommerce'] );
}

$WCPaymentGateweyService = new WCPaymentGateways();
$WCPaymentGateweyService->startReplacingNecessaryOptions();
?>
<div id="booknetic_settings_area">
	<link rel="stylesheet" href="<?php print Helper::assets('css/payment_gateways_settings.css', 'Settings')?>">
	<script type="application/javascript" src="<?php print Helper::assets('js/payment_gateways_settings.js', 'Settings')?>"></script>

	<div class="actions_panel clearfix">
		<button type="button" class="btn btn-lg btn-success settings-save-btn float-right"><i class="fa fa-check pr-2"></i> <?php print bkntc__('SAVE CHANGES')?></button>
	</div>

	<div class="settings-light-portlet">
		<div class="ms-title">
			<?php print bkntc__('Payments')?>
			<span class="ms-subtitle"><?php print bkntc__('Payment methods')?></span>
		</div>
		<div class="ms-content">

			<div class="step_settings_container">
				<div class="step_elements_list">
					<?php
					foreach ( $gateways_order AS $gateway )
					{
						if( !isset( $gateways[$gateway] ) )
							continue;

						$disabled = '';
						if( ( $gateway == 'paypal' || $gateway == 'stripe' ) && Helper::isSaaSVersion() && Permission::getPermission( $gateway ) == 'off' )
						{
							$disabled = ' disabled';
						}

						?>
						<div class="step_element" data-step-id="<?php print $gateway?>">
							<span class="drag_drop_helper"><img src="<?php print Helper::icon('drag-default.svg')?>"></span>
							<span><?php print $gateways[$gateway]['title']?></span>
							<div class="step_switch">
								<div class="fs_onoffswitch">
									<input type="checkbox" name="enable_gateway_<?php print $gateway?>" class="fs_onoffswitch-checkbox green_switch" id="enable_gateway_<?php print $gateway?>"<?php print ($gateways[$gateway]['is_enabled']?' checked':'') . $disabled; ?>>
									<label class="fs_onoffswitch-label" for="enable_gateway_<?php print $gateway?>"></label>
								</div>
							</div>
						</div>
						<?php
					}
					?>
				</div>
				<div class="step_elements_options dashed-border">
					<form id="booking_panel_settings_per_step" class="position-relative">

						<div class="hidden" data-step="paypal">

							<?php
							if( Helper::isSaaSVersion() && Permission::getPermission( 'paypal' ) == 'off' ):
								print Helper::renderView( 'Base.view.modal.permission_denied', [
									'no_close_btn'  => true,
									'text'          => bkntc__( 'You can\'t use Paypal with the %s plan. Please upgrade your plan to use Paypal.', [ esc_html( Permission::tenantInf()->plan()->fetch()->name ) ] )
								] );
							else:
							?>
								<div class="form-group col-md-12">
									<label for="input_paypal_mode"><?php print bkntc__('Mode')?>:</label>
									<select class="form-control" id="input_paypal_mode">
										<option value="sandbox" <?php print Helper::getOption('paypal_mode', 'sandbox')=='sandbox'?'selected':''?>><?php print bkntc__('Sandbox')?></option>
										<option value="live" <?php print Helper::getOption('paypal_mode', 'sandbox')=='live'?'selected':''?>><?php print bkntc__('Live')?></option>
									</select>
								</div>

								<div class="form-group col-md-12">
									<label for="input_paypal_client_id"><?php print bkntc__('Client ID')?>:</label>
									<input class="form-control" id="input_paypal_client_id" value="<?php print htmlspecialchars( Helper::getOption('paypal_client_id', '') )?>">
								</div>

								<div class="form-group col-md-12">
									<label for="input_paypal_client_secret"><?php print bkntc__('Client Secret')?>:</label>
									<input class="form-control" id="input_paypal_client_secret" value="<?php print htmlspecialchars( Helper::getOption('paypal_client_secret', '') )?>">
								</div>
							<?php endif; ?>

						</div>

						<div class="hidden" data-step="stripe">

							<?php
							if( Helper::isSaaSVersion() && Permission::getPermission( 'stripe' ) == 'off' ):
								print Helper::renderView( 'Base.view.modal.permission_denied', [
									'no_close_btn'  => true,
									'text'          => bkntc__( 'You can\'t use Stripe with the %s plan. Please upgrade your plan to use Stripe.', [ Permission::tenantInf()->plan()->fetch()->name ] )
								] );
							else:
								?>
								<div class="form-group col-md-12">
									<label for="input_stripe_client_id"><?php print bkntc__('Publishable key')?>:</label>
									<input class="form-control" id="input_stripe_client_id" value="<?php print htmlspecialchars( Helper::getOption('stripe_client_id', '') )?>">
								</div>

								<div class="form-group col-md-12">
									<label for="input_stripe_client_secret"><?php print bkntc__('Secret key')?>:</label>
									<input class="form-control" id="input_stripe_client_secret" value="<?php print htmlspecialchars( Helper::getOption('stripe_client_secret', '') )?>">
								</div>
							<?php endif; ?>

						</div>

						<div class="hidden" data-step="local">

							<span class="text-secondary"><?php print bkntc__('No settings found for this step.')?></span>

						</div>

						<div class="hidden" data-step="woocommerce">
							<?php if( !Helper::isSaaSVersion() ):?>
							<div class="form-group col-md-12">
								<div class="form-control-checkbox">
									<label for="input_woocommerce_skip_confirm_step"><?php print bkntc__('Skip the Confirmation step')?>: <i class="far fa-question-circle do_tooltip" data-content="<?php print bkntc__("If you use Deposit Payments or Coupons, then you can have a confirmation step for your customers. Otherwise, it will redirect to WooCommerce by automatically skipping this step.")?>"></i></label>
									<div class="fs_onoffswitch">
										<input type="checkbox" class="fs_onoffswitch-checkbox" id="input_woocommerce_skip_confirm_step"<?php print Helper::getOption('woocommerce_skip_confirm_step', 'on')=='on'?' checked':''?>>
										<label class="fs_onoffswitch-label" for="input_woocommerce_skip_confirm_step"></label>
									</div>
								</div>
							</div>

							<div class="form-group col-md-12">
								<label for="input_woocommerce_rediret_to"><?php print bkntc__('Redirect customer to')?>:</label>
								<select class="form-control" id="input_woocommerce_rediret_to">
									<option value="cart" <?php print Helper::getOption('woocommerce_rediret_to', 'cart')=='cart'?'selected':''?>><?php print bkntc__('Cart page')?></option>
									<option value="checkout" <?php print Helper::getOption('woocommerce_rediret_to', 'cart')=='checkout'?'selected':''?>><?php print bkntc__('Checkout page')?></option>
								</select>
							</div>

							<div class="form-group col-md-12">
								<label for="input_woocommerde_order_details"><?php print bkntc__('Woocommerce order details')?>:</label>
								<textarea class="form-control" id="input_woocommerde_order_details"><?php print htmlspecialchars( Helper::getOption('woocommerde_order_details', "Date: {appointment_date}\nTime: {appointment_start_time}\nStaff: {staff_name}") )?></textarea>
								<button type="button" class="btn btn-default btn-sm mt-2" data-load-modal="Settings.keywords_list"><?php print bkntc__('List of keywords')?> <i class="far fa-question-circle"></i></button>
							</div>
							<?php else:?>
								<?php if ( ! class_exists( 'woocommerce' ) ): ?>
									<span class="text-secondary"><?php print bkntc__('Other payment methods are not activated. Please contact the service provider.')?></span>
								<?php else: ?>
									<?php foreach ( $WCPaymentGateweyService->paymentGatewaysList() AS $paymentGateway ):?>
										<div class="row mb-3">
											<div class="col-md-9">
												<div class="form-control-checkbox">
													<label for="input_wc_payment_gateway_<?php print esc_html($paymentGateway->id)?>"><?php print esc_html( $paymentGateway->title )?></label>
													<div class="fs_onoffswitch">
														<input type="checkbox" class="fs_onoffswitch-checkbox woocommerce_payment_gateway_checkbox" data-id="<?php print esc_html($paymentGateway->id)?>" id="input_wc_payment_gateway_<?php print esc_html($paymentGateway->id)?>"<?php print $paymentGateway->enabled == 'enabled' || $paymentGateway->enabled == 'yes' ? ' checked' : ''?>>
														<label class="fs_onoffswitch-label" for="input_wc_payment_gateway_<?php print esc_html($paymentGateway->id)?>"></label>
													</div>
												</div>
											</div>
											<div class="col-md-3 d-flex align-items-center">
												<a href="<?php print admin_url('admin.php?page=booknetic&module=settings&action=woocommerce_gateway_settings&wc_payment_gateway_id=' . esc_html($paymentGateway->id))?>" style="" class="btn btn-default"><?php print bkntc__('Set up')?></a>
											</div>
										</div>
									<?php endforeach;?>
								<?php endif; ?>
							<?php endif;?>
						</div>

					</form>
				</div>
			</div>

		</div>
	</div>
</div>