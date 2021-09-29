<?php

namespace BookneticApp\Backend\NotificationTab\Controller;

use BookneticApp\Providers\Permission;
use BookneticApp\Backend\Emailnotifications\Model\Notification;
use BookneticApp\Backend\Locations\Model\Location;
use BookneticApp\Backend\NotificationTab\Model\NotificationTab;
use BookneticApp\Backend\Services\Model\Service;
use BookneticApp\Backend\Staff\Model\Staff;
use BookneticApp\Providers\Helper;

class Ajax extends \BookneticApp\Providers\Ajax
{
    public $notification_types;

    public function __construct()
    {
        $this->notification_types = [
        	'email' => bkntc__('Email Notifications'),
	        'sms' => bkntc__('SMS Notifications'),
	        'whatsapp' => bkntc__('WhatsApp Notifications')
        ];
        
        if ( Helper::isSaaSVersion() )
        {
	        if ( Permission::getPermission( 'email_notifications' ) === 'off' )
	        {
		        unset( $this->notification_types['email'] );
	        }
	        if ( Permission::getPermission( 'sms_notifications' ) === 'off' )
	        {
		        unset( $this->notification_types['sms'] );
	        }
	        if ( Permission::getPermission( 'whatsapp_notifications' ) === 'off' )
	        {
		        unset( $this->notification_types['whatsapp'] );
	        }
        }
    }

    public function add_tab()
    {
        $id = Helper::_post('id', '0', 'int');

        $current_module = Helper::_post('current_module', null,'string');

        $services = Service::fetchAll();
        $staff = Staff::fetchAll();
        $locations = Location::fetchAll();

        $tab = NotificationTab::where('id', $id)->fetch();

        $this->modalView('add_tab', [
            'id'                    => $id,
            'services'              => $services,
            'staff'                 => $staff,
            'locations'             => $locations,
            'notification_types'    => $this->notification_types,
            'current_module'        => $current_module,
            'tab'                   => $tab
        ]);
    }

    public function save_tab()
    {
        $id		                = Helper::_post('id', '0', 'integer');
        $name	                = Helper::_post('name', null, 'string');
        $languages	            = Helper::_post('languages', 'en_US', 'string');
        $services	            = Helper::_post('services',  [],'array');
        $locations	            = Helper::_post('locations',  [],'array');
        $staff	                = Helper::_post('staff',  [],'array');
        $notification_types	    = Helper::_post('notification_types',  [],'array');

        if($id < 0 || empty($name))
        {
            Helper::response(false, bkntc__('Please fill in all required fields correctly!'));
        }

        $sqlData = [
            'name'		            =>	$name,
            'languages'	            =>	$languages,
            'services'              =>  implode(',', $services),
            'staff'                 =>  implode(',', $staff),
            'locations'             =>  implode(',', $locations),
        ];

        if( $id > 0 )
        {
            NotificationTab::where('id', $id)->update($sqlData);
        }
        else
        {
            if(count($notification_types) == 0)
            {
                Helper::response(false, bkntc__('You did not choose a notification type!'));
            }
            $sqlData['notification_types'] = implode(',', $notification_types);
            $id =  NotificationTab::insertOtherNotificaitonTabData($sqlData);
        }

        Helper::response(true, ['id' => $id]);
    }

    public function delete_tab()
    {
        $id = Helper::_post('id', '0', 'int');

        Notification::where('tab_id', $id)->delete();
        NotificationTab::where('id', $id)->delete();

        Helper::response( true );
    }
}
