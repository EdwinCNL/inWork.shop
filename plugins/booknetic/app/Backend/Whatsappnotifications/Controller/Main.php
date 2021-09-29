<?php

namespace BookneticApp\Backend\Whatsappnotifications\Controller;

use BookneticApp\Backend\Emailnotifications\Model\Notification;
use BookneticApp\Backend\NotificationTab\Model\NotificationTab;
use BookneticApp\Providers\Controller;
use BookneticApp\Providers\DB;
use BookneticApp\Providers\Helper;

class Main extends Controller
{

	public function index()
	{
        $tab_id = Helper::_get('tab_id', 0,'int');
		$notifications = Notification::where('type', 'whatsapp');
        if($tab_id)
        {
            $notifications = $notifications->where('tab_id', $tab_id);
        }
        else
        {
            $notifications = $notifications->where('tab_id', 'is', NULL);
        }
        $notifications = $notifications->orderBy('order_by')->fetchAll();

		$activeNotification = Helper::_get('tab', 'new_booking:customer', 'string');

        $tabs = NotificationTab::whereFindInSet('notification_types', 'whatsapp')->fetchAll();

		$this->view( 'index',[
			'notifications'		=>	$notifications,
			'active_tab'		=>	$activeNotification,
            'tabs'		        =>	$tabs,
            'tab_id'		    =>	$tab_id,
		] );
	}

}
