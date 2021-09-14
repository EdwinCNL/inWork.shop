<?php

namespace BookneticApp\Backend\Customers;

use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Permission;

class Middleware extends \BookneticApp\Providers\Middleware
{

	public static function handle()
	{
		if( Helper::isSaaSVersion() && Permission::getPermission( 'customers' ) === 'off' )
		{
			return false;
		}

		return true;
	}

	public function boot()
	{
		// add menu...
		$this->createMenu(bkntc__('Customers'))
			->setIcon('fa fa-users')
			->setOrder(6)
			->show();



	}

}