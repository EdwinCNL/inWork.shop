<?php

namespace BookneticApp\Backend\Emailnotifications\Helpers;

use BookneticApp\Backend\Appointments\Model\Appointment;
use BookneticApp\Backend\Appointments\Model\AppointmentCustomer;
use BookneticApp\Backend\Appointments\Model\AppointmentExtra;
use BookneticApp\Backend\Customers\Model\Customer;
use BookneticApp\Backend\Emailnotifications\Model\Notification;
use BookneticApp\Backend\Locations\Model\Location;
use BookneticApp\Backend\NotificationTab\Model\NotificationTab;
use BookneticApp\Backend\Services\Model\Service;
use BookneticApp\Backend\Services\Model\ServiceCategory;
use BookneticApp\Backend\Staff\Model\Staff;
use BookneticApp\Providers\Backend;
use BookneticApp\Providers\Controller;
use BookneticApp\Providers\Date;
use BookneticApp\Providers\DB;
use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Permission;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;

class SendEmail
{

	private $action;
	private $actionId;
	private $appointmentID;
	private $customerID;

	private $appointmentInf;
	private $appointmentCustomerInf;
	private $customerInf;
	private $staffInf;

	private $serviceInf;
	private $serviceCategoryInf;
	private $locationInf;
	private $preventStaffDublicateNotifications = [];
	private $userPassword;

	private $cacheFolder = '';
	private $attachments = [];

	public function __construct( $action )
	{
		$this->action = $action;
	}

	public function setID( $appointmentID )
	{
		$this->appointmentID = $appointmentID;

		return $this;
	}

	public function setCustomer( $customerId )
	{
		$this->customerID = $customerId;

		return $this;
	}

	public function setActionId( $actionId )
	{
		$this->actionId = $actionId;

		return $this;
	}

	public function setPassword( $password )
	{
		$this->userPassword = $password;

		return $this;
	}

	public function send()
	{
		$queryWhere = [
			'action'	=> $this->action,
			'is_active' => '1',
			'type'		=> 'email'
		];

		if( !is_null( $this->actionId ) )
		{
			$queryWhere['id'] = $this->actionId;
		}

        $this->appointmentInf			= Appointment::get( $this->appointmentID );
        $this->appointmentCustomerInf	= AppointmentCustomer::where('appointment_id', $this->appointmentID)
            ->where('customer_id', $this->customerID)->fetch();


        $getNotfTabIds = DB::DB()->get_results( DB::DB()->prepare( 'SELECT id FROM '.DB::table( 'notification_tabs' ).' WHERE FIND_IN_SET( "email", notification_types )  AND  ( FIND_IN_SET( %d, locations ) OR IFNULL( locations, "" ) = "" )  AND ( FIND_IN_SET( %d, services ) OR IFNULL( services, "" ) = "" )  AND ( FIND_IN_SET( %s, languages ) OR IFNULL( languages, "" ) = "" )  AND ( FIND_IN_SET( %d, staff ) OR IFNULL( staff, "" ) = "" ) ' . Permission::queryFilter( 'notification_tabs' ), [
	        $this->appointmentInf['location_id'],
	        $this->appointmentInf['service_id'],
	        $this->appointmentCustomerInf['locale'],
	        $this->appointmentInf['staff_id']
        ] ), ARRAY_A );

        $notfTabIds = [0];

        foreach($getNotfTabIds as $value)
        {
            $notfTabIds[] = $value['id'];
        }

		$sendToStaff = Notification::where( $queryWhere )->where('send_to', 'staff')
								   ->where('tab_id', $notfTabIds)
								   ->fetch();

		if( !$sendToStaff )
		{
			$sendToStaff = Notification::where( $queryWhere )->where('send_to', 'staff')
								       ->where('tab_id', 'is', NULL)
								       ->fetch();
		}

        $sendToCustomer = Notification::where( $queryWhere )->where('send_to', 'customer')
            ->where('tab_id',  $notfTabIds)->fetch();

        if(!$sendToCustomer)
        {
            $sendToCustomer = Notification::where( $queryWhere )->where('tab_id', 'is', NULL)
                ->where('send_to', 'customer')->fetch();
        }

        if(!$sendToCustomer && !$sendToStaff)
        {
            return false;
        }

		$this->customerInf				= Customer::get( $this->customerID );
		$this->staffInf					= Staff::get( $this->appointmentInf['staff_id'] );

		$this->serviceInf				= Service::get( $this->appointmentInf['service_id'] );
		$this->serviceCategoryInf		= ServiceCategory::get( $this->serviceInf['category_id'] );
		$this->locationInf				= Location::get( $this->appointmentInf['location_id'] );

		if( $sendToCustomer )
		{
			$customerEmail	= $this->customerInf['email'];

			$emailSubject	= $this->replaceShortTags( $sendToCustomer['subject'] );
			$emailBody		= $this->replaceShortTags( $sendToCustomer['body'] );

			$this->sendMail( $customerEmail, $emailSubject, $emailBody, $sendToCustomer['id'] );
		}

		if( $sendToStaff )
		{
			$staffEmail	= $this->staffInf['email'];

			if( $this->preventStaffDublicateNotifications( $staffEmail ) )
			{
				$emailSubject	= $this->replaceShortTags( $sendToStaff['subject'], true );
				$emailBody		= $this->replaceShortTags( $sendToStaff['body'], true );

				$this->sendMail( $staffEmail, $emailSubject, $emailBody, $sendToStaff['id'], true );
			}
		}
	}

	private function preventStaffDublicateNotifications( $email )
	{
		if( isset( $this->preventStaffDublicateNotifications[ $email ] ) )
		{
			return false;
		}

		$this->preventStaffDublicateNotifications[ $email ] = true;

		return true;
	}

	private function sendMail( $sendTo, $subject, $body, $notificationId, $toStaff = false )
	{
		if( empty( $sendTo ) )
			return false;

		if( Helper::isSaaSVersion() )
		{
			$allowToSend = Permission::tenantInf()->checkNotificationLimits( 'email' );

			if( $allowToSend[0] === false )
			{
				return false;
			}
		}

		$mailGateway	= Helper::getOption('mail_gateway', 'wp_mail', false);
		$senderEmail	= Helper::getOption('sender_email', '', false);
		$senderName		= Helper::getOption('sender_name', '', false);

		if( Helper::isSaaSVersion() && Helper::getOption('allow_tenants_to_set_email_sender', 'off', false) == 'on' )
		{
			$tenantSenderName = Helper::getOption('sender_name', '');
			if( !empty( $tenantSenderName ) )
			{
				$senderName = $tenantSenderName;
			}
		}

		$this->addInvoices( $notificationId, $toStaff );

		$headers = 'From: ' . $senderName . ' <' . $senderEmail . '>' . "\r\n" .
			"Content-Type: text/html; charset=UTF-8\r\n";

		if( $mailGateway == 'wp_mail' )
		{
			wp_mail( $sendTo, $subject, $body, $headers, $this->attachments );
		}
		else // SMTP
		{
			$mail = new \Booknetic_PHPMailer\PHPMailer\PHPMailer();

			$mail->isSMTP();

			$mail->Host			= Helper::getOption('smtp_hostname', '', false);
			$mail->Port			= Helper::getOption('smtp_port', '', false);
			$mail->SMTPSecure	= Helper::getOption('smtp_secure', '', false);
			$mail->SMTPAuth		= true;
			$mail->Username		= Helper::getOption('smtp_username', '', false);
			$mail->Password		= Helper::getOption('smtp_password', '', false);

			$mail->setFrom( $senderEmail, $senderName );
			$mail->addAddress( $sendTo );

			$mail->Subject		= $subject;
			$mail->Body			= $body;

			$mail->IsHTML(true);
			$mail->CharSet = 'UTF-8';

			foreach ( $this->attachments AS $attachment )
			{
				$mail->AddAttachment( $attachment, basename($attachment),  'base64', 'application/pdf');
			}

			$mail->send();
		}

		$this->clearCache();
	}

	private function addInvoices( $notificationId, $toStaff = false )
	{
		$invoices = DB::DB()->get_results(
			DB::DB()->prepare('SELECT * FROM `'.DB::table('invoices').'` WHERE FIND_IN_SET(%d,`notifications`)' . DB::tenantFilter(), [ $notificationId ]),
			ARRAY_A
		);

		foreach ( $invoices AS $invoice )
		{
			$body = $this->replaceShortTags( $invoice['content'], $toStaff );

			$fileName = preg_replace( '[^a-zA-Z0-9\-\_\(\)]','', $invoice['name'] );
			if ( empty($fileName) )
			{
				$fileName = uniqid();
			}

			if( empty( $this->cacheFolder ) )
			{
				$this->cacheFolder = Helper::uploadedDir( 'Invoices/' . uniqid() );
			}

			$pdfPath = $this->cacheFolder . $fileName . '.pdf';

			$mpdf = new Mpdf();
			$mpdf->WriteHTML( $body );
			$mpdf->Output( $pdfPath, Destination::FILE );

			$this->attachments[] = $pdfPath;
		}
	}

	private function clearCache()
	{
		foreach ( $this->attachments AS $cacheFile )
		{
			unlink( $cacheFile );
		}

		if( !empty( $this->cacheFolder ) )
		{
			rmdir( $this->cacheFolder );
		}

		$this->attachments = [];
		$this->cacheFolder = '';
	}

	private function translateStatus($status)
    {
        switch($status)
        {
            case 'approved': return bkntc__('Approved'); break;
            case 'pending': return bkntc__('Pending'); break;
            case 'waiting_for_payment': return bkntc__('Waiting for payment'); break;
            case 'canceled': return bkntc__('Canceled'); break;
            case 'rejected': return bkntc__('Rejected'); break;
            default: return $status; break;
        }
    }

	private function replaceShortTags( $body, $toStaff = false )
	{
		$body = str_replace( [
			'{appointment_id}',
			'{appointment_date}',
			'{appointment_date_time}',
			'{appointment_start_time}',
			'{appointment_end_time}',
			'{appointment_duration}',
			'{appointment_buffer_before}',
			'{appointment_buffer_after}',
			'{appointment_status}',
			'{appointment_service_price}',
			'{appointment_extras_price}',
			'{appointment_extras_list}',
			'{appointment_discount_price}',
			'{appointment_sum_price}',
			'{appointment_paid_price}',
			'{appointment_payment_method}',
			'{appointment_tax_amount}',

			'{service_name}',
			'{service_price}',
			'{service_duration}',
			'{service_notes}',
			'{service_color}',
			'{service_image_url}',
			'{service_category_name}',

			'{customer_full_name}',
			'{customer_first_name}',
			'{customer_last_name}',
			'{customer_phone}',
			'{customer_email}',
			'{customer_birthday}',
			'{customer_notes}',
			'{customer_profile_image_url}',
			'{customer_panel_url}',
			'{customer_panel_password}',

			'{staff_name}',
			'{staff_email}',
			'{staff_phone}',
			'{staff_about}',
			'{staff_profile_image_url}',

			'{location_name}',
			'{location_address}',
			'{location_image_url}',
			'{location_phone_number}',
			'{location_notes}',

			'{company_name}',
			'{company_image_url}',
			'{company_website}',
			'{company_phone}',
			'{company_address}',

			'{zoom_meeting_url}',
			'{zoom_meeting_password}',

            '{appointment_created_date}',
            '{appointment_created_time}',
		], [
			$this->appointmentCustomerInf['id'],
            Date::datee( $this->appointmentInf['date'] . ' ' . $this->appointmentInf['start_time'], false, !$toStaff,  $this->appointmentCustomerInf['client_timezone'] ), // staff ucun gonderende musterinin timezonuna gore getmemelidir
            Date::dateTime( $this->appointmentInf['date'] . ' ' . $this->appointmentInf['start_time'], false, !$toStaff, $this->appointmentCustomerInf['client_timezone'] ), // staff ucun gonderende musterinin timezonuna gore getmemelidir
            Date::time( $this->appointmentInf['date'] . ' ' . $this->appointmentInf['start_time'], false, !$toStaff, $this->appointmentCustomerInf['client_timezone'] ), // staff ucun gonderende musterinin timezonuna gore getmemelidir
            Date::time(Date::epoch( $this->appointmentInf['date'] . ' ' . $this->appointmentInf['start_time'] ) + $this->appointmentInf['duration'] * 60, false, !$toStaff, $this->appointmentCustomerInf['client_timezone']), // staff ucun gonderende musterinin timezonuna gore getmemelidir
            Helper::secFormat( $this->appointmentInf['duration'] * 60 ),
			Helper::secFormat( $this->appointmentInf['buffer_before'] * 60 ),
			Helper::secFormat( $this->appointmentInf['buffer_after'] * 60 ),
			self::translateStatus($this->appointmentCustomerInf['status']),
			Helper::price( $this->appointmentCustomerInf['service_amount'] ),
			Helper::price( $this->appointmentCustomerInf['extras_amount'] ),
			$this->extraServicesList(),
			Helper::price( $this->appointmentCustomerInf['discount'] ),
			Helper::price( $this->appointmentCustomerInf['service_amount'] + $this->appointmentCustomerInf['extras_amount'] - $this->appointmentCustomerInf['discount'] ),
			Helper::price( $this->appointmentCustomerInf['paid_amount'] ),
			Helper::paymentMethod( $this->appointmentCustomerInf['payment_method'] ),
			Helper::price( $this->appointmentCustomerInf['tax_amount'] ),

			$this->serviceInf['name'],
			Helper::price( $this->serviceInf['price'] ),
			Helper::secFormat( $this->serviceInf['duration'] * 60 ),
			$this->serviceInf['notes'],
			$this->serviceInf['color'],
			Helper::profileImage( $this->serviceInf['image'], 'Services' ),
			$this->serviceCategoryInf['name'],

			$this->customerInf['first_name'] . ' ' . $this->customerInf['last_name'],
			$this->customerInf['first_name'],
			$this->customerInf['last_name'],
			$this->customerInf['phone_number'],
			$this->customerInf['email'],
			$this->customerInf['birthdate'],
			$this->customerInf['notes'],
			Helper::profileImage( $this->customerInf['profile_image'], 'Customers' ),
			Helper::customerPanelURL(),
			$this->userPassword,

			$this->staffInf['name'],
			$this->staffInf['email'],
			$this->staffInf['phone_number'],
			$this->staffInf['about'],
			Helper::profileImage( $this->staffInf['profile_image'], 'Staff' ),

			$this->locationInf['name'],
			$this->locationInf['address'],
			Helper::profileImage( $this->locationInf['image'], 'Locations' ),
			$this->locationInf['phone_number'],
			$this->locationInf['notes'],

			Helper::getOption('company_name', ''),
			Helper::profileImage( Helper::getOption('company_image', ''), 'Settings'),
			Helper::getOption('company_website', ''),
			Helper::getOption('company_phone', ''),
			Helper::getOption('company_address', ''),

			$this->getZoomData('url', $toStaff),
			$this->getZoomData('password', $toStaff),

            Date::datee( $this->appointmentCustomerInf['created_at'], false, !$toStaff ), // staff ucun gonderende musterinin timezonuna gore getmemelidir
            Date::time($this->appointmentCustomerInf['created_at'], false, !$toStaff ), // staff ucun gonderende musterinin timezonuna gore getmemelidir

		], $body );

		$body = preg_replace_callback('/{appointment_custom_field_([0-9]+)}/', function ( $found )
		{

			if( !isset( $found[1] ) )
				return $found[0];

			return $this->getCustomFieldValue( $found[1] );

		}, $body);

		$body = preg_replace_callback('/{appointment_custom_field_([0-9]+)_url}/', function ( $found )
		{

			if( !isset( $found[1] ) )
				return $found[0];

			return $this->getCustomFieldValue( $found[1], true );

		}, $body);

		return $body;
	}

	private function getCustomFieldValue( $cf_id, $fileUrl = false )
	{
		$customData = DB::DB()->get_row(
			DB::DB()->prepare("
				SELECT 
					tb2.type, tb1.input_file_name,
					IF( tb2.type IN ('select', 'checkbox', 'radio'), (SELECT group_concat(' ', `title`) FROM `".DB::table('form_input_choices')."` WHERE FIND_IN_SET(id, tb1.`input_value`)), tb1.`input_value` ) AS real_value
				FROM `".DB::table('appointment_custom_data')."` tb1 
				LEFT JOIN `".DB::table('form_inputs')."` tb2 ON tb2.id=tb1.form_input_id
				WHERE appointment_id=%d AND customer_id=%d AND form_input_id=%d
				", [ $this->appointmentID, $this->customerID, $cf_id ]
			),
			ARRAY_A
		);

		if( !$customData )
		{
			return '';
		}

		if( $customData['type'] == 'file' )
		{
			if( $fileUrl )
			{
				return Helper::uploadedFileURL( htmlspecialchars($customData['real_value']), 'CustomForms');
			}
			else
			{
				return $customData['input_file_name'];
			}
		}
		else
		{
			return $customData['real_value'];
		}
	}

	private function getZoomData( $fieldName, $toStaff )
	{
		$zoomData = json_decode( $this->appointmentInf['zoom_meeting_data'], true );

		if( empty( $zoomData ) || !is_array( $zoomData ) )
			return '';

		if( $fieldName == 'url' )
		{
			return $toStaff ? $zoomData['start_url'] : $zoomData['join_url'];
		}
		else if( $fieldName == 'password' )
		{
			return isset( $zoomData['password'] ) ? $zoomData['password'] : '';
		}
		else
		{
			return '';
		}
	}

	private function extraServicesList()
	{
		$extraServices = AppointmentExtra::where('appointment_id', $this->appointmentID)->where('customer_id', $this->customerID)->fetchAll();
		$listStr = '';

		foreach ( $extraServices AS $extraInf )
		{
			$listStr .= $extraInf->extra()->fetch()->name . ( $extraInf->quantity > 1 ? ' x' . $extraInf->quantity : '' ) . ' - ' . Helper::price( $extraInf->price * $extraInf->quantity ) . '<br/>';
		}

		return $listStr;
	}

}
