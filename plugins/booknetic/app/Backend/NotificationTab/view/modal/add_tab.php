<?php
namespace BookneticApp\Frontend\view;

use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Date;

defined( 'ABSPATH' ) or die();

$services = isset($parameters['tab']['services']) && $parameters['tab']['services'] ?  explode(',',$parameters['tab']['services']) : [];
$staff_ = isset($parameters['tab']['staff']) && $parameters['tab']['staff'] ? explode(',',$parameters['tab']['staff']) : [];
$locations = isset($parameters['tab']['locations']) && $parameters['tab']['locations'] ?  explode(',',$parameters['tab']['locations']) : [];
$notification_types = isset($parameters['tab']['notification_types']) && $parameters['tab']['notification_types'] ?  explode(',',$parameters['tab']['notification_types']) : [];
$language = isset($parameters['tab']['languages']) && $parameters['tab']['languages'] ? $parameters['tab']['languages'] : Helper::getOption('default_language', '');

?>

<script type="application/javascript" src="<?php print Helper::assets('js/add_tab.js', 'NotificationTab')?>" id="add_new_JS"  data-tab-id="<?php print $parameters['id']?>"></script>

<div class="fs-modal-title">
    <div class="title-icon badge-lg badge-purple"><i class="fa fa-plus"></i></div>
    <div class="title-text"><?php print $parameters['tab'] ? bkntc__('Tab Edit') : bkntc__('Add Tab')?></div>
    <div class="close-btn" data-dismiss="modal"><i class="fa fa-times"></i></div>
</div>

<div class="fs-modal-body">
    <input type="text" hidden id="current_module" name="current_module" value="<?php print $parameters['current_module']; ?>">
    <div class="fs-modal-body-inner">
        <div class="form-group">
            <label><?php print bkntc__('Select Type')?></label>
            <?php if($parameters['id']):?>
                <div class="fs_notification_tab" data-id="3087">
                    <div class="fsn_title"><?php print $parameters['tab']['notification_types']; ?></div>
                </div>
            <?php else: ?>
            <select name="add_tab_notification_types[]" id="add_tab_notification_types" multiple class="form-control">
                <?php
                foreach( $parameters['notification_types'] AS $key => $type )
                {
                    $select = in_array($key,$notification_types) ? 'selected' : '';
                    print '<option selected '.$select.' value="'.$key.'">' . htmlspecialchars($type) . '</option>';
                }
                ?>
            </select>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label><?php print bkntc__('Tab Name')?></label>
            <input type="text" class="form-control" name="name" id="add_tab_name" value="<?php print htmlspecialchars(isset($parameters['tab']['name']) ? $parameters['tab']['name'] : '')?>" placeholder="<?php print bkntc__('Enter Tab Name')?>">
        </div>
        <div class="form-group">
            <label><?php print bkntc__('Select Language')?></label>
            <?php
            print str_replace( ['<select name=', 'value=""'], ['<select class="form-control" name=', 'value="en_US"'], wp_dropdown_languages([
                'id'        =>  'add_tab_language',
                'echo'      =>  false,
                'selected'  =>  $language
            ]));
            ?>
        </div>
        <div class="form-group">
            <label><?php print bkntc__('Select Service')?></label>
            <select name="service_ids[]" id="add_tab_service_ids" multiple class="form-control">
                <?php
                foreach( $parameters['services'] AS $service )
                {
                    $select = in_array($service['id'], $services) ? 'selected' : '';
                    print '<option '.$select.' value="'.$service['id'].'">' . htmlspecialchars($service['name']) . '</option>';
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label><?php print bkntc__('Select Staff')?></label>
            <select name="staff_ids[]" id="add_tab_staff_ids" multiple class="form-control">
                <?php
                foreach( $parameters['staff'] AS $staff )
                {
                    $select = in_array($staff['id'],$staff_) ? 'selected' : '';
                    print '<option '.$select.' value="'.$staff['id'].'">' . htmlspecialchars($staff['name']) . '</option>';
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label><?php print bkntc__('Select Location')?></label>
            <select name="location_ids[]" id="add_tab_location_ids" multiple class="form-control">
                <?php
                foreach( $parameters['locations'] AS $location )
                {
                    $select = in_array($location['id'],$locations) ? 'selected' : '';
                    print '<option '.$select.' value="'.$location['id'].'">' . htmlspecialchars($location['name']) . '</option>';
                }
                ?>
            </select>
        </div>

    </div>
</div>

<div class="fs-modal-footer">
	<button type="button" class="btn btn-lg btn-outline-secondary" data-dismiss="modal"><?php print bkntc__('CANCEL')?></button>
	<button type="button" class="btn btn-lg btn-primary" id="saveTabBtn"><?php print $parameters['tab'] ? bkntc__('SAVE') : bkntc__('ADD TAB')?></button>
</div>
