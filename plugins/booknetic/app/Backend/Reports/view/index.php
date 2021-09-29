<?php
namespace BookneticApp\Frontend\view;

use BookneticApp\Providers\Helper;

defined( 'ABSPATH' ) or die();

?>
<link rel="stylesheet" href="<?php print Helper::assets('css/main.css', 'Reports')?>">
<script type="application/javascript" src="<?php print Helper::assets('js/reports.js', 'Reports')?>"></script>
<script type="application/javascript" src="<?php print Helper::assets('js/chart.min.js', 'Reports')?>"></script>

<div class="m_header clearfix">
	<div class="m_head_title float-left"><?php print bkntc__('Reports')?> <span class="badge badge-warning row_count">4</span></div>
	<div class="m_head_actions float-right"></div>
</div>

<div id="module-reports">
    <div class="row">

        <div class="col-md-6 col-sm-12 col-xs-12 col-lg-6">
	        <div class="fs_portlet">
		        <div class="fs_portlet_title">
			        <div><?php print bkntc__('Reports by the number of appointments')?></div>
			        <div>
				        <span class="actions_btn" data-toggle="dropdown"><span><?php print bkntc__('Daily')?></span> <i class="fa fa-chevron-down pl-2"></i></span>
				        <div class="dropdown-menu dropdown-menu-right row-actions-area">
					        <button class="dropdown-item" data-appointment-report-via-count-type="daily" type="button"><?php print bkntc__('Daily')?></button>
					        <button class="dropdown-item" data-appointment-report-via-count-type="monthly" type="button"><?php print bkntc__('Monthly')?></button>
					        <button class="dropdown-item" data-appointment-report-via-count-type="annualy" type="button"><?php print bkntc__('Annualy')?></button>
				        </div>
			        </div>
		        </div>
		        <div class="fs_portlet_content">
			        <div class="form-row">
				        <div class="form-group col-md-4">
					        <select class="form-control" data-placeholder="<?php print bkntc__('Service filter')?>" data-filter="service">
						        <option></option>
								<?php foreach( $parameters['services'] AS $service ):?>
						        <option value="<?php print (int)$service->id?>"><?php print esc_html($service->name)?></option>
						        <?php endforeach;?>
					        </select>
				        </div>
				        <div class="form-group col-md-4">
					        <select class="form-control" data-placeholder="<?php print bkntc__('Location filter')?>" data-filter="location">
						        <option></option>
						        <?php foreach( $parameters['locations'] AS $location ):?>
							        <option value="<?php print (int)$location->id?>"><?php print esc_html($location->name)?></option>
						        <?php endforeach;?>
					        </select>
				        </div>
				        <div class="form-group col-md-4">
					        <select class="form-control" data-placeholder="<?php print bkntc__('Staff filter')?>" data-filter="staff">
						        <option></option>
						        <?php foreach( $parameters['staff'] AS $staff ):?>
							        <option value="<?php print (int)$staff->id?>"><?php print esc_html($staff->name)?></option>
						        <?php endforeach;?>
					        </select>
				        </div>
			        </div>
			        <div>
				        <canvas id="appointment-count"></canvas>
			        </div>
		        </div>
	        </div>
        </div>

        <div class="col-md-6 col-sm-12 col-xs-12 col-lg-6 mt-md-0 mt-4">
	        <div class="fs_portlet">
		        <div class="fs_portlet_title">
			        <div><?php print bkntc__('Reports by appointment earnings')?> (<?php print Helper::currencySymbol()?>)</div>
			        <div>
				        <span class="actions_btn" data-toggle="dropdown"><span><?php print bkntc__('Daily')?></span> <i class="fa fa-chevron-down pl-2"></i></span>
				        <div class="dropdown-menu dropdown-menu-right row-actions-area">
					        <button class="dropdown-item" data-appointment-report-via-price-type="daily" type="button"><?php print bkntc__('Daily')?></button>
					        <button class="dropdown-item" data-appointment-report-via-price-type="monthly" type="button"><?php print bkntc__('Monthly')?></button>
					        <button class="dropdown-item" data-appointment-report-via-price-type="annualy" type="button"><?php print bkntc__('Annualy')?></button>
				        </div>
			        </div>
		        </div>
		        <div class="fs_portlet_content">
			        <div class="form-row">
				        <div class="form-group col-md-4">
					        <select class="form-control" data-placeholder="<?php print bkntc__('Service filter')?>" data-filter="service">
						        <option></option>
						        <?php foreach( $parameters['services'] AS $service ):?>
							        <option value="<?php print (int)$service->id?>"><?php print esc_html($service->name)?></option>
						        <?php endforeach;?>
					        </select>
				        </div>
				        <div class="form-group col-md-4">
					        <select class="form-control" data-placeholder="<?php print bkntc__('Location filter')?>" data-filter="location">
						        <option></option>
						        <?php foreach( $parameters['locations'] AS $location ):?>
							        <option value="<?php print (int)$location->id?>"><?php print esc_html($location->name)?></option>
						        <?php endforeach;?>
					        </select>
				        </div>
				        <div class="form-group col-md-4">
					        <select class="form-control" data-placeholder="<?php print bkntc__('Staff filter')?>" data-filter="staff">
						        <option></option>
						        <?php foreach( $parameters['staff'] AS $staff ):?>
							        <option value="<?php print (int)$staff->id?>"><?php print esc_html($staff->name)?></option>
						        <?php endforeach;?>
					        </select>
				        </div>
			        </div>
			        <div>
				        <canvas id="appointment-price"></canvas>
			        </div>
		        </div>
	        </div>
        </div>
    </div>
	<div class="row mt-4">

        <div class="col-md-6 col-sm-12 col-xs-12 col-lg-6">
	        <div class="fs_portlet">
		        <div class="fs_portlet_title">
			        <div><?php print bkntc__('Most earning locations')?></div>
			        <div>
				        <span class="actions_btn" data-toggle="dropdown"><span><?php print bkntc__('This week')?></span> <i class="fa fa-chevron-down pl-2"></i></span>
				        <div class="dropdown-menu dropdown-menu-right row-actions-area">
					        <button class="dropdown-item" data-report-by-location-type="this-week" type="button"><?php print bkntc__('This week')?></button>
					        <button class="dropdown-item" data-report-by-location-type="previous-week" type="button"><?php print bkntc__('Previous week')?></button>
					        <button class="dropdown-item" data-report-by-location-type="this-month" type="button"><?php print bkntc__('This month')?></button>
					        <button class="dropdown-item" data-report-by-location-type="previous-month" type="button"><?php print bkntc__('Previous month')?></button>
					        <button class="dropdown-item" data-report-by-location-type="this-year" type="button"><?php print bkntc__('This year')?></button>
					        <button class="dropdown-item" data-report-by-location-type="previous-year" type="button"><?php print bkntc__('Previous year')?></button>
				        </div>
			        </div>
		        </div>
		        <div class="fs_portlet_content">
			        <canvas id="location-report"></canvas>
		        </div>
	        </div>
        </div>

        <div class="col-md-6 col-sm-12 col-xs-12 col-lg-6 mt-md-0 mt-4">
	        <div class="fs_portlet">
		        <div class="fs_portlet_title">
			        <div><?php print bkntc__('Most earning staffs')?></div>
			        <div>
				        <span class="actions_btn" data-toggle="dropdown"><span><?php print bkntc__('This week')?></span> <i class="fa fa-chevron-down pl-2"></i></span>
				        <div class="dropdown-menu dropdown-menu-right row-actions-area">
					        <button class="dropdown-item" data-report-by-staff-type="this-week" type="button"><?php print bkntc__('This week')?></button>
					        <button class="dropdown-item" data-report-by-staff-type="previous-week" type="button"><?php print bkntc__('Previous week')?></button>
					        <button class="dropdown-item" data-report-by-staff-type="this-month" type="button"><?php print bkntc__('This month')?></button>
					        <button class="dropdown-item" data-report-by-staff-type="previous-month" type="button"><?php print bkntc__('Previous month')?></button>
					        <button class="dropdown-item" data-report-by-staff-type="this-year" type="button"><?php print bkntc__('This year')?></button>
					        <button class="dropdown-item" data-report-by-staff-type="previous-year" type="button"><?php print bkntc__('Previous year')?></button>
				        </div>
			        </div>
		        </div>
		        <div class="fs_portlet_content">
			        <canvas id="staff-report"></canvas>
		        </div>
	        </div>
        </div>
    </div>
</div>