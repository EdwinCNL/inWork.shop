<?php

namespace BookneticApp\Backend\Giftcards;

use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Permission;

class Middleware extends \BookneticApp\Providers\Middleware
{

	public static function handle()
	{
		if( Helper::isSaaSVersion() && Permission::tenantInf()->getPermission( 'giftcards' ) !== 'on' )
		{
			return false;
		}

		return Permission::isAdministrator();
	}

	public function boot()
	{
		// add menu...
		$this->createMenu(bkntc__('Giftcards'))
			->setIcon('fa fa-gift')
			->setOrder(11)
			->show();



	}

}