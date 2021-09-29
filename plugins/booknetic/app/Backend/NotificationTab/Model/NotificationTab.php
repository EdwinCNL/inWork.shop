<?php

namespace BookneticApp\Backend\NotificationTab\Model;

use BookneticApp\Backend\Emailnotifications\Model\Notification;
use BookneticApp\Providers\Model;

class NotificationTab extends Model
{

    public static function insertOtherNotificaitonTabData($data)
    {
        NotificationTab::insert( $data );
        $id = NotificationTab::lastId();
        $notification_types = explode(',', $data['notification_types']);

        foreach ($notification_types as $type)
        {
            $notifications = Notification::where('type', $type)->where('tab_id', 'is', NULL)->fetchAll();
            foreach ($notifications as $key => $notification)
            {
                Notification::insert([
	                'tab_id'        => $id,
	                'action'        => $notification['action'],
	                'send_to'       => $notification['send_to'],
	                'subject'       => $notification['subject'],
	                'body'          => $notification['body'],
	                'is_active'     => $notification['is_active'],
	                'order_by'      => $notification['order_by'],
	                'type'          => $notification['type'],
	                'reminder_time' => $notification['reminder_time'],
                ]);
            }
        }

        return $id;
    }

}