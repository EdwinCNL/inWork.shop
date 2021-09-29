<?php

namespace BookneticApp\Integrations\WooCommerce;

use BookneticApp\Providers\Helper;

class WCPaymentGateways
{

	private $payment_gateway_id;
	private $updated_options = [];
	private $OPTION_PREFIX = 'WCS_';

	public function __construct()
	{

	}

	public function initSettings()
	{
		$this->payment_gateway_id = Helper::_get('wc_payment_gateway_id', '', 'string');
		$this->startReplacingNecessaryOptions();

		do_action( 'load-woocommerce_page_wc-settings' );

		// prevent locations to the Wordpress Admin panel...
		header_register_callback( [ $this, 'registerHeaderHook' ] );

		// Simulate WC settings page...
		set_current_screen('woocommerce_page_wc-settings');
		$_GET['page']           = 'wc-settings';
		$_GET['tab']            = 'checkout';
		$_GET['section']        = $this->payment_gateway_id;
		$_REQUEST['page']       = 'wc-settings';
		$_REQUEST['tab']        = 'checkout';
		$_REQUEST['section']    = $this->payment_gateway_id;

		$this->checkPaymentGatewayIsCorrect();

		$this->saveAction();
	}

	public function paymentGatewaysList()
	{
		if ( ! class_exists( 'woocommerce' ) )
		{
			return [];
		}

		$wc_payment_gateways = \WC_Payment_Gateways::instance();

		return $wc_payment_gateways->payment_gateways();
	}

	public function paymentGateway( $id = null )
	{
		if( is_null( $id ) )
		{
			$id = $this->payment_gateway_id;
		}

		$list = $this->paymentGatewaysList();
		if( ! isset( $list[ $id ] ) )
			return false;

		return $this->paymentGatewaysList()[ $id ];
	}

	public function saveAction()
	{
		$save_settings = Helper::_post('bkntc_save_settings', '', 'string', ['ok']);

		if( $save_settings === 'ok' )
		{
			$this->startCollectiongUpdatedOptions();

			// Save WooCommerce Settings...
			$settings = apply_filters( 'woocommerce_get_settings_' . $this->payment_gateway_id, [] );
			\WC_Admin_Settings::save_fields( $settings );

			do_action( 'woocommerce_update_options_payment_gateways_' . $this->payment_gateway_id );

			// Get collected options and save them according to thenant ID...
			$this->stopCollectiongUpdatedOptions();
			$this->saveCollectedOptions();

			wp_safe_redirect( admin_url( 'admin.php?page=booknetic&module=settings&action=woocommerce_gateway_settings&wc_payment_gateway_id=' . $this->payment_gateway_id ) );
			exit();
		}
	}

	public function settingsForm()
	{
		ob_start();

		$this->startReplacingNecessaryOptions();

		// Print settings form ( It's Woocommerce function ).
		$this->paymentGateway()->admin_options();

		// Get output for made some replaces...
		$settingInputs = ob_get_clean();

		// Delete WC urls
		$replaceStrFrom = 'admin.php?page=wc-settings&tab=checkout&section=';
		$replaceStrTo   = 'admin.php?page=booknetic&module=settings&action=woocommerce_gateway_settings&wc_payment_gateway_id=';

		$settingInputs  = str_replace(
			[ $replaceStrFrom, urlencode( $replaceStrFrom ), rawurlencode( $replaceStrFrom ), esc_html( $replaceStrFrom ) ],
			[ $replaceStrTo, urlencode( $replaceStrTo ), rawurlencode( $replaceStrTo ), esc_html( $replaceStrTo ) ],
			$settingInputs
		);

		return $settingInputs;
	}

	public function registerHeaderHook()
	{
		foreach ( headers_list() as $header)
		{
			if ( strpos( strtolower( $header ), 'location:') === 0 )
			{
				$redirectURL = trim( substr( $header, strlen('location:') ) );

				$adminURL = admin_url('admin.php');
				if( strpos( $redirectURL, $adminURL ) === 0 )
				{
					$redirectURL = admin_url( 'admin.php?page=booknetic&module=settings&action=woocommerce_gateway_settings&wc_payment_gateway_id=' . $this->payment_gateway_id );

					header_remove( 'location' );
					header('Location: ' . $redirectURL );
				}
			}
		}
	}

	/*
	 * Start collecting options that will update...
	 * For making a multi-tenant system we need to collect the updated options and then save them according to tenant ID.
	 * For example: Developer are store the settings in the "wc_ppec_settings" option. We will save it as: "bkntc_tenantID_wc_ppec_settings".
	 */
	public function startCollectiongUpdatedOptions()
	{
		add_filter('pre_update_option', [ $this, 'collectUpdatedOptions' ], 10, 3);
	}

	public function stopCollectiongUpdatedOptions()
	{
		remove_filter('pre_update_option', [ $this, 'collectUpdatedOptions' ], 10);
	}

	public function collectUpdatedOptions( $value, $option, $old_value )
	{
		$this->updated_options[ $option ] = $value;

		return $old_value;
	}

	public function saveCollectedOptions()
	{
		$alWCUpdatedOptionsList = Helper::getOption('wc_all_updated_options_list', [], false);

		foreach ( $this->updated_options AS $updated_option_key => $updated_option_value )
		{
			$alWCUpdatedOptionsList[] = $updated_option_key;
			Helper::setOption( $this->OPTION_PREFIX . $updated_option_key, $updated_option_value );
		}

		Helper::setOption( 'wc_all_updated_options_list', array_unique( $alWCUpdatedOptionsList ), false );
	}

	private function checkPaymentGatewayIsCorrect()
	{
		if( $this->paymentGateway() === false )
		{
			wp_safe_redirect( admin_url( 'admin.php?page=booknetic&module=settings' ) );
			exit();
		}
	}

	public function startReplacingNecessaryOptions()
	{
		$alWCUpdatedOptionsList = Helper::getOption('wc_all_updated_options_list', [], false);

		foreach ( $alWCUpdatedOptionsList AS $optionName )
		{
			add_filter( 'pre_option_' . $optionName, [ $this, 'replaceOptionAccordingToTenant' ], PHP_INT_MAX, 2 );
		}
	}

	public function stopReplacingNecessaryOptions()
	{
		$alWCUpdatedOptionsList = Helper::getOption('wc_all_updated_options_list', [], false);

		foreach ( $alWCUpdatedOptionsList AS $optionName )
		{
			remove_filter( 'pre_option_' . $optionName, [ $this, 'replaceOptionAccordingToTenant' ], PHP_INT_MAX );
		}
	}

	public function replaceOptionAccordingToTenant( $true, $option_name )
	{
		return Helper::getOption( $this->OPTION_PREFIX . $option_name );
	}

	public function changePaymentGatewayStatus( $id, $newStatus )
	{
		$paymentGateway = $this->paymentGateway( $id );

		if( $paymentGateway !== false )
		{
			$paymentGateway->update_option( 'enabled', ( $newStatus == 'on' ? 'yes' : 'no' ) );
		}

		return false;
	}

}