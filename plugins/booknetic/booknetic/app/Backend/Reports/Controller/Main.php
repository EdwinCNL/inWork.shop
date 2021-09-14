<?php

namespace BookneticApp\Backend\Reports\Controller;

use BookneticApp\Backend\Locations\Model\Location;
use BookneticApp\Backend\Services\Model\Service;
use BookneticApp\Backend\Staff\Model\Staff;
use BookneticApp\Providers\Controller;

class Main extends Controller
{

	public function index()
	{
		$data = [];
		$data['locations'] = Location::fetchAll();
		$data['staff'] = Staff::fetchAll();
		$data['services'] = Service::fetchAll();

		$this->view( 'index', $data );
	}

}
