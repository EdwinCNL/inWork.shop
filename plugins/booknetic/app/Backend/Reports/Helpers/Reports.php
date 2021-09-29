<?php

namespace BookneticApp\Backend\Reports\Helpers;

use BookneticApp\Providers\Date;
use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Math;

class Reports
{

    public static function getDateRangeByType ($type)
    {
        if($type == 'daily')
        {
            $start = Date::format('Y-m-01');
            $end = Date::format('Y-m-d');
            $format = 'Y-m-d';
            $iter = 'day';
        }
        else if($type == 'monthly')
        {
            $start = Date::format('Y-m-01', 'now -11 month');
            $end = Date::format('Y-m-d', 'now');
            $format = 'Y-m';
            $iter = 'month';
        }
        else if($type == 'annualy')
        {
            $start = Date::format('Y-01-01', 'now -5 year');
            $end = Date::format('Y-m-d', 'now');
            $format = 'Y';
            $iter = 'year';
        }
        else if($type == 'this-week')
        {
            $start = Date::format('Y-m-d', 'now monday this week');
            $end = Date::format('Y-m-d', 'now');
            $format = 'Y-m-d';
            $iter = 'day';
        }
        else if($type == 'previous-week')
        {
            $start = Date::format('Y-m-d', 'now monday last week');
            $end = Date::format('Y-m-d', 'now sunday last week');
            $format = 'Y-m-d';
            $iter = 'day';
        }
        else if($type == 'this-month')
        {
            $start = Date::format('Y-m-01', 'now');
            $end = Date::format('Y-m-d', 'now');
            $format = 'Y-m-d';
            $iter = 'month';
        }
        else if($type == 'previous-month')
        {
            $start = Date::format('Y-m-01', 'now -1 month');
            $end = Date::format('Y-m-t', 'now -1 month');
            $format = 'Y-m-d';
            $iter = 'month';
        }
        else if($type == 'this-year')
        {
            $start = Date::format('Y-01-01', 'now');
            $end = Date::format('Y-m-d', 'now');
            $format = 'Y-m-d';
            $iter = 'year';
        }
        else if($type == 'previous-year')
        {
            $start = Date::format('Y-01-01', 'now -1 year');
            $end = Date::format('Y-12-31', 'now -1 year');
            $format = 'Y-m-d';
            $iter = 'year';
        }

        return [
            'start'     => $start,
            'end'       => $end,
            'format'    => $format,
            'iter'      => $iter,
        ];
    }

    public static function sql ($type, $agg)
    {
        if($type == 'daily')
        {
            $select = " `date` as title, $agg as val ";
            $groupBy = ' `date` ';
        }
        else if($type == 'monthly')
        {
            $select = " DATE_FORMAT(`date`, '%Y-%m') as title, $agg as val ";
            $groupBy = " DATE_FORMAT(`date`, '%Y-%m') ";
        }
        else if($type == 'annualy')
        {
            $select = " DATE_FORMAT(`date`, '%Y') as title, $agg as val ";
            $groupBy = " DATE_FORMAT(`date`, '%Y') ";
        }

        return [
            'select'    => $select,
            'groupBy'   => $groupBy,
        ];
    }

    public static function Iterate($data, $start, $end, $format, $iter)
    {
        $titles = [];
        $endEpoch = Date::epoch($end);

        while( Date::epoch($start) <= $endEpoch )
        {
            $s = Date::format($format, $start);
            $titles[$s] = [$s, 0];
            $start = Date::format('Y-m-d', "$start +1 $iter");
        }

        foreach ($data as $value)
        {
            if( isset( $titles[$value['title']][1] ) )
            {
                $titles[$value['title']][1] = Math::floor( $value['val'] );
            }
        }

        if($iter == 'month')
        {
            $monthArray = ['', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            foreach ($titles as $key => $value)
            {
                $titles[$key][0] = $monthArray[(int)Date::format('m', $key)].', '.(int)Date::format('Y', $key);
            }
        }

        $labels = [];
        $values = [];

        foreach ( $titles AS $data )
        {
	        $labels[] = $data[0];
	        $values[] = $data[1];
        }

        return [
        	'labels'    =>  $labels,
	        'values'    =>  $values
        ];
    }

}