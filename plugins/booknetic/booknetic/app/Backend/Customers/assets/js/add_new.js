(function ($)
{
	"use strict";

	$(document).ready(function()
	{

		let current_modal = $('#addCustomerSave').closest('.fs-modal');

		current_modal.on('click', '#addCustomerSave', function ()
		{
			var wp_user	                    = $("#input_wp_user").val(),
				first_name	                = $("#input_first_name").val(),
				last_name	                = $("#input_last_name").val(),
				gender		                = $("#input_gender").val(),
				birthday	                = $("#input_birthday").val(),
				phone		                = $("#input_phone").data('iti').getNumber(intlTelInputUtils.numberFormat.E164),
				email		                = $("#input_email").val(),
				allow_customer_to_login	    = $("#input_allow_customer_to_login").is(':checked') ? 1 : 0,
				wp_user_use_existing	    = $("#input_wp_user_use_existing").val(),
				wp_user_password		    = $("#input_wp_user_password").val(),
				note		                = $("#input_note").val(),
				image		                = $("#input_image")[0].files[0];

			if( first_name === '' || (last_name === '' && !$("#input_last_name").parent().is('.hidden')) )
			{
				booknetic.toast(booknetic.__('fill_all_required'), 'unsuccess');
				return;
			}

			var data = new FormData();

			data.append('id', $('#add_new_JS').data('customer-id'));
			data.append('wp_user', wp_user);
			data.append('first_name', first_name);
			data.append('last_name', last_name);
			data.append('gender', gender);
			data.append('birthday', birthday);
			data.append('phone', phone);
			data.append('email', email);
			data.append('allow_customer_to_login', allow_customer_to_login);
			data.append('wp_user_use_existing', wp_user_use_existing);
			data.append('wp_user_password', wp_user_password);
			data.append('note', note);
			data.append('image', image);

			booknetic.ajax( 'Customers.save_customer', data, function()
			{
				booknetic.modalHide( current_modal );

				if( $("#fs_data_table_div").length )
				{
					booknetic.dataTable.reload( $("#fs_data_table_div") );
				}
			});
		}).on('change', '#input_allow_customer_to_login', function ()
		{
			if( $(this).is(':checked') )
			{
				$('[data-hide="allow_customer_to_login"]').slideDown(200);
				$('#input_wp_user_use_existing').trigger('change');

			}
			else
			{
				$('[data-hide="allow_customer_to_login"]').slideUp(200);
				$('[data-hide="existing_user"]').slideUp(200);
				$('[data-hide="create_password"]').slideUp(200);
			}
		}).on('change', '#input_wp_user_use_existing', function ()
		{
			if( $(this).val() === 'yes' )
			{
				$('[data-hide="existing_user"]').show();
				$('[data-hide="create_password"]').hide();
			}
			else
			{
				$('[data-hide="existing_user"]').hide();
				$('[data-hide="create_password"]').show();
			}
		});

		$('#input_wp_user_use_existing').trigger('change');
		$('#input_allow_customer_to_login').trigger('change');

		var phone_input = $('#input_phone');
		phone_input.data('iti', window.intlTelInput( phone_input[0], {
			initialCountry: phone_input.data('country-code')
		}));

		$("#input_birthday").datepicker({
			autoclose: true,
			format: 'yyyy-mm-dd',
			weekStart: weekStartsOn == 'sunday' ? 0 : 1
		});

		$('#input_wp_user, #input_gender').select2({
			theme: 'bootstrap',
			placeholder: booknetic.__('select'),
			allowClear: true
		});

	});

})(jQuery);