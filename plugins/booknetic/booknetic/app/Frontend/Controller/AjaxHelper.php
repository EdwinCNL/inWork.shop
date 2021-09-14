<?php

namespace BookneticApp\Frontend\Controller;

use BookneticApp\Backend\Appointments\Helpers\AppointmentService;
use BookneticApp\Backend\Customers\Model\Customer;
use BookneticApp\Backend\Customforms\Model\FormInput;
use BookneticApp\Backend\Emailnotifications\Helpers\SendEmail;
use BookneticApp\Backend\Services\Model\ServiceExtra;
use BookneticApp\Backend\Smsnotifications\Helpers\SendSMS;
use BookneticApp\Backend\Whatsappnotifications\Helpers\SendMessage;
use BookneticApp\Providers\Curl;
use BookneticApp\Providers\Date;
use BookneticApp\Providers\DB;
use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Permission;

class AjaxHelper
{

	/**
	 * If the Customer is new, create it. And return the Customer ID.
	 *
	 * @param $customer_data
	 *
	 * @return array
	 */
	public static function getOrCreateCustomer( $customer_data )
	{
		$repeatCustomer = false;
		$wpUserId = Permission::userId();

		if( $wpUserId > 0 )
		{
			$checkCustomerExists = Customer::where('user_id', $wpUserId)->fetch();
			if(
				$checkCustomerExists
				&& !( !empty( $checkCustomerExists->email ) && $checkCustomerExists->email != $customer_data['email'] )
				&& !( !empty( $checkCustomerExists->phone_number ) && $checkCustomerExists->phone_number != $customer_data['phone'] )
			)
			{
				$repeatCustomer = true;
				$customerId = $checkCustomerExists->id;
			}
		}

		if( !$repeatCustomer && !( empty( $customer_data['phone'] ) && empty( $customer_data['email'] ) ) )
		{
			$checkCustomerExists = Customer::where('email', $customer_data['email'])->where('phone_number', $customer_data['phone'])->fetch();
			if( $checkCustomerExists )
			{
				$repeatCustomer = true;
				$customerId = $checkCustomerExists->id;
			}
		}

		if( !$repeatCustomer )
		{
			$customerWPUserId = $wpUserId > 0 ? $wpUserId : null;

			if( Helper::getOption('customer_panel_enable', 'off', false) == 'on' && !empty( $customer_data['email'] ) )
			{
				if( is_null( $customerWPUserId )  )
				{
					$userRandomPass = wp_generate_password( 8, false );

					$customerWPUserId = wp_insert_user( [
						'user_login'	=>	$customer_data['email'],
						'user_email'	=>	$customer_data['email'],
						'display_name'	=>	$customer_data['first_name'] . ' ' . $customer_data['last_name'],
						'first_name'	=>	$customer_data['first_name'],
						'last_name'		=>	$customer_data['last_name'],
						'role'			=>	'booknetic_customer',
						'user_pass'		=>	$userRandomPass
					] );

					if( is_wp_error( $customerWPUserId ) )
					{
						$customerWPUserId = null;
					}
					else if( !empty( $customer_data['phone'] ) )
					{
						add_user_meta( $customerWPUserId, 'billing_phone', $customer_data['phone'], true );
					}
				}
				else
				{
					$userInfo = wp_get_current_user();

					if( ! Helper::checkUserRole( $userInfo, [ 'administrator', 'booknetic_customer', 'booknetic_staff' ] ) )
					{
						$userInfo->set_role('booknetic_customer');
					}
				}
			}

			Customer::insert( [
				'user_id'		=>	$customerWPUserId,
				'first_name'	=>	$customer_data['first_name'],
				'last_name'		=>	$customer_data['last_name'],
				'phone_number'	=>	$customer_data['phone'],
				'email'			=>	$customer_data['email']
			] );

			$customerId = DB::lastInsertedId();
		}
		else if( isset( $checkCustomerExists ) && ( $checkCustomerExists->first_name != $customer_data['first_name'] || $checkCustomerExists->last_name != $customer_data['last_name'] ) )
		{
			$newFirstName = empty( $customer_data['first_name'] ) ? $checkCustomerExists->first_name : $customer_data['first_name'];
			$newLastName = empty( $customer_data['last_name'] ) ? $checkCustomerExists->last_name : $customer_data['last_name'];

			Customer::where('id', $checkCustomerExists->id)->update([
				'first_name'    =>  $newFirstName,
				'last_name'     =>  $newLastName
			]);

			if( $checkCustomerExists->user_id > 0 )
			{
				$updateData = [];

				$updateData['display_name'] = trim( $newFirstName . ' ' . $newLastName );
				$updateData['first_name'] = $newFirstName;
				$updateData['last_name'] = $newLastName;
				$updateData['ID'] = $checkCustomerExists->user_id;

				wp_update_user( $updateData );
			}
		}

		return [
			'customer_id'       => $customerId,
			'is_new_customer'   => isset( $userRandomPass ),
			'new_customer_pass' => isset( $userRandomPass ) ? $userRandomPass : null
		];
	}

	public static function sendNotificationsForCP( $appointmentId, $customerId, $newCustomerPass )
	{
		$sendMail = new SendEmail( 'cp_access' );
		$sendMail->setID( $appointmentId )
		         ->setCustomer( $customerId )
		         ->setPassword( $newCustomerPass )
		         ->send();

		$sendSMS = new SendSMS( 'cp_access' );
		$sendSMS->setID( $appointmentId )
		        ->setCustomer( $customerId )
		        ->setPassword( $newCustomerPass )
		        ->send();

		$sendWPMessage = new SendMessage( 'cp_access' );
		$sendWPMessage->setID( $appointmentId )
		              ->setCustomer( $customerId )
		              ->setPassword( $newCustomerPass )
		              ->send();
	}

	public static function handleCustomData( $custom_fields, $customer_data, $customFiles, $service )
	{
		$customer_inputs = ['first_name', 'last_name', 'email', 'phone'];

		foreach ( $customer_inputs AS $required_input_name )
		{
			if( !isset( $customer_data[ $required_input_name ] ) || !is_string( $customer_data[ $required_input_name ] ) )
			{
				Helper::response(false, bkntc__('Please fill in all required fields correctly!'));
			}
		}

		foreach ( $customer_data AS $input_name => $customer_datum )
		{
			if( !((in_array( $input_name, $customer_inputs ) || ( strpos( $input_name, 'custom_field' ) === 0)) && is_string($customer_datum)) )
			{
				Helper::response(false, bkntc__('Please fill in all required fields correctly!'));
			}
		}

		$getFormId = DB::DB()->get_row( DB::DB()->prepare( 'SELECT id FROM `'.DB::table('forms').'` WHERE FIND_IN_SET(%d, service_ids) '.DB::tenantFilter().' LIMIT 0,1', [ $service ] ), ARRAY_A );
		if( $getFormId )
		{
			$curFormId = (int)$getFormId['id'];

			$getRequiredFilesFields = FormInput::where('is_required', '1')->where('form_id', $curFormId)->where('type', 'file')->fetchAll();
			foreach ( $getRequiredFilesFields AS $fieldInf )
			{
				if( !isset( $customFiles[ $fieldInf['id'] ] ) )
				{
					Helper::response(false, bkntc__('Please fill in all required fields correctly!', [ htmlspecialchars( $fieldInf['label'] ) ]));
				}
			}
		}

		foreach ( $custom_fields AS $field_id => $value )
		{
			if( !( is_numeric($field_id) && $field_id > 0 && is_string( $value ) ) )
			{
				Helper::response(false, bkntc__('Please fill in all required fields correctly!'));
			}

			$customFieldInf = FormInput::get( $field_id );

			if( !$customFieldInf )
			{
				Helper::response(false, bkntc__('Please fill in all required fields correctly!'));
			}

			if( $customFieldInf['type'] == 'file' )
			{
				continue;
			}

			$isRequired = (int)$customFieldInf['is_required'];

			if( $isRequired && empty( $value ) )
			{
				Helper::response(false, bkntc__('Please fill in all required fields correctly!', [ htmlspecialchars( $customFieldInf['label'] ) ]));
			}

			$options = $customFieldInf['options'];
			$options = json_decode( $options, true );

			if( isset( $options['min_length'] ) && is_numeric( $options['min_length'] ) && $options['min_length'] > 0 && !empty( $value ) && mb_strlen( $value, 'UTF-8' ) < $options['min_length'] )
			{
				Helper::response(false, bkntc__('Minimum length of "%s" field is %d!', [ htmlspecialchars( $customFieldInf['label'] ) , (int)$options['min_length'] ]));
			}

			if( isset( $options['max_length'] ) && is_numeric( $options['max_length'] ) && $options['max_length'] > 0 && mb_strlen( $value, 'UTF-8' ) > $options['max_length'] )
			{
				Helper::response(false, bkntc__('Maximum length of "%s" field is %d!', [ htmlspecialchars( $customFieldInf['label'] ) , (int)$options['max_length'] ]));
			}

		}

		foreach( $customFiles AS $field_id => $value )
		{
			if( !( is_numeric($field_id) && $field_id > 0 && is_string( $value ) ) )
			{
				Helper::response(false, bkntc__('Please fill in all required fields correctly!'));
			}

			$customFieldInf = FormInput::get( $field_id );

			if( !$customFieldInf || $customFieldInf['type'] != 'file' )
			{
				Helper::response(false, bkntc__('Please fill in all required fields correctly!'));
			}

			$isRequired = (int)$customFieldInf['is_required'];
			$options = json_decode( $customFieldInf['options'], true );

			if( isset( $options['allowed_file_formats'] ) && !empty( $options['allowed_file_formats'] ) && is_string( $options['allowed_file_formats'] ) )
			{
				$allowedFileFormats = Helper::secureFileFormats( explode(',', str_replace(' ', '', $options['allowed_file_formats'])) );
			}
			else
			{
				$allowedFileFormats = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png', 'gif', 'mp4', 'zip', 'rar', 'csv'];
			}

			if( $isRequired && empty( $value ) )
			{
				Helper::response(false, bkntc__('Please fill in all required fields correctly!', [ htmlspecialchars( $customFieldInf['label'] ) ]));
			}

			$customFileName = $_FILES['custom_fields']['name'][ $field_id ];
			$extension = strtolower( pathinfo($customFileName, PATHINFO_EXTENSION) );

			if( !in_array( $extension, $allowedFileFormats ) )
			{
				Helper::response(false, bkntc__('File extension is not allowed!' ));
			}
		}
	}

	public static function handleServiceExtras( $service_extras, $service )
	{
		$extras_arr		= [];
		$extras_price	= 0;
		$extras_drtn	= 0;

		foreach ( $service_extras AS $extra_id => $quantity )
		{
			if( !(is_numeric($quantity) && $quantity > 0) )
				continue;

			$extra_inf = ServiceExtra::where('service_id', $service)->where('id', $extra_id)->fetch();

			if( $extra_inf && $extra_inf['max_quantity'] >= $quantity )
			{
				$extra_inf['quantity'] = $quantity;
				$extra_inf['customer'] = 0;

				$extras_arr[]   = $extra_inf;
				$extras_price   += $extra_inf['price'] * $quantity;
				$extras_drtn    += $extra_inf['duration'] * $quantity;
			}
		}

		return [
			'extras_arr'    =>  $extras_arr,
			'extras_price'  =>  $extras_price,
			'extras_drtn'   =>  $extras_drtn
		];
	}

	public static function findAnAvailableStaff( $serviceInf, $location, $extras_arr, $date, $time, $appointmentsParam )
	{
		$staff = -1;

		$availableStaffIDs = AppointmentService::staffByService( $serviceInf['id'], $location, true, $date );
		$checkAppointments = [];

		if( $serviceInf['is_recurring'] )
		{
			foreach( $appointmentsParam AS $appointmentElement )
			{
				if(
				!(
					isset( $appointmentElement[0] ) && is_string( $appointmentElement[0] ) && Date::isValid( $appointmentElement[0] ) &&
					isset( $appointmentElement[1] ) && is_string( $appointmentElement[1] ) && Date::isValid( $appointmentElement[1] )
				)
				)
				{
					Helper::response(false, bkntc__('Please fill in all required fields correctly!'));
				}

				$checkAppointments[] = [ Date::dateSQL( $appointmentElement[0] ) , Date::timeSQL( $appointmentElement[1] ) ];
			}
		}
		else
		{
			$checkAppointments[] = [ $date , $time ];
		}

		foreach ( $availableStaffIDs AS $staffID )
		{
			$allRecurringTimesIsAvailable = true;

			foreach ( $checkAppointments AS $checkAppointmentDateTime )
			{
				if( !AppointmentService::checkStaffAvailability( $serviceInf['id'], $extras_arr, $staffID, $checkAppointmentDateTime[0], $checkAppointmentDateTime[1] ) )
				{
					$allRecurringTimesIsAvailable = false;
					break;
				}
			}

			if( $allRecurringTimesIsAvailable )
			{
				$staff = $staffID;
				break;
			}
		}

		return $staff;
	}

	public static function validateGoogleReCaptcha()
	{
		$googleRecaptchaOption = Helper::getOption('google_recaptcha', 'off', false);

		/**
		 * If the Google ReCaptcha setting has enabled...
		 */
		if( $googleRecaptchaOption == 'on' )
		{
			$google_site_key    = Helper::getOption('google_recaptcha_site_key', '', false);
			$google_secret_key  = Helper::getOption('google_recaptcha_secret_key', '', false);

			if( !empty( $google_site_key ) && !empty( $google_secret_key ) )
			{
				$google_recaptcha_token	    = Helper::_post('google_recaptcha_token', '', 'string');
				$google_recaptcha_action    = Helper::_post('google_recaptcha_action', '', 'string');

				if( empty( $google_recaptcha_token ) || empty( $google_recaptcha_action ) )
				{
					Helper::response( false, bkntc__('Please refresh the page and try again.') );
				}

				$checkToken = Curl::getURL( 'https://www.google.com/recaptcha/api/siteverify?secret=' . urlencode($google_secret_key) . '&response=' . urlencode($google_recaptcha_token) );
				$checkToken = json_decode( $checkToken, true );

				if( !($checkToken['success'] == '1' && $checkToken['action'] == $google_recaptcha_action && $checkToken['score'] >= 0.5) )
				{
					Helper::response( false, bkntc__('Please refresh the page and try again.') );
				}
			}
		}
	}

}