<?php

namespace BookneticApp\Backend\Settings\Controller;

use BookneticApp\Integrations\WooCommerce\WCPaymentGateways;
use BookneticApp\Providers\Controller;
use BookneticApp\Providers\Curl;
use BookneticApp\Providers\DataTable;
use BookneticApp\Providers\DB;
use BookneticApp\Providers\Helper;

class Main extends Controller
{

	public function index()
	{
		$this->view( 'index' );
	}

	public function woocommerce_gateway_settings()
	{
		if( ! Helper::isSaaSVersion() )
			return;

		$WCSettingsHelper = new WCPaymentGateways();
		$WCSettingsHelper->initSettings();

		$this->view( 'wc_settings', [
			'inputs'    =>  $WCSettingsHelper->settingsForm(),
			'title'     =>  $WCSettingsHelper->paymentGateway()->title
		] );
	}

}
