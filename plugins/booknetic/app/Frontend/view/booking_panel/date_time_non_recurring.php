<?php
namespace BookneticApp\Frontend\view;

use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Date;

defined( 'ABSPATH' ) or die();

?>
<div class="booknetic_date_time_area<?php print $parameters['date_based'] ? ' booknetic_date_based_reservation' : '';?>">
	<div class="booknetic_calendar_div">
		<div class="booknetic_calendar_head">
			<div class="booknetic_prev_month"> < </div>
			<div class="booknetic_month_name"></div>
			<div class="booknetic_next_month"> > </div>
		</div>
		<div id="booknetic_calendar_area"></div>
	</div>
	<div class="booknetic_time_div">
		<div class="booknetic_times_head"><?php print bkntc__('Time')?></div>
		<div class="booknetic_times">
			<div class="booknetic_times_title"><?php print bkntc__('Select date')?></div>
			<div class="booknetic_times_list booknetic_clearfix"></div>
		</div>
	</div>
</div>


<div id="booknetic_bring_someone_section">
	<div class="form-row mt-4">
		<div class="col-md-12">
			<input type="checkbox" id="booknetic_bring_someone_checkbox">
			<label for="booknetic_bring_someone_checkbox"><?php print bkntc__('Bring People with You')?> <i class="fa fa-info-circle help-icon"></i></label>
		</div>

		<div class="form-group col-md-6 booknetic_number_of_brought_customers d-none">
			<label for=""><?php print bkntc__('Number of people:') ?></label>
			<select name="booknetic_bring_people_count" id="booknetic_bring_people_count" class="form-control">
				<option value="1">1</option>
			</select>
		</div>
	</div>
</div>