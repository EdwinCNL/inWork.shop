<?php

namespace BookneticApp\Backend\Reports\Controller;

use BookneticApp\Backend\Appointments\Model\Appointment;
use BookneticApp\Backend\Reports\Helpers\Reports;
use BookneticApp\Providers\DB;
use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Permission;

class Ajax extends \BookneticApp\Providers\Ajax
{

	public function get_appointment_report_via_count ()
	{
	    $type       = Helper::_post('type', 'daily', 'string', ['daily', 'monthly', 'annualy']);
	    $filters    = Helper::_post('filters', [], 'array');

        $dateRange  = Reports::getDateRangeByType($type);
        $sql        = Reports::sql($type,'count(0)');

		$response = Appointment::where('date', '>=', $dateRange['start'])
						->where('date', '<=', $dateRange['end'])
						->groupBy( $sql['groupBy'] )
						->select( $sql['select'] );

		if( isset( $filters['location'] ) && is_numeric( $filters['location'] ) && $filters['location'] > 0 )
		{
			$response->where('location_id', $filters['location']);
		}

		if( isset( $filters['service'] ) && is_numeric( $filters['service'] ) && $filters['service'] > 0 )
		{
			$response->where('service_id', $filters['service']);
		}

		if( isset( $filters['staff'] ) && is_numeric( $filters['staff'] ) && $filters['staff'] > 0 )
		{
			$response->where('staff_id', $filters['staff']);
		}

		$response = $response->fetchAll();

        Helper::response(true, [
            'response' => Reports::Iterate($response, $dateRange['start'], $dateRange['end'], $dateRange['format'], $dateRange['iter'])
        ]);
    }

    public function get_appointment_report_via_price ()
    {
        $type       = Helper::_post('type', 'daily', 'string', ['daily', 'monthly', 'annualy']);
	    $filters    = Helper::_post('filters', [], 'array');

        $dateRange  = Reports::getDateRangeByType($type);
        $sql        = Reports::sql($type, 'SUM(`service_amount`+`extras_amount`-`discount`)');

	    $response = Appointment::leftJoin( 'customers' )
						->where('date', '>=', $dateRange['start'])
						->where('date', '<=', $dateRange['end'])
						->groupBy( $sql['groupBy'] )
						->select( $sql['select'] );

	    if( isset( $filters['location'] ) && is_numeric( $filters['location'] ) && $filters['location'] > 0 )
	    {
		    $response->where('location_id', $filters['location']);
	    }

	    if( isset( $filters['service'] ) && is_numeric( $filters['service'] ) && $filters['service'] > 0 )
	    {
		    $response->where('service_id', $filters['service']);
	    }

	    if( isset( $filters['staff'] ) && is_numeric( $filters['staff'] ) && $filters['staff'] > 0 )
	    {
		    $response->where('staff_id', $filters['staff']);
	    }

	    $response = $response->fetchAll();

        Helper::response(true, [
            'response' => Reports::Iterate($response, $dateRange['start'], $dateRange['end'], $dateRange['format'], $dateRange['iter'])
        ]);
    }

    public function get_location_report()
    {
        $type = Helper::_post('type', 'this-week', 'string', ['this-week', 'previous-week', 'this-month', 'previous-month', 'this-year', 'previous-year']);

        $dateRange = Reports::getDateRangeByType($type);

	    $response = Appointment::leftJoin( 'location' )
	                            ->where('date', '>=', $dateRange['start'])
	                            ->where('date', '<=', $dateRange['end'])
	                            ->groupBy( 'location_id' )
	                            ->select( '`name` AS `title`, count(0) as `val`', true )
	                            ->fetchAll();

	    $labels = [];
	    $values = [];
	    foreach ( $response AS $item )
	    {
		    $labels[] = $item['title'];
		    $values[] = $item['val'];
	    }

        Helper::response(true, [
            'response' => [
            	'labels'    =>  $labels,
	            'values'    =>  $values
            ]
        ]);
    }

    public function get_staff_report()
    {
        $type = Helper::_post('type', 'this-week', 'string', ['this-week', 'previous-week', 'this-month', 'previous-month', 'this-year', 'previous-year']);

        $dateRange = Reports::getDateRangeByType($type);

	    $response = Appointment::leftJoin( 'staff' )
	                           ->where('date', '>=', $dateRange['start'])
	                           ->where('date', '<=', $dateRange['end'])
	                           ->groupBy( 'staff_id' )
	                           ->select( '`name` AS `title`, count(0) as `val`', true )
	                           ->fetchAll();

	    $labels = [];
	    $values = [];
	    foreach ( $response AS $item )
	    {
		    $labels[] = $item['title'];
		    $values[] = $item['val'];
	    }

	    Helper::response(true, [
		    'response' => [
			    'labels'    =>  $labels,
			    'values'    =>  $values
		    ]
	    ]);
    }

}
