<?php

namespace BookneticApp\Backend\Giftcards\Controller;

use BookneticApp\Backend\Appointments\Model\Appointment;
use BookneticApp\Backend\Appointments\Model\AppointmentCustomer;
use BookneticApp\Backend\Appointments\Model\AppointmentExtra;
use BookneticApp\Backend\Giftcards\Model\Giftcard;
use BookneticApp\Backend\Locations\Model\Location;
use BookneticApp\Backend\Services\Model\Service;
use BookneticApp\Backend\Staff\Model\Staff;
use BookneticApp\Providers\Helper;
use BookneticApp\Providers\DB;

class Ajax extends \BookneticApp\Providers\Ajax
{
    public function add_new()
	{
		$cid = Helper::_post('id', '0', 'integer');

		$services = [];
        $staff = [];
        $location = [];

		if( $cid > 0 )
		{
			$giftcardInf = Giftcard::get( $cid );

			foreach ( explode(',', $giftcardInf['services']) AS $serviceId )
			{
				if( $serviceId > 0 )
				{
					$serviceInf = Service::get( $serviceId );
					$services[] = [ $serviceId, $serviceInf['name'] ];
				}
			}

			foreach ( explode(',', $giftcardInf['staff']) AS $staffId )
			{
				if( $staffId > 0 )
				{
					$serviceInf = Staff::get( $staffId );
					$staff[] = [ $staffId, $serviceInf['name'] ];
				}
            }
            
            
			foreach ( explode(',', $giftcardInf['location']) AS $locationId )
			{
				if( $locationId > 0 )
				{
					$locationInf = Location::get( $locationId );
					$location[] = [ $locationId, $locationInf['name'] ];
				}
			}
		}
		else
		{
			$giftcardInf = [
				'id'                =>  null,
				'code'              =>  null,
				'amount'            =>  null,
				'service'           =>  null,
				'location'          =>  null,
				'staff'             =>  null
			];
        }

		$this->modalView('add_new', [
			'giftcard'	=>	$giftcardInf,
			'services'	=>	$services,
            'staff'		=>	$staff,
            'locations' =>  $location
		]);
    }

	public function save_giftcard()
	{
		$id					=	Helper::_post('id', '0', 'integer');

		$code				=	Helper::_post('code', '', 'string');
		$amount				=	Helper::_post('amount', '', 'float');
		$locations			=	Helper::_post('locations', '', 'string');
		$services			=	Helper::_post('services', '', 'string');
		$staff				=	Helper::_post('staff', '', 'string');

        $checkDuplicate = Giftcard::select(['code'])->where('code', $code)->where('id', '<>', $id)->fetch();

        if( $checkDuplicate )
        {
            Helper::response(false, bkntc__('The code already exists!'));
        }

		if( $amount <= 0)
		{
			Helper::response(false, bkntc__('Amount cannot be zero or negative number!') );
        }

        $locationArr = json_decode( $locations, true );
		$location = [];
		foreach ( $locationArr AS $locationId )
		{
			$location[] = (int)$locationId;
		}
		$locations = implode(',', $location);

		$servicesArr = json_decode( $services, true );
		$services = [];
		foreach ( $servicesArr AS $serviceId )
		{
			$services[] = (int)$serviceId;
		}
		$services = implode(',', $services);

		$staffArr = json_decode( $staff, true );
		$staff = [];
		foreach ( $staffArr AS $staffid )
		{
			$staff[] = (int)$staffid;
		}
		$staff = implode(',', $staff);

		if( empty($code) )
		{
			Helper::response(false, bkntc__('Please type the giftcard code field!'));
		}

		$sqlData = [
			'code'				=>	$code,
            'amount'			=>	$amount,
			'locations'			=>	$locations,
			'services'			=>	$services,
			'staff'				=>	$staff
		];

		if( $id > 0 )
		{
			$spent = AppointmentCustomer::where( [ 'giftcard_id' => $id ] )->sum( 'giftcard_amount' );

			if ( $amount < $spent )
			{
				Helper::response(false, bkntc__('The gift card balance cannot be less than the amount spent!') );
			}

			Giftcard::where('id', $id)->update( $sqlData );
		}
		else
		{
			Giftcard::insert( $sqlData );
		}

		Helper::response(true );
	}

    public function get_services()
	{
		$search		= Helper::_post('q', '', 'string');

		$services = Service::where('name', 'LIKE', '%'.$search.'%')->fetchAll();
		$data = [];

		foreach ( $services AS $service )
		{
			$data[] = [
				'id'				=>	(int)$service['id'],
				'text'				=>	htmlspecialchars($service['name'])
			];
		}

		Helper::response(true, [ 'results' => $data ]);
	}

	public function get_staff()
	{
		$search	= Helper::_post('q', '', 'string');

		$staff  = Staff::where('name', 'LIKE', '%'.$search.'%')->fetchAll();
		$data   = [];

		foreach ( $staff AS $staffInf )
		{
			$data[] = [
				'id'				=>	(int)$staffInf['id'],
				'text'				=>	htmlspecialchars($staffInf['name'])
			];
		}

		Helper::response(true, [ 'results' => $data ]);
    }
    
    public function get_location()
	{
		$search	= Helper::_post('q', '', 'string');

		$location  = Location::where('name', 'LIKE', '%'.$search.'%')->fetchAll();
		$data   = [];

		foreach ( $location AS $locationInf )
		{
			$data[] = [
				'id'				=>	(int)$locationInf['id'],
				'text'				=>	htmlspecialchars($locationInf['name'])
			];
		}
		Helper::response(true, [ 'results' => $data ]);
	}

	public function giftcard_usage_history()
	{
		$giftcardId = Helper::_post('id', '0', 'integer');
		$data = [];
		$giftInf = [];
		$counter = 0;

		$giftcardInf = AppointmentCustomer::where('giftcard_id', $giftcardId)->fetchAll();

		if( !$giftcardInf )
		{
			Helper::response(false, bkntc__('Giftcard is not used yet!'));
		}
		
		foreach($giftcardInf as $gift)
		{
			$customerInf = $gift->customer()->fetch();
			$appointmentInf = $gift->appointment()->fetch();
			$serviceInf = $appointmentInf->service()->fetch();

			$giftInf['gift-'.$counter.'-giftcard_amount'] 	= $gift->giftcard_amount;
			$giftInf['gift-'.$counter.'-first_name'] 		= $customerInf->first_name;
			$giftInf['gift-'.$counter.'-last_name'] 		= $customerInf->last_name;
			$giftInf['gift-'.$counter.'-profile_image'] 	= $customerInf->profile_image;
			$giftInf['gift-'.$counter.'-email'] 			= $customerInf->email;
			$giftInf['gift-'.$counter.'-service_name'] 		= $serviceInf->name;
			$giftInf['gift-'.$counter.'-date'] 				= $appointmentInf->date;
			$giftInf['gift-'.$counter.'-appointment_id'] 				= $appointmentInf->id;

			array_push($data, $giftInf);
			$counter++;
		}

		$this->modalView( 'giftcard_usage_history', [
			'giftcards'		=> $data
		] );
	}

}