<?php

namespace BookneticApp\Frontend\Controller;

use BookneticApp\Backend\Appointments\Helpers\AppointmentService;
use BookneticApp\Backend\Appointments\Model\Appointment;
use BookneticApp\Backend\Appointments\Model\AppointmentCustomer;
use BookneticApp\Backend\Appointments\Model\AppointmentExtra;
use BookneticApp\Backend\Customers\Model\Customer;
use BookneticApp\Providers\Date;
use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Permission;

trait ClientPanelAjax
{

	public static function save_profile()
	{
		if( Helper::getOption('customer_panel_enable', 'off', false) != 'on' )
		{
			Helper::response( false );
		}

		$name		=	Helper::_post('name', '', 'str');
		$surname	=	Helper::_post('surname', '', 'str');
		$email		=	Helper::_post('email', '', 'str');
		$phone		=	Helper::_post('phone', '', 'str');
		$birthdate	=	Helper::_post('birthdate', '', 'str');
		$gender		=	Helper::_post('gender', '', 'str', ['', 'male', 'female']);

		if( !empty( $birthdate ) && !Date::isValid( $birthdate ) )
		{
			Helper::response( false );
		}

		$userId = Permission::userId();
		$customer = Customer::where('user_id', $userId)->noTenant()->fetch();

		if( !$customer )
			Helper::response( false );

		Customer::where('user_id', $userId)->noTenant()->update([
			'first_name'		=>	trim($name),
			'last_name'			=>	trim($surname),
			'phone_number'		=>	$phone,
			'email'				=>	$email,
			'gender'			=>	$gender,
			'birthdate'			=>	empty( $birthdate ) ? null : Date::dateSQL( $birthdate )
		]);

		Helper::response( true, ['message' => bkntc__('Profile data was saved successfully')] );
	}

	public static function change_password()
	{
		if( Helper::getOption('customer_panel_enable', 'off', false) != 'on' )
		{
			Helper::response( false );
		}

		$old_password			=	Helper::_post('old_password', '', 'str');
		$new_password			=	Helper::_post('new_password', '', 'str');
		$repeat_new_password	=	Helper::_post('repeat_new_password', '', 'str');

		if( $new_password != $repeat_new_password || empty( $new_password ) )
		{
			Helper::response( false, bkntc__('Password does not match!') );
		}

		$userId = Permission::userId();
		$customer = Customer::where('user_id', $userId)->noTenant()->fetch();

		if( !$customer )
			Helper::response( false );

		$userInf = get_user_by('id', $userId);
		if( $userInf && !wp_check_password( $old_password, $userInf->data->user_pass, $userId ) )
		{
			Helper::response( false, bkntc__('Current password is wrong!') );
		}

		wp_set_password( $new_password, $userId );

		Helper::response( true, ['message' => bkntc__('Password was changed successfully')] );
	}

	public static function reschedule_appointment()
	{

		$appointment_id		=	Helper::_post('id', '', 'int');
		$date				=	Helper::_post('date', '', 'str');
		$time				=	Helper::_post('time', '', 'str');

		if(Date::epoch() >= Date::epoch($date.' '.$time. ':00'))
        {
            Helper::response( false, bkntc__('You can not change the date and time to past.') );
        }

		$userId = Permission::userId();
		$customers = Customer::where('user_id', $userId)->noTenant()->fetchAll();

		if( empty( $customers ) )
		{
			Helper::response( false );
		}

		$customerIds = [];
		foreach ( $customers AS $customerInf )
		{
			$customerIds[] = $customerInf->id;
		}

		$appointmentCustomerInfo = AppointmentCustomer::noTenant()->get( $appointment_id );

		if( !$appointmentCustomerInfo || !in_array( $appointmentCustomerInfo->customer_id, $customerIds ) )
		{
			Helper::response( false );
		}

		$appointmentInfo = Appointment::noTenant()->get( $appointmentCustomerInfo->appointment_id );

        $minute = Helper::getCustomerOption('time_restriction_to_make_changes_on_appointments', '5', $appointmentInfo->tenant_id);
        $beforeThisTime = Helper::getOption('min_time_req_prior_booking', '5');

        if(Date::epoch('+'.$minute.' minutes') > Date::epoch($appointmentInfo->date.' '.$appointmentInfo->start_time))
        {
            Helper::response( false, bkntc__('Minimum time requirement prior to change the appointment date and time is %s', [Helper::secFormatWithName($minute*60)]) );
        }

        $before = Date::epoch('+'.$beforeThisTime.' minutes');

        if($before > Date::epoch($date.' '.$time.':00'))
        {
            Helper::response( false, bkntc__('You cannot change the appointment less than %s in advance', [Helper::secFormatWithName($beforeThisTime * 60)]));
        }

		if( $appointmentCustomerInfo->status != 'canceled' && Date::dateSQL($appointmentInfo->date) == Date::dateSQL($date) && Date::timeSQL($appointmentInfo->start_time) == Date::timeSQL($time) )
		{
			Helper::response( false, bkntc__('You have not changed the date and time.') );
		}

        if( Helper::isSaaSVersion() )
        {
            $tenantId = $appointmentInfo->tenant_id;
            Permission::setTenantId( $tenantId );
        }
        if( Helper::getOption('customer_panel_enable', 'off',false) != 'on' )
        {
            Helper::response( false );
        }

        if( Helper::getOption('customer_panel_allow_reschedule', 'on') != 'on' )
        {
            Helper::response( false );
        }

		$inf = AppointmentService::reschedule( $appointment_id, $date, $time );

		if( $inf['appointment_status'] == 'approved' )
		{
			$appointmentStatusText = bkntc__('Approved');
		}
		else if( $inf['appointment_status'] == 'pending' )
		{
			$appointmentStatusText = bkntc__('Pending');
		}
		else if( $inf['appointment_status'] == 'canceled' )
		{
			$appointmentStatusText = bkntc__('Canceled');
		}
		else
		{
			$appointmentStatusText = bkntc__('Rejected');
		}

		Helper::response( true, [
			'message' 	                =>  bkntc__('Appointment was rescheduled successfully!'),
			'datetime'	                =>  Date::dateTime( $date . ' ' . $time ),
			'appointment_status'        =>  $inf['appointment_status'],
			'appointment_status_text'   =>  $appointmentStatusText
		] );
	}

	public static function cancel_appointment()
	{


		$appointment_id =	Helper::_post('id', '', 'int');

		$userId = Permission::userId();
		$customers = Customer::where('user_id', $userId)->noTenant()->fetchAll();

		if( empty( $customers ) )
		{
			Helper::response( false );
		}

		$customerIds = [];
		foreach ( $customers AS $customerInf )
		{
			$customerIds[] = $customerInf->id;
		}

		$appointmentCustomerInfo = AppointmentCustomer::noTenant()
	                                      ->where('status', ['<>', 'canceled'])
	                                      ->where('status', ['<>', 'rejected'])
	                                      ->get( $appointment_id );

		if( !$appointmentCustomerInfo || !in_array( $appointmentCustomerInfo->customer_id, $customerIds ) )
		{
			Helper::response( false );
		}

		$appointmentInfo = Appointment::noTenant()->get( $appointmentCustomerInfo->appointment_id );


        $minute = Helper::getCustomerOption('time_restriction_to_make_changes_on_appointments', '5', $appointmentInfo->tenant_id);

        if(Date::epoch('+'.$minute.' minutes') > Date::epoch($appointmentInfo->date.' '.$appointmentInfo->start_time))
        {
            Helper::response( false, bkntc__('Minimum time requirement prior to change the appointment date and time is %s', [Helper::secFormatWithName($minute*60)]) );
        }

		if( Helper::isSaaSVersion() )
		{
			$tenantId = $appointmentInfo->tenant_id;
			Permission::setTenantId( $tenantId );
		}
        if( Helper::getOption('customer_panel_enable', 'off',false) != 'on' )
        {
            Helper::response( false );
        }

        if( Helper::getOption('customer_panel_allow_cancel', 'on') != 'on' )
        {
            Helper::response( false );
        }

		AppointmentService::cancel( $appointment_id );

		Helper::response( true, [
			'message' 	                =>  bkntc__('You have canceled the appointment!'),
			'appointment_status'        =>  'canceled',
			'appointment_status_text'   =>  bkntc__('Canceled')
		] );
	}

	public static function get_available_times_of_appointment()
	{
		if( Helper::getOption('customer_panel_enable', 'off', false) != 'on' )
		{
			Helper::response( false );
		}



		$appointment_id	= Helper::_post('id', 0, 'int');
		$search			= Helper::_post('q', '', 'string');
		$date			= Helper::_post('date', '', 'string');

		$userId = Permission::userId();
		$customers = Customer::where('user_id', $userId)->noTenant()->fetchAll();

		if( empty( $customers ) )
		{
			Helper::response( false );
		}

		$customerIds = [];
		foreach ( $customers AS $customerInf )
		{
			$customerIds[] = $customerInf->id;
		}

		$appointmentCustomerInfo = AppointmentCustomer::noTenant()->get( $appointment_id );

		if( !$appointmentCustomerInfo || !in_array( $appointmentCustomerInfo->customer_id, $customerIds ) )
		{
			Helper::response( false );
		}

		$customer_id    = $appointmentCustomerInfo->customer_id;
		$appointmentInf	= Appointment::noTenant()->get( $appointmentCustomerInfo->appointment_id );
		$staff			= $appointmentInf->staff_id;
		$service		= $appointmentInf->service_id;

		if( Helper::isSaaSVersion() )
		{
			Permission::setTenantId( $appointmentInf->tenant_id );
		}
        if( Helper::getOption('customer_panel_allow_reschedule', 'on') != 'on' )
        {
            Helper::response( false );
        }

		$extras_arr = [];
		$appointmentExtras = AppointmentExtra::where('appointment_id', $appointmentCustomerInfo->appointment_id)->where('customer_id', $customer_id)->fetchAll();
		foreach ( $appointmentExtras AS $extra )
		{
			$extra_inf = $extra->extra()->fetch();
			$extra_inf['quantity'] = $extra['quantity'];
			$extra_inf['customer'] = $customer_id;

			$extras_arr[] = $extra_inf;
		}

		$dataForReturn = [];

		$appointmentCustomersCount = AppointmentCustomer::where('appointment_id', $appointmentCustomerInfo->appointment_id)->count();

		$data = AppointmentService::getCalendar( $staff, $service, 0, $extras_arr, $date, $date, true, ($appointmentCustomersCount > 1 ? null : $appointmentCustomerInfo->appointment_id) );
		$data = $data['dates'];

		if( isset( $data[ $date ] ) )
		{
			foreach ( $data[ $date ] AS $dataInf )
			{
				$startTime = $dataInf['start_time_format'];

				// search...
				if( !empty( $search ) && strpos( $startTime, $search ) === false )
				{
					continue;
				}

				$dataForReturn[] = [
					'id'					=>	$dataInf['start_time'],
					'text'					=>	$startTime . ( $dataInf['available_customers'] > 0 && $dataInf['max_capacity'] > 1 ? ' [ '.(int)$dataInf['available_customers'].'/'.(int)$dataInf['max_capacity'].' ]' : '' ),
					'min_capacity'			=>	$dataInf['min_capacity'],
					'max_capacity'			=>	$dataInf['max_capacity'],
					'available_customers'	=>	$dataInf['available_customers']
				];
			}
		}

		Helper::response(true, [ 'results' => $dataForReturn ]);
	}

	public static function delete_profile()
	{
		if( Helper::getOption('customer_panel_enable', 'off', false) != 'on' )
		{
			Helper::response( false );
		}

		if( Helper::getOption('customer_panel_allow_delete_account', 'on', false) != 'on' )
		{
			Helper::response( false );
		}

		$userId = Permission::userId();
		$customer = Customer::where('user_id', $userId)->noTenant()->fetch();

		if( !$customer )
			Helper::response( false );

		Customer::where('user_id', $userId)->noTenant()->update([
			'user_id'		=>	null,
			'first_name'	=>	'[-] ID: ' . $customer->id,
			'last_name'		=>	'',
			'phone_number'	=>	'',
			'email'			=>	'',
			'birthdate'		=>	null,
			'gender'		=>	'',
			'notes'			=>	'',
			'profile_image'	=>	''
		]);

		wp_logout();
		wp_delete_user( $userId );

		Helper::response( true, ['redirect_url' => site_url('/')] );
	}

}