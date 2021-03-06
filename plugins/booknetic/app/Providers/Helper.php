<?php

namespace BookneticApp\Providers;

use BookneticApp\Backend\Services\Model\ServiceCategory;

class Helper
{

	public static function secFormat( $seconds )
	{
		$weeks = floor($seconds /  ( 60 * 60 * 24 * 7 ) );

		$seconds = $seconds % ( 60 * 60 * 24 * 7 );

		$days = floor($seconds /  ( 60 * 60 * 24 ) );

		$seconds = $seconds % ( 60 * 60 * 24 );

		$hours = floor($seconds /  ( 60 * 60 ) );

		$seconds = $seconds % ( 60 * 60 );

		$minutes = floor($seconds / 60 );

		$seconds = $seconds % 60;

		if($weeks == 0)
        {
            $result = rtrim(
                ( $weeks > 0 ? $weeks . bkntc__('w').' ' : '' ) .
                ( $days > 0 ? $days . bkntc__('d').' ' : '' ) .
                ( $hours > 0 ? $hours . bkntc__('h').' ' : '' ) .
                ( $minutes > 0 ? $minutes . bkntc__('m').' ' : '' ) .
                ( $seconds > 0 ? $seconds . bkntc__('s').' ' : '' )
            );
        }
		else if($days)
        {
            $days += 7 * $weeks;
            $result = rtrim($days > 0 ? $days . bkntc__('d').' ' : '');
        }
		else if($weeks)
        {
            $result = rtrim($weeks > 0 ? $weeks . bkntc__('w').' ' : '');
        }

		return empty( $result ) ? '0' : $result;
	}

    public static function secFormatWithName( $seconds )
    {
        $weeks = floor($seconds /  ( 60 * 60 * 24 * 7 ) );

        $seconds = $seconds % ( 60 * 60 * 24 * 7 );

        $days = floor($seconds /  ( 60 * 60 * 24 ) );

        $seconds = $seconds % ( 60 * 60 * 24 );

        $hours = floor($seconds /  ( 60 * 60 ) );

        $seconds = $seconds % ( 60 * 60 );

        $minutes = floor($seconds / 60 );

        $seconds = $seconds % 60;

        if($weeks == 0) {
            $result = rtrim(
                ($weeks > 0 ? $weeks . ' ' . ($weeks == 1 ? bkntc__('week') : bkntc__('weeks')) . ' ' : '') .
                ($days > 0 ? $days . ' ' . ($days == 1 ? bkntc__('day') : bkntc__('days')) . ' ' : '') .
                ($hours > 0 ? $hours . ' ' . ($hours == 1 ? bkntc__('hour') : bkntc__('hours')) . ' ' : '') .
                ($minutes > 0 ? $minutes . ' ' . ($minutes == 1 ? bkntc__('minute') : bkntc__('minutes')) . ' ' : '') .
                ($seconds > 0 ? $seconds . ' ' . ($seconds == 1 ? bkntc__('second') : bkntc__('seconds')) . ' ' : '')
            );
        }
        else if($days)
        {
            $days += 7 * $weeks;
            $result = rtrim($days > 0 ? $days . ' ' . ($days == 1 ? bkntc__('day') : bkntc__('days')) . ' ' : '');
        }
        else if($weeks)
        {
            $result = rtrim($weeks > 0 ? $weeks . ' ' . ($weeks == 1 ? bkntc__('week') : bkntc__('weeks')) . ' ' : '');
        }

        return empty( $result ) ? '0' : $result;
    }

	public static function response($status , $arr = [] )
	{
		$arr = is_array($arr) ? $arr : ( is_string($arr) ? ['error_msg' => $arr] : [] );

		if( $status )
		{
			$arr['status'] = 'ok';
		}
		else
		{
			$arr['status'] = 'error';

			if( !isset($arr['error_msg']) )
			{
				$arr['error_msg'] = 'Error!';
			}

			if( self::isModal() )
			{
				$arr['status'] = 'ok';
				$arr['html'] = '
					<div class="fs-modal-body mt-5">
						<div class="text-center mt-5 text-secondary">' . $arr['error_msg'] . '</div>
					</div>
					<div class="fs-modal-footer">
						<button type="button" class="btn btn-lg btn-default" data-dismiss="modal">' . bkntc__('CLOSE') . '</button>
					</div>';
				unset($arr['error_msg']);
			}
		}

		print json_encode($arr);
		exit();
	}

	public static function isModal()
	{
		$isModalRequest = Helper::_post('_mn', false, 'int');

		return $isModalRequest === false ? false : true;
	}

	public static function _post( $key , $default = null , $check_type = null , $whiteList = [] )
	{
		$res = isset($_POST[$key]) ? $_POST[$key] : $default;

		if( !is_null( $check_type ) )
		{
			if( $check_type == 'num' || $check_type == 'int' || $check_type == 'integer' )
			{
				$res = is_numeric( $res ) ? (int)$res : $default;
			}
			else if($check_type == 'str' || $check_type == 'string')
			{
				$res = is_string( $res ) ? trim( stripslashes_deep((string)$res) ) : $default;
			}
			else if($check_type == 'arr' || $check_type == 'array')
			{
				$res = is_array( $res ) ? stripslashes_deep((array)$res) : $default;
			}
			else if($check_type == 'float')
			{
				$res = is_numeric( $res ) ? (float)$res : $default;
			}
			else if($check_type == 'email')
			{
				$res = is_string( $res ) && filter_var($res, FILTER_VALIDATE_EMAIL) !== false ? trim( (string)$res ) : $default;
			}
		}

		if( !empty( $whiteList ) && !in_array( $res , $whiteList ) )
		{
			$res = $default;
		}

		return $res;
	}

	public static function _get( $key , $default = null , $check_type = null , $whiteList = [] )
	{
		$res = isset($_GET[$key]) ? $_GET[$key] : $default;

		if( !is_null( $check_type ) )
		{
			if( $check_type == 'num' || $check_type == 'int' || $check_type == 'integer' )
			{
				$res = is_numeric( $res ) ? (int)$res : $default;
			}
			else if($check_type == 'str' || $check_type == 'string')
			{
				$res = is_string( $res ) ? trim( (string)$res ) : $default;
			}
			else if($check_type == 'arr' || $check_type == 'array')
			{
				$res = is_array( $res ) ? (array)$res : $default;
			}
			else if($check_type == 'float')
			{
				$res = is_numeric( $res ) ? (float)$res : $default;
			}
		}

		if( !empty( $whiteList ) && !in_array( $res , $whiteList ) )
		{
			$res = $default;
		}

		return $res;
	}

	public static function _any( $key , $default = null , $check_type = null , $whiteList = [] )
	{
		$res = isset($_REQUEST[$key]) ? $_REQUEST[$key] : $default;

		if( !is_null( $check_type ) )
		{
			if( $check_type == 'num' || $check_type == 'int' || $check_type == 'integer' )
			{
				$res = is_numeric( $res ) ? (int)$res : $default;
			}
			else if($check_type == 'str' || $check_type == 'string')
			{
				$res = is_string( $res ) ? trim( (string)$res ) : $default;
			}
			else if($check_type == 'arr' || $check_type == 'array')
			{
				$res = is_array( $res ) ? (array)$res : $default;
			}
			else if($check_type == 'float')
			{
				$res = is_numeric( $res ) ? (float)$res : $default;
			}
		}

		if( !empty( $whiteList ) && !in_array( $res , $whiteList ) )
		{
			$res = $default;
		}

		return $res;
	}

	public static function cutText( $text , $n = 35 )
	{
		return mb_strlen($text , 'UTF-8') > $n ? mb_substr($text , 0 , $n , 'UTF-8') . '...' : $text;
	}

	public static function checkRequirements()
	{
		if( !ini_get('allow_url_fopen') )
		{
			self::response(false , bkntc__( "\"allow_url_fopen\" disabled in your php.ini settings! Please actiavte id and try again!" ));
		}
	}

	public static function getVersion()
	{
		$plugin_data = get_file_data(__DIR__ . '/../../init.php' , array('Version' => 'Version') , false);

		return isset($plugin_data['Version']) ? $plugin_data['Version'] : '1.0.0';
	}

	public static function getInstalledVersion()
	{
		$ver = self::getOption('plugin_version' , '1.0.0', false);

		return ( $ver === '1' || empty($ver) ) ? '1.0.0' : $ver;
	}

	public static function fsDebug()
	{
		error_reporting(E_ALL);
		ini_set('display_errors' , 'on');
	}

	public static function assets( $url, $module = 'Base' )
	{
		if( preg_match('/\.(js|css)$/i', $url) )
		{
			$url .= '?v=' . self::getVersion();
		}

		if( $module == 'front-end' )
		{
			return rtrim(plugin_dir_url( __DIR__ ), '/') . '/Frontend/assets/' . ltrim($url, '/');
		}

		return rtrim(plugin_dir_url( __DIR__ ), '/') . '/Backend/' . urlencode( ucfirst($module) ) . '/assets/' . ltrim($url, '/');
	}

	public static function icon( $icon, $module = 'Base' )
	{
		if( $module == 'front-end' )
		{
			return rtrim(plugin_dir_url( __DIR__ ), '/') . '/Frontend/assets/icons/' . ltrim($icon, '/');
		}

		return rtrim(plugin_dir_url( __DIR__ ), '/') . '/Backend/' . urlencode( ucfirst($module) ) . '/assets/icons/' . ltrim($icon, '/');
	}

	public static function profileImage( $image, $module = 'Base' )
	{
		if( empty( $image ) )
		{
			return self::assets( 'images/no-photo.png', $module );
		}
		else if( $image === 'logo' )
		{
			return self::assets( 'images/logo-white.svg', $module );
		}
		else if( $image === 'logo-sm' )
		{
			return self::assets( 'images/logo-sm.svg', $module );
		}

		return self::uploadedFileURL( $image, $module );
	}

	public static function uploadedDirURL( $module )
	{
		$upload_dir	= wp_upload_dir();
		$upload_dir = $upload_dir['baseurl'] . '/booknetic/' . strtolower( $module ) . ( empty($module) ? '' : '/' );

		if( !is_dir( $upload_dir ) )
			wp_mkdir_p( $upload_dir );

		return $upload_dir;
	}

	public static function uploadedFileURL( $fileName, $module = 'Base' )
	{
		return self::uploadedDirURL( $module ) . basename( $fileName );
	}

	public static function uploadedDir( $module )
	{
		$upload_dir	= wp_upload_dir();
		$upload_dir = $upload_dir['basedir'] . '/booknetic/' . strtolower( $module ) . ( empty($module) ? '' : '/' );

		if( !is_dir( $upload_dir ) )
			wp_mkdir_p( $upload_dir );

		return $upload_dir;
	}

	public static function uploadedFile( $fileName, $module = 'Base' )
	{
		return self::uploadedDir( $module ) . basename( $fileName );
	}

	public static function getOption( $optionName, $default = null, $multi_tenant_option = true )
	{
		$prefix = 'bkntc_';

		if( Helper::isSaaSVersion() && $multi_tenant_option )
		{
			$tenant = Permission::tenantId();

			if( $tenant > 0 )
			{
				$prefix .= 't' . $tenant . '_';
			}

			if( $optionName == 'stripe_enable' && Permission::tenantId() > 0 && Permission::getPermission('stripe') == 'off' )
			{
				return 'off';
			}
			else if( $optionName == 'paypal_enable' && Permission::tenantId() > 0 && Permission::getPermission('paypal') == 'off' )
			{
				return 'off';
			}
			else if( $optionName == 'hide_coupon_section' && Permission::tenantId() > 0 && Permission::getPermission('coupons') == 'off' )
			{
				return 'on';
			}
			else if( $optionName == 'hide_giftcard_section' && Permission::tenantId() > 0 && Permission::getPermission('giftcards') != 'on' )
			{
				return 'on';
			}
			else if( $optionName == 'display_logo_on_booking_panel' && Permission::tenantId() > 0 && Permission::getPermission('upload_logo_to_booking_panel') == 'off' )
			{
				return 'off';
			}
		}
		else if( Helper::isSaaSVersion() )
		{
			if( $optionName == 'zoom_enable' && Permission::tenantId() > 0 && Permission::getPermission('zoom') == 'off' )
			{
				return 'off';
			}
			else if( $optionName == 'google_calendar_enable' && Permission::tenantId() > 0 && Permission::getPermission('google_calendar') == 'off' )
			{
				return 'off';
			}
		}

		return get_option( $prefix . $optionName, $default );
	}

    public static function getCustomerOption( $optionName, $default = null, $tenantId=0, $multi_tenant_option = true )
    {
        $prefix = 'bkntc_';

        if (Helper::isSaaSVersion() && $multi_tenant_option) {

            if ($tenantId > 0) {
                $prefix .= 't' . $tenantId . '_';
            }
        }
        return get_option( $prefix . $optionName, $default );
    }

	public static function setOption( $optionName, $optionValue, $multi_tenant_option = true, $autoLoad = null )
	{
		$prefix = 'bkntc_';

		if( Helper::isSaaSVersion() && $multi_tenant_option )
		{
			$tenant = Permission::tenantId();

			if( $tenant > 0 )
			{
				$prefix .= 't' . $tenant . '_';
			}
		}

		return update_option( $prefix . $optionName, $optionValue, $autoLoad );
	}

	public static function deleteOption( $optionName, $multi_tenant_option = true )
	{
		$prefix = 'bkntc_';

		if( Helper::isSaaSVersion() && $multi_tenant_option )
		{
			$tenant = Permission::tenantId();

			if( $tenant > 0 )
			{
				$prefix .= 't' . $tenant . '_';
			}
		}

		return delete_option( $prefix . $optionName );
	}

	public static function getBookingStepsOrder($recurringInfoStep = false)
	{
		$steps_order = Helper::getOption('steps_order', 'location,staff,service,service_extras,date_time,information');

		if( $recurringInfoStep )
		{
			$steps_order = ',' . $steps_order . ',';
			$steps_order = str_replace(',date_time,', ',date_time,recurring_info,', $steps_order);
			$steps_order = trim($steps_order, ',');
		}

		$steps_order = explode(',', $steps_order);

		if( $recurringInfoStep )
		{
			$requiredSteps = explode(',', 'location,staff,service,service_extras,date_time,information,recurring_info,confirm_details,finish');
		}
		else
		{
			$requiredSteps = explode(',', 'location,staff,service,service_extras,date_time,information,confirm_details,finish');
		}

		foreach ( $requiredSteps AS $requiredStep )
		{
			if( !in_array( $requiredStep, $steps_order ) )
			{
				$steps_order[] = $requiredStep;
			}
		}

		foreach ( $steps_order AS $key => $item )
		{
			if( !in_array( $item, $requiredSteps ) )
			{
				array_splice( $steps_order, $key, 1 );
			}
		}

		return $steps_order;
	}

	public static function profileCard( $name, $image, $email, $module, $line_break = false )
	{
		if( $line_break )
		{
			$pos = strpos($name, ' ');

			if ($pos !== false)
			{
				$name = substr_replace( htmlspecialchars( $name ), '<br>', $pos, 1);
			}
		}
		else
		{
			$name = htmlspecialchars( $name );
		}

		return '<div class="user_visit_card">
					<div class="circle_image"><img src="' . Helper::profileImage( $image , $module) . '"></div>
					<div class="user_visit_details">
						<span>' . $name . '</span>
						<span>' . htmlspecialchars( $email ) . '</span>
					</div>
				</div>';
	}

	public static function paymentMethod( $key )
	{
		switch( $key )
		{
			case "paypal":
				return bkntc__("PayPal");
				break;
			case "stripe":
				return bkntc__("Stripe");
				break;
			case "giftcard":
				return bkntc__("Giftcard");
				break;
			default:
				return bkntc__("On site");
		}
	}

	public static function appointmentStatus( $status )
	{
		switch( $status )
		{
			case 'approved':
				$color = 'success';
				$icon = 'fa fa-check';
				break;
			case 'pending':
			case 'waiting_for_payment':
				$color = 'warning';
				$icon = 'fa fa-clock';
				break;
			case 'canceled':
				$color = 'danger';
				$icon = 'fa fa-times';
				break;
			default:
				$color = 'default';
				$icon = 'fa fa-times-circle';
				break;
		}

		return [
			'icon'	=> $icon,
			'color'	=> $color
		];
	}

	public static function price( $price, $currency = null )
	{
		$scale				= self::getOption('price_number_of_decimals', '2');
		$priceNumberFormat	= self::getOption('price_number_format', '1');

		switch( $priceNumberFormat )
		{
			case '2':
				$decimalPoint		= '.';
				$thousandsSeparator	= ',';
				break;
			case '3':
				$decimalPoint		= ',';
				$thousandsSeparator	= ' ';
				break;
			case '4':
				$decimalPoint		= ',';
				$thousandsSeparator	= '.';
				break;
			default:
				$decimalPoint		= '.';
				$thousandsSeparator	= ' ';
				break;
		}

		$price = Math::floor( $price, $scale);

		$price = number_format($price, $scale, $decimalPoint, $thousandsSeparator);

		$currencyFormat	= self::getOption('currency_format', '1');
		$currency		= is_null( $currency ) ? self::currencySymbol() : $currency;

		switch ( $currencyFormat )
		{
			case '2':
				return $currency . ' ' . $price;
			case '3':
				return $price . $currency;
			case '4':
				return $price . ' ' . $currency;
			default:
				return $currency . $price;
		}
	}

	public static function currencySymbol( $currency = null )
	{
		$currency_symbol = Helper::getOption('currency_symbol', '');

		if( !empty( $currency_symbol ) )
		{
			return $currency_symbol;
		}

		$currency = is_null( $currency ) ? Helper::getOption('currency', 'USD') : $currency;

		$currencyInf = self::currencies( $currency );

		return isset($currencyInf['symbol']) ? $currencyInf['symbol'] : '$';
	}

	public static function currencies( $currency = null )
	{
		$currencies = [
			'USD' => [ 'name' => 'US Dollar', 'symbol' => '$'],
			'EUR' => [ 'name' => 'Euro', 'symbol' => '???'],
			'GBP' => [ 'name' => 'Pound Sterling', 'symbol' => '??'],
			'AFN' => [ 'name' => 'Afghani', 'symbol' => 'Af'],
			'DZD' => [ 'name' => 'Algerian Dinar', 'symbol' => '??.??'],
			'ARS' => [ 'name' => 'Argentine Peso', 'symbol' => '$'],
			'AMD' => [ 'name' => 'Armenian Dram', 'symbol' => '??'],
			'AWG' => [ 'name' => 'Aruban Guilder/Florin', 'symbol' => '??'],
			'AUD' => [ 'name' => 'Australian Dollar', 'symbol' => '$'],
			'AZN' => [ 'name' => 'Azerbaijani Manat', 'symbol' => 'AZN'],
			'BSD' => [ 'name' => 'Bahamian Dollar', 'symbol' => '$'],
			'BHD' => [ 'name' => 'Bahraini Dinar', 'symbol' => '??.??'],
			'THB' => [ 'name' => 'Baht', 'symbol' => '???'],
			'PAB' => [ 'name' => 'Balboa', 'symbol' => 'B/.'],
			'BBD' => [ 'name' => 'Barbados Dollar', 'symbol' => '$'],
			'BYN' => [ 'name' => 'Belarusian Ruble', 'symbol' => 'Br'],
			'BZD' => [ 'name' => 'Belize Dollar', 'symbol' => '$'],
			'BMD' => [ 'name' => 'Bermudian Dollar', 'symbol' => '$'],
			'VEF' => [ 'name' => 'Bolivar Fuerte', 'symbol' => 'Bs F'],
			'BOB' => [ 'name' => 'Boliviano', 'symbol' => 'Bs.'],
			'BRL' => [ 'name' => 'Brazilian Real', 'symbol' => 'R$'],
			'BND' => [ 'name' => 'Brunei Dollar', 'symbol' => '$'],
			'BGN' => [ 'name' => 'Bulgarian Lev', 'symbol' => '????'],
			'BIF' => [ 'name' => 'Burundi Franc', 'symbol' => '???'],
			'CAD' => [ 'name' => 'Canadian Dollar', 'symbol' => '$'],
			'CVE' => [ 'name' => 'Cape Verde Escudo', 'symbol' => '$'],
			'KYD' => [ 'name' => 'Cayman Islands Dollar', 'symbol' => '$'],
			'GHS' => [ 'name' => 'Cedi', 'symbol' => '???'],
			'XAF' => [ 'name' => 'CFA Franc BCEAO', 'symbol' => '???'],
			'XPF' => [ 'name' => 'CFP Franc', 'symbol' => '???'],
			'CLP' => [ 'name' => 'Chilean Peso', 'symbol' => '$'],
			'COP' => [ 'name' => 'Colombian Peso', 'symbol' => '$'],
			'CDF' => [ 'name' => 'Congolese Franc', 'symbol' => '???'],
			'NIO' => [ 'name' => 'Cordoba Oro', 'symbol' => 'C$'],
			'CRC' => [ 'name' => 'Costa Rican Colon', 'symbol' => '???'],
			'HRK' => [ 'name' => 'Croatian Kuna', 'symbol' => 'Kn'],
			'CUP' => [ 'name' => 'Cuban Peso', 'symbol' => '$'],
			'CZK' => [ 'name' => 'Czech Koruna', 'symbol' => 'K??'],
			'GMD' => [ 'name' => 'Dalasi', 'symbol' => 'D'],
			'DKK' => [ 'name' => 'Danish Krone', 'symbol' => 'kr'],
			'MKD' => [ 'name' => 'Denar', 'symbol' => '??????'],
			'DJF' => [ 'name' => 'Djibouti Franc', 'symbol' => '???'],
			'STN' => [ 'name' => 'Dobra', 'symbol' => 'Db'],
			'DOP' => [ 'name' => 'Dominican Peso', 'symbol' => '$'],
			'VND' => [ 'name' => 'Dong', 'symbol' => '???'],
			'XCD' => [ 'name' => 'East Caribbean Dollar', 'symbol' => '$'],
			'EGP' => [ 'name' => 'Egyptian Pound', 'symbol' => '??'],
			'ETB' => [ 'name' => 'Ethiopian Birr', 'symbol' => 'ETB'],
			'FKP' => [ 'name' => 'Falkland Islands Pound', 'symbol' => '??'],
			'FJD' => [ 'name' => 'Fiji Dollar', 'symbol' => '$'],
			'HUF' => [ 'name' => 'Forint', 'symbol' => 'Ft'],
			'GIP' => [ 'name' => 'Gibraltar Pound', 'symbol' => '??'],
			'HTG' => [ 'name' => 'Gourde', 'symbol' => 'G'],
			'PYG' => [ 'name' => 'Guarani', 'symbol' => '???'],
			'GNF' => [ 'name' => 'Guinea Franc', 'symbol' => '???'],
			'GYD' => [ 'name' => 'Guyana Dollar', 'symbol' => '$'],
			'HKD' => [ 'name' => 'Hong Kong Dollar', 'symbol' => '$'],
			'UAH' => [ 'name' => 'Hryvnia', 'symbol' => '???'],
			'ISK' => [ 'name' => 'Iceland Krona', 'symbol' => 'Kr'],
			'INR' => [ 'name' => 'Indian Rupee', 'symbol' => '???'],
			'IRR' => [ 'name' => 'Iranian Rial', 'symbol' => '???'],
			'IQD' => [ 'name' => 'Iraqi Dinar', 'symbol' => '??.??'],
			'JMD' => [ 'name' => 'Jamaican Dollar', 'symbol' => '$'],
			'JOD' => [ 'name' => 'Jordanian Dinar', 'symbol' => '??.??'],
			'KES' => [ 'name' => 'Kenyan Shilling', 'symbol' => 'Sh'],
			'PGK' => [ 'name' => 'Kina', 'symbol' => 'K'],
			'LAK' => [ 'name' => 'Kip', 'symbol' => '???'],
			'BAM' => [ 'name' => 'Konvertibilna Marka', 'symbol' => '????'],
			'KWD' => [ 'name' => 'Kuwaiti Dinar', 'symbol' => '??.??'],
			'MWK' => [ 'name' => 'Kwacha', 'symbol' => 'MK'],
			'AOA' => [ 'name' => 'Kwanza', 'symbol' => 'Kz'],
			'MMK' => [ 'name' => 'Kyat', 'symbol' => 'K'],
			'GEL' => [ 'name' => 'Lari', 'symbol' => '???'],
			'LBP' => [ 'name' => 'Lebanese Pound', 'symbol' => '??.??'],
			'ALL' => [ 'name' => 'Lek', 'symbol' => 'L'],
			'HNL' => [ 'name' => 'Lempira', 'symbol' => 'L'],
			'SLL' => [ 'name' => 'Leone', 'symbol' => 'Le'],
			'RON' => [ 'name' => 'Leu', 'symbol' => 'L'],
			'LRD' => [ 'name' => 'Liberian Dollar', 'symbol' => '$'],
			'LYD' => [ 'name' => 'Libyan Dinar', 'symbol' => '??.??'],
			'SZL' => [ 'name' => 'Lilangeni', 'symbol' => 'L'],
			'LSL' => [ 'name' => 'Loti', 'symbol' => 'L'],
			'MGA' => [ 'name' => 'Malagasy Ariary', 'symbol' => 'MGA'],
			'MYR' => [ 'name' => 'Malaysian Ringgit', 'symbol' => 'RM'],
			'TMT' => [ 'name' => 'Manat', 'symbol' => 'm'],
			'MUR' => [ 'name' => 'Mauritius Rupee', 'symbol' => '???'],
			'MZN' => [ 'name' => 'Metical', 'symbol' => 'MTn'],
			'MXN' => [ 'name' => 'Mexican Peso', 'symbol' => '$'],
			'MDL' => [ 'name' => 'Moldovan Leu', 'symbol' => 'L'],
			'MAD' => [ 'name' => 'Moroccan Dirham', 'symbol' => '??.??.'],
			'NGN' => [ 'name' => 'Naira', 'symbol' => '???'],
			'ERN' => [ 'name' => 'Nakfa', 'symbol' => 'Nfk'],
			'NAD' => [ 'name' => 'Namibia Dollar', 'symbol' => '$'],
			'NPR' => [ 'name' => 'Nepalese Rupee', 'symbol' => '???'],
			'ILS' => [ 'name' => 'New Israeli Shekel', 'symbol' => '???'],
			'NZD' => [ 'name' => 'New Zealand Dollar', 'symbol' => '$'],
			'BTN' => [ 'name' => 'Ngultrum', 'symbol' => 'BTN'],
			'KPW' => [ 'name' => 'North Korean Won', 'symbol' => '???'],
			'NOK' => [ 'name' => 'Norwegian Krone', 'symbol' => 'kr'],
			'PEN' => [ 'name' => 'Nuevo Sol', 'symbol' => 'S/.'],
			'MRU' => [ 'name' => 'Ouguiya', 'symbol' => 'UM'],
			'TOP' => [ 'name' => 'Pa???anga', 'symbol' => 'T$'],
			'PKR' => [ 'name' => 'Pakistan Rupee', 'symbol' => '???'],
			'MOP' => [ 'name' => 'Pataca', 'symbol' => 'P'],
			'UYU' => [ 'name' => 'Peso Uruguayo', 'symbol' => 'UYU'],
			'PHP' => [ 'name' => 'Philippine Peso', 'symbol' => '???'],
			'BWP' => [ 'name' => 'Pula', 'symbol' => 'P'],
			'PLN' => [ 'name' => 'PZloty', 'symbol' => 'z??'],
			'QAR' => [ 'name' => 'Qatari Rial', 'symbol' => '??.??'],
			'GTQ' => [ 'name' => 'Quetzal', 'symbol' => 'Q'],
			'ZAR' => [ 'name' => 'Rand', 'symbol' => 'R'],
			'OMR' => [ 'name' => 'Rial Omani', 'symbol' => '??.??.'],
			'KHR' => [ 'name' => 'Riel', 'symbol' => '???'],
			'MVR' => [ 'name' => 'Rufiyaa', 'symbol' => '??.'],
			'IDR' => [ 'name' => 'Rupiah', 'symbol' => 'Rp'],
			'RUB' => [ 'name' => 'Russian Ruble', 'symbol' => '??.'],
			'RWF' => [ 'name' => 'Rwanda Franc', 'symbol' => '???'],
			'SHP' => [ 'name' => 'Saint Helena Pound', 'symbol' => '??'],
			'SAR' => [ 'name' => 'Saudi Riyal', 'symbol' => '??.??'],
			'RSD' => [ 'name' => 'Serbian Dinar', 'symbol' => 'din'],
			'SCR' => [ 'name' => 'Seychelles Rupee', 'symbol' => '???'],
			'SGD' => [ 'name' => 'Singapore Dollar', 'symbol' => '$'],
			'SBD' => [ 'name' => 'Solomon Islands Dollar', 'symbol' => '$'],
			'KGS' => [ 'name' => 'Som', 'symbol' => 'KGS'],
			'SOS' => [ 'name' => 'Somali Shilling', 'symbol' => 'Sh'],
			'TJS' => [ 'name' => 'Somoni', 'symbol' => '????'],
			'KRW' => [ 'name' => 'South Korean Won', 'symbol' => '???'],
			'LKR' => [ 'name' => 'Sri Lanka Rupee', 'symbol' => 'Rs'],
			'SDG' => [ 'name' => 'Sudanese Pound', 'symbol' => '??'],
			'SRD' => [ 'name' => 'Suriname Dollar', 'symbol' => '$'],
			'SEK' => [ 'name' => 'Swedish Krona', 'symbol' => 'kr'],
			'CHF' => [ 'name' => 'Swiss Franc', 'symbol' => '???'],
			'SYP' => [ 'name' => 'Syrian Pound', 'symbol' => '??.??'],
			'TWD' => [ 'name' => 'Taiwan Dollar', 'symbol' => '$'],
			'BDT' => [ 'name' => 'Taka', 'symbol' => '???'],
			'WST' => [ 'name' => 'Tala', 'symbol' => 'T'],
			'TZS' => [ 'name' => 'Tanzanian Shilling', 'symbol' => 'Sh'],
			'KZT' => [ 'name' => 'Tenge', 'symbol' => '???'],
			'TTD' => [ 'name' => 'Trinidad and Tobago Dollar', 'symbol' => '$'],
			'MNT' => [ 'name' => 'Tugrik', 'symbol' => '???'],
			'TND' => [ 'name' => 'Tunisian Dinar', 'symbol' => '??.??'],
			'TRY' => [ 'name' => 'Turkish Lira', 'symbol' => '???'],
			'AED' => [ 'name' => 'UAE Dirham', 'symbol' => '??.??'],
			'UGX' => [ 'name' => 'Uganda Shilling', 'symbol' => 'Sh'],
			'UZS' => [ 'name' => 'Uzbekistan Sum', 'symbol' => 'UZS'],
			'VUV' => [ 'name' => 'Vatu', 'symbol' => 'Vt'],
			'YER' => [ 'name' => 'Yemeni Rial', 'symbol' => '???'],
			'JPY' => [ 'name' => 'Yen', 'symbol' => '??'],
			'CNY' => [ 'name' => 'Yuan', 'symbol' => '??'],
			'ZMW' => [ 'name' => 'Zambian Kwacha', 'symbol' => 'ZK'],
			'ZWL' => [ 'name' => 'Zimbabwe Dollar', 'symbol' => '$']
		];

		if( is_null( $currency ) )
		{
			return $currencies;
		}
		else
		{
			return isset( $currencies[ $currency ] ) ? $currencies[ $currency ] : false;
		}
	}

	public static function assocByKey( $array, $key, $multipleData = false )
	{
		$newArr = [];
		$array = is_array( $array ) ? $array : [];

		foreach ( $array AS $data )
		{
			$keyValue = isset( $data[ $key ] ) ? $data[ $key ] : '-';

			// filters...
			if( $key == 'date' )
			{
				$keyValue = $keyValue == '-' ? '-' : Date::dateSQL( $keyValue );
			}

			if( $multipleData )
			{
				if( !isset($newArr[ $keyValue ]) )
				{
					$newArr[ $keyValue ] = [];
				}

				$newArr[ $keyValue ][] = $data;
			}
			else
			{
				$newArr[ $keyValue ] = $data;
			}
		}

		return $newArr;
	}

	public static function pluginTables()
	{
		return [
			'appearance',
			'appointment_custom_data',
			'appointment_customers',
			'appointment_extras',
			'appointments',
			'coupons',
			'customers',
			'form_input_choices',
			'form_inputs',
			'forms',
			'holidays',
			'locations',
			'notifications',
			'service_categories',
			'service_extras',
			'service_staff',
			'special_days',
			'timesheet',
			'services',
			'staff'
		];
	}

	public static function uninstallPlugin()
	{
		$purchaseKey = self::getOption('purchase_code' , '', false);

		$checkPurchaseCodeURL = Backend::API_URL . "?act=delete&purchase_code=" . urlencode( $purchaseKey ) . "&domain=" . site_url();

		wp_remote_get( $checkPurchaseCodeURL );

		// drop tables...
		$deleteTables = self::pluginTables();

		foreach( $deleteTables AS $tableName )
		{
			DB::DB()->query("DROP TABLE IF EXISTS `" . DB::table( $tableName ) . "`");
		}

		// delete options...
		DB::DB()->query('DELETE FROM `'.DB::DB()->base_prefix.'options` WHERE `option_name` LIKE \'bkntc_%\'');
	}

	public static function redirect( $url )
	{
		header('Location: ' . $url);
		exit();
	}

	public static function getAllSubCategories( $category )
	{
		$arr = [ (int)$category ];

		$subCategories = ServiceCategory::where('parent_id', $category)->fetchAll();
		foreach ( $subCategories AS $subCategory )
		{
			$arr = array_merge( $arr, self::getAllSubCategories( $subCategory['id'] ) );
		}

		return $arr;
	}

	public static function secureFileFormats( $formats )
	{
		$newFormats = [];
		foreach ( $formats AS $format )
		{
			$format = strtolower($format);

			if( !preg_match( '/^(php[0-9]*)|(htaccess)|(htpasswd)|(ini)$/', $format ) )
			{
				$newFormats[] = $format;
			}
		}

		return $newFormats;
	}

	public static function customerPanelURL()
	{
		$customerPanelPageID = Helper::getOption('customer_panel_page_id', '', false);

		if( empty( $customerPanelPageID ) )
			return '';

		return get_page_link( (int)$customerPanelPageID );
	}

	public static function isSaaSVersion()
	{
		return class_exists( '\BookneticSaaS\Providers\Bootstrap' );
	}

	public static function renderView( $view_path, $parameters = [] )
	{
		$viewsPath	= Backend::MODULES_DIR . str_replace( '.', DIRECTORY_SEPARATOR, $view_path ) . '.php';

		if( !file_exists( $viewsPath ) )
		{
			return bkntc__( 'View ( %s ) not found!', [ $view_path ] );
		}

		ob_start();
		require $viewsPath;
		$viewOutput = ob_get_clean();

		return $viewOutput;
	}

	public static function is_ajax()
	{
		return defined('DOING_AJAX') && DOING_AJAX;
	}

	public static function is_update_process()
	{
		$isUpdate = Helper::_post('action', '', 'string') == 'update-plugin';

		if( $isUpdate && Helper::_post('slug', '', 'string') == Backend::MENU_SLUG )
		{
			set_time_limit(150);
		}

		return $isUpdate;
	}

	public static function checkUserRole( $userInfo, $roles )
	{
		$roles = is_array( $roles ) ? $roles : (array)$roles;

		foreach( $roles AS $checkRole )
		{
			if( in_array( $checkRole, $userInfo->roles ) )
			{
				return true;
			}
		}

		return false;
	}


    public static function is_rtl_language( $tenant_id = 0, $is_backend = false, $session_lang = '' )
    {
        $default_language = '';

        if($is_backend && $session_lang != '')
        {
            $default_language = $session_lang;
        }
        else if (self::isSaaSVersion() && $tenant_id > 0)
        {
            $default_language = self::getOption('default_language' , 'en', $tenant_id);
        }
        else if( !self::isSaaSVersion() && is_rtl() )
        {
            return true;
        }
         $rtl_languages = [
                'ar',
                'ary',
                'azb',
                'ckb',
                'fa_AF',
                'fa_IR',
                'haz',
                'ps',
                'ug_CN',
                'ur',
                'he_IL'
            ];

            if( in_array( $default_language, $rtl_languages ) )
            {
                return true;
            }

        return false;
    }

}
