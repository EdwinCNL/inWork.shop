<?php

namespace BookneticApp\Backend\Reports;

use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Permission;

class Middleware extends \BookneticApp\Providers\Middleware
{

	public static function handle()
	{
		if( Helper::isSaaSVersion() && Permission::getPermission( 'reports' ) === 'off' )
		{
			return false;
		}

		return true;
	}

	public function boot()
	{
		// add menu...
		$this->createMenu( bkntc__('Reports') )
			->setIcon('fa fa-chart-line')
			->setOrder(2)
			->show();

	}

}