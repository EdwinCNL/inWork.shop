<?php
namespace BookneticApp\Frontend\view;

use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Date;
use BookneticApp\Providers\Math;

defined( 'ABSPATH' ) or die();
?>

<div class="fs-modal-title">
	<div class="title-icon"><img src="<?php print Helper::icon('payment-appointment.svg')?>"></div>
	<div class="title-text"><?php print bkntc__('Usage history')?></div>
	<div class="close-btn" data-dismiss="modal"><i class="fa fa-times"></i></div>
</div>

<div class="fs-modal-body">
	<div class="fs-modal-body-inner">
		<div class="row mt-4">
			<div class="col-md-12">
				<div class="fs_data_table_wrapper">
					<table class="table-gray-2 dashed-border">
						<thead>
							<tr>
								<th><?php print bkntc__('CUSTOMER')?></th>
								<th class="text-center"><?php print bkntc__('USED AMOUNT')?></th>
								<th class="text-center"><?php print bkntc__('SERVICE')?></th>
								<th class="text-center"><?php print bkntc__('DATE')?></th>
								<th class="text-center"><?php print bkntc__('INFO')?></th>
								<th></th>
							</tr>
						</thead>
						<tbody>
						<?php

						$counter = 0;
						foreach( $parameters['giftcards'] AS $giftcard )
						{
							$paid = Math::floor( $giftcard['gift-'.$counter.'-giftcard_amount'] );

							print '<tr data-customer-id="' . (int)$giftcard['customer_id'] . '" data-id="' . (int)$giftcard['id'] . '">';
							print '<td>' . Helper::profileCard($giftcard['gift-'.$counter.'-first_name'], $giftcard['customer_image'], $giftcard['gift-'.$counter.'-email'], 'Customers') . '</td>';
							print '<td align="center">' . Helper::price( $paid ) . '</td>';
							print '<td align="center">' . $giftcard['gift-'.$counter.'-service_name'] . '</td>';
							print '<td align="center">' . $giftcard['gift-'.$counter.'-date'] . '</td>';
							print '<td align="center" data-column="appointment_info" data-appointment-id="' . $giftcard['gift-'.$counter.'-appointment_id'] . '"><img class="invoice-icon" src="' . Helper::icon('invoice.svg') . '"></td>';
							print '</tr>';
							$counter++;
						}
						?>
						</tbody>
					</table>
				</div>
			</div>
		</div>

	</div>
</div>

<div class="fs-modal-footer">
	<button type="button" class="btn btn-lg btn-default" data-dismiss="modal"><?php print bkntc__('CLOSE')?></button>
</div>

