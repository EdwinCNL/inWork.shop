<?php

namespace BookneticApp\Frontend\view;

use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Date;

defined('ABSPATH') or die();
?>

<link rel="stylesheet" href="<?php print Helper::assets('css/add_new.css', 'Giftcards') ?>">
<script type="text/javascript" src="<?php print Helper::assets('js/add_new.js', 'Giftcards') ?>" id="add_new_JS" data-giftcard-id="<?php print (int)$parameters['giftcard']['id'] ?>"></script>

<div class="fs-modal-title">
    <div class="title-icon badge-lg badge-purple"><i class="fa fa-plus"></i></div>
    <div class="title-text"><?php print bkntc__('Add Giftcard') ?></div>
    <div class="close-btn" data-dismiss="modal"><i class="fa fa-times"></i></div>
</div>

<div class="fs-modal-body">
    <div class="fs-modal-body-inner">
        <form id="addLocationForm">

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="input_code"><?php print bkntc__('Code') ?></label>
                    <input type="text" class="form-control" id="input_code" value="<?php print htmlspecialchars($parameters['giftcard']['code']) ?>">
                </div>
                <div class="form-group col-md-6">
                    <label for="input_amount"><?php print bkntc__('Amount (' . Helper::currencySymbol(). ')')   ?></label>
                    <input type="text" class="form-control" id="input_amount" value="<?php print htmlspecialchars($parameters['giftcard']['amount']) ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="input_locations"><?php print bkntc__('Location filter') ?></label>
                    <select class="form-control" id="input_locations" multiple>
                        <?php
                        foreach ($parameters['locations'] as $location)
                        {
                            print '<option value="' . (int)$location[0] . '" selected>' . esc_html($location[1]) . '</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="input_services"><?php print bkntc__('Services filter') ?></label>
                    <select class="form-control" id="input_services" multiple>
                        <?php
                        foreach ($parameters['services'] as $service) {
                            print '<option value="' . (int)$service[0] . '" selected>' . esc_html($service[1]) . '</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="input_staff"><?php print bkntc__('Staff filter') ?></label>
                    <select class="form-control" id="input_staff" multiple>
                        <?php
                        foreach ($parameters['staff'] as $staff) {
                            print '<option value="' . (int)$staff[0] . '" selected>' . esc_html($staff[1]) . '</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>

        </form>
    </div>
</div>

<div class="fs-modal-footer">
    <button type="button" class="btn btn-lg btn-outline-secondary" data-dismiss="modal"><?php print bkntc__('CANCEL') ?></button>
    <button type="button" class="btn btn-lg btn-primary" id="addGiftcardSave"><?php print bkntc__('ADD GIFTCARD') ?></button>
</div>