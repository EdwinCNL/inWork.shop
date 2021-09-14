<?php

namespace BookneticApp\Backend\Locations\Controller;

use BookneticApp\Backend\Appointments\Model\Appointment;
use BookneticApp\Backend\Locations\Model\Location;
use BookneticApp\Providers\Controller;
use BookneticApp\Providers\DataTable;
use BookneticApp\Providers\DB;
use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Permission;

class Main extends Controller
{

	public function index()
	{
		$dataTable = new DataTable( new Location() );

		$dataTable->setTitle(bkntc__('Locations'));
		$dataTable->addNewBtn(bkntc__('ADD LOCATION'));
		$dataTable->activateExportBtn();

		$dataTable->deleteFn( [static::class , '_delete'] );

		$dataTable->searchBy(["name", 'address', 'phone_number', 'notes']);

		$dataTable->addColumns(bkntc__('ID'), 'id');

		$dataTable->addColumns(bkntc__('NAME'), function( $location )
		{
			return Helper::profileCard( $location['name'], $location['image'], '', 'Locations' );
		}, ['is_html' => true, 'order_by_field' => "name"]);

		$dataTable->addColumns(bkntc__('PHONE'), 'phone_number');
		$dataTable->addColumns(bkntc__('ADDRESS'), 'address');

		$table = $dataTable->renderHTML();

		$this->view( 'index', ['table' => $table] );
	}

	public static function _delete( $ids )
	{
		foreach ( $ids AS $id )
		{
			// check if appointment exist
			$checkAppointments = Appointment::where('location_id', $id)->fetch();
			if( $checkAppointments )
			{
				Helper::response(false, bkntc__('This location is using some Appointments. Firstly remove them!'));
			}

			DB::DB()->query( DB::DB()->prepare("UPDATE `".DB::table('staff')."` SET locations=TRIM(BOTH ',' FROM REPLACE(CONCAT(',',`locations`,','),%s,'')) WHERE FIND_IN_SET(%d, `locations`)", [",{$id},", $id]) );
		}
	}

}
