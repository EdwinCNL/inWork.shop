(function ($)
{
    "use strict";

    $(document).ready(function()
    {

        $(document).on('click', '#add_tab_btn', function ()
        {
            var current_module = $('#add_tab_btn').data('module')

            booknetic.loadModal('NotificationTab.add_tab', {current_module: current_module ,id: 0},{});
        });

        $(document).on('click', '[data-tabEdit="edit"]', function ()
        {
            var id = $(this).data('tab_id'),
                current_module = $('#add_tab_btn').data('module');
            booknetic.loadModal('NotificationTab.add_tab', {current_module: current_module ,id: id},{});
        });

        $(document).on('click', '[data-tab="delete"]', function ()
        {
            var id = $(this).data('tab_id'),
                current_module = $('#add_tab_btn').data('module');

            booknetic.confirm(booknetic.__('are_you_sure_want_to_delete'), 'danger', 'trash', function()
            {
                booknetic.ajax('NotificationTab.delete_tab', { id, current_module: current_module }, function(data) {
                    location.reload();
                });
            });

        });


    });
})(jQuery);
