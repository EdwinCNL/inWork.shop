<?php

namespace BookneticApp\Backend\Emailnotifications\Controller;

use BookneticApp\Backend\Emailnotifications\Model\Notification;
use BookneticApp\Backend\Invoices\Model\Invoice;
use BookneticApp\Backend\NotificationTab\Model\NotificationTab;
use BookneticApp\Providers\Controller;
use BookneticApp\Providers\DB;
use BookneticApp\Providers\Helper;

class Main extends Controller
{

	public function index()
	{
        $tab_id = Helper::_get('tab_id', 0,'int');

        $notifications = Notification::where('type', 'email');

		$tabInf = NotificationTab::whereFindInSet('notification_types', 'email')->get( $tab_id );
        if ( $tabInf )
        {
            $notifications = $notifications->where('tab_id', $tab_id);
        }
        else
        {
            $notifications = $notifications->where('tab_id', 'is',null);
            $tab_id = 0;
        }

        $notifications = $notifications->orderBy('order_by')->fetchAll();

		$invoices = Invoice::fetchAll();

		$inv = [];

		foreach ( $invoices AS $invoice )
		{
			$notificationIDs = empty( $invoice->notifications ) ? [] : explode(',', $invoice->notifications);

			foreach ( $notificationIDs AS $notificationID )
			{
				foreach ( $notifications AS $nKey => $notificationInf )
				{
					if( $notificationInf['id'] == $notificationID )
					{
						if( !isset( $notifications[$nKey]['invoices'] ) )
						{
							$notifications[$nKey]['invoices'] = [];
							$inv[$nKey] = [];
						}

                        $inv[$nKey][] = $invoice->id;
					}
				}
			}
		}

		foreach ( $notifications AS $key => $notificationInf )
		{
			if( !isset( $notificationInf['invoices'] ) )
			{
				$notificationInf['invoices'] = [];
			}
			else
			{
                $notificationInf['invoices'] = $inv[$key];
            }
		}

		$activeNotification = Helper::_get('tab', 'new_booking:customer', 'string');

        $tabs = NotificationTab::whereFindInSet('notification_types', 'email')->fetchAll();

        $this->view( 'index',[
            'tab_id'            =>  $tab_id,
            'tabs'              =>  $tabs,
            'notifications'		=>	$notifications,
            'active_tab'		=>	$activeNotification
		] );
	}

}
