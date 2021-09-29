<?php
namespace BookneticApp\Frontend\view;

use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Date;
use BookneticApp\Providers\Math;

defined( 'ABSPATH' ) or die();
?>

<link rel="stylesheet" href="<?php print Helper::assets('css/add_new_category.css', 'Services')?>">
<script type="application/javascript" src="<?php print Helper::assets('js/add_new_category.js', 'Services')?>"></script>

<div class="fs-modal-title">
    <div class="title-icon badge-lg badge-purple"><i class="fa fa-plus"></i></div>
    <div class="title-text"><?php print bkntc__('Add Category')?></div>
    <div class="close-btn" data-dismiss="modal"><i class="fa fa-times"></i></div>
</div>

<div class="fs-modal-body">
    <div class="fs-modal-body-inner">
        <form id="addServiceForm">

            <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="input_parent_category"><?php print bkntc__('Parent category')?> <span class="required-star">*</span></label>
                    <select id="input_parent_category" class="form-control">
                        <option value="0"><?php print bkntc__('Root category')?></option>
                        <?php
                            foreach( $parameters['categories'] AS $category )
                            {
                                print '<option value="' . (int)$category['id'] . '"' . ( $parameters['category'] == $category['id'] ? ' selected' : '' ) . '>' . htmlspecialchars($category['name']) . '</option>';
                            }
                        ?>
                    </select>
                </div>
                <div class="form-group col-md-12">
                    <label for="new_category_name"><?php print bkntc__('Category name')?> <span class="required-star">*</span></label>
                    <input type="text" class="form-control" id="new_category_name">
                </div>
            </div>

        </form>
    </div>
</div>

<div class="fs-modal-footer">
    <button type="button" class="btn btn-lg btn-default" data-dismiss="modal"><?php print bkntc__('CLOSE')?></button>
    <button type="button" class="btn btn-lg btn-primary" id="save_new_category"><?php print bkntc__('SAVE')?></button>
</div>