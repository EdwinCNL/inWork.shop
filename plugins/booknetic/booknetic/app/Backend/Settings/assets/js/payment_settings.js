(function ($)
{
	"use strict";

	$(document).ready(function()
	{

		$('#booknetic_settings_area').on('click', '.settings-save-btn', function ()
		{
			var currency							= $("#input_currency").val(),
				currency_symbol						= $("#input_currency_symbol").val(),
				currency_format						= $("#input_currency_format").val(),
				price_number_format					= $("#input_price_number_format").val(),
				price_number_of_decimals			= $("#input_price_number_of_decimals").val(),
				deposit_can_pay_full_amount			= $("#input_deposit_can_pay_full_amount").is(':checked') ? 'on' : 'off',
				max_time_limit_for_payment			= $("#input_max_time_limit_for_payment").val();

			booknetic.ajax('save_payments_settings', {
				currency: currency,
				currency_symbol: currency_symbol,
				currency_format: currency_format,
				price_number_format: price_number_format,
				price_number_of_decimals: price_number_of_decimals,
				deposit_can_pay_full_amount: deposit_can_pay_full_amount,
				max_time_limit_for_payment: max_time_limit_for_payment
			}, function ()
			{
				booknetic.toast(booknetic.__('saved_successfully'), 'success');
			});
		}).on('change', '#input_currency', function ()
		{
			var symbol = $(this).children(':selected').data('symbol');
			$('#input_currency_symbol').val( symbol );
		});

		$("#input_currency, #input_currency_format, #input_price_number_format, #input_price_number_of_decimals, #input_max_time_limit_for_payment").select2({
			theme: 'bootstrap',
			placeholder: booknetic.__('select'),
			allowClear: true
		});

		$('.do_tooltip').popover({ trigger: "click", html: true, animation:false})
			.on("mouseenter", function () {
				var _this = this;
				$(this).popover("show");
				$(".popover").on("mouseleave", function () {
					$(_this).popover('hide');
				});
			}).on("mouseleave", function () {
			var _this = this;
			setTimeout(function () {
				if (!$(".popover:hover").length) {
					$(_this).popover("hide");
				}
			}, 300);
		});
	});

})(jQuery);