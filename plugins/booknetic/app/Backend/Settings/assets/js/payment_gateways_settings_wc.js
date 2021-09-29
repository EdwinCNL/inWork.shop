(function ($)
{
	"use strict";

	$(document).ready(function ()
	{

		$(document).on('click', '.settings-save-btn', function()
		{
			booknetic.loading(1);
			$('.wc_pg_options_form').submit();
		});

	});

})(jQuery);