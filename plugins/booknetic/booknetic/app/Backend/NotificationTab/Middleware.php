<?php

namespace BookneticApp\Backend\NotificationTab;

use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Permission;

class Middleware extends \BookneticApp\Providers\Middleware
{

	public static function handle()
	{
		return true;
	}

	public function boot()
	{


	}

}