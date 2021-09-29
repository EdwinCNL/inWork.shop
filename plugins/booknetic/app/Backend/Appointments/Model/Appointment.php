<?php

namespace BookneticApp\Backend\Appointments\Model;

use BookneticApp\Backend\Locations\Model\Location;
use BookneticApp\Backend\Services\Model\Service;
use BookneticApp\Backend\Staff\Model\Staff;
use BookneticApp\Providers\Date;
use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Model;

class Appointment extends Model
{

	public static $relations = [
		'customers'     => [ AppointmentCustomer::class, 'appointment_id', 'id' ],
		'extras'        => [ AppointmentExtra::class ],
		'customData'    => [ AppointmentCustomData::class ],
		'location'      => [ Location::class, 'id', 'location_id' ],
		'service'       => [ Service::class, 'id', 'service_id' ],
		'staff'         => [ Staff::class, 'id', 'staff_id' ]
	];

	public static function checkAllowSchedule($appointment)
    {
        if(Date::epoch() >= Date::epoch($appointment->date.' '.$appointment->start_time)) return false;

        $minute = Helper::getOption('time_restriction_to_make_changes_on_appointments', '5');

        if(Date::epoch('+'.$minute.' minutes') > Date::epoch($appointment->date.' '.$appointment->start_time)) return false;

        return true;
    }

    public static function checkAllowCancel ($appointment)
    {
        if(Date::epoch() >= Date::epoch($appointment->date.' '.$appointment->start_time)) return false;
        return true;
    }

}
