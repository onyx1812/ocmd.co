jQuery(document).ready(function ($) {

	"use strict";

	// ****** Browse File Starts **********//
	$('#browse_file').click(function () {
		$('#unify_import_tool').trigger("click");
	});

	$('#unify_import_tool').change(function (e) {
		if ($(this).val().split('.').pop().toLowerCase() != 'csv')
		{
			$(this).val('')
			$('#unify_import_status').html('<div class="notice notice-error is-dismissible"><p>Invalid file</p></div>');
			$('#unify_import_tool_read').val('');
		} else {
			var fileName = e.target.files[0].name;
			$('#unify_import_tool_read').val(fileName);
		}
	});

	$('#codeclouds_unify_tool_import').submit(function (event) {

		event.preventDefault();

//		$('.submit').html('<div class="loader"></div>');
		$('#import-submit').addClass('disabled');

		var form_data = new FormData();

		form_data.append('unify_import_tool', $('#unify_import_tool').prop('files')[0]);
		form_data.append('action', 'codeclouds_unify_tool_import');

		$.ajax({
			url: $(this).attr('action'),
			type: 'POST',
			contentType: false,
			processData: false,
			data: form_data,
			success: function (response)
			{
				if (response === '1')
				{
					$('#unify_import_tool').val('');
//                    $('#unify_import_status').html('<div class="notice notice-success is-dismissible"><p>Successful!</p></div>');
//                    $('.submit').html('<input type="submit" name="submit" id="submit" class="button button-primary" value="Upload">');
				} else
				{
					$('#unify_import_tool').val('');
//                    $('#unify_import_status').html('<div class="notice notice-error is-dismissible"><p>Invalid file</p></div>');
//                    $('.submit').html('<input type="submit" name="submit" id="submit" class="button button-primary" value="Upload">');
				}
				$('#import-submit').removeClass('disabled');
				window.location.reload();
			},
			fail: function (error)
			{
				$('#unify_import_tool').val('');
//                $('#unify_import_status').html('<div class="notice notice-error is-dismissible"><p>' + error + '</p></div>');
//                $('.submit').html('<input type="submit" name="submit" id="submit" class="button button-primary" value="Upload">');
				$('#import-submit').removeClass('disabled');
				window.location.reload();
			}
		});

	});

	// ****** Browse File Ends **********//
	
	// ######## OnHover Sorting Title Starts #########

	if($('#orderby').val() != ''){
		var className = ($('#order').val() == 'asc') ? 'fa-caret-up' : 'fa-caret-down';
		$('#'+$('#orderby').val()+'-icn').addClass(className).show();
	}

	$('.sortab').on('hover', function(){
		var orderBy = $(this).attr('data-order-by');
		var order = $(this).attr('data-order');
		var className = (order == 'asc') ? 'fa-caret-up' : 'fa-caret-down';
		if($('#'+orderBy+'-icn').attr('data-hide') != 'false'){
			$('#'+orderBy+'-icn').addClass(className).show();
		}
	});
	
	$('.sortab').on('mouseleave', function(){
		var orderBy = $(this).attr('data-order-by');
		var order = $(this).attr('data-order');
		
		var className = (order == 'asc') ? 'fa-caret-up' : 'fa-caret-down';
		
		if($('#'+orderBy+'-icn').attr('data-hide') != 'false'){
			$('#'+orderBy+'-icn').removeClass(className);
			$('#'+orderBy+'-icn').hide();
		}		
	});
	
	$('.sortab').on('click', function(){
		var orderBy = $(this).attr('data-order-by');
		var order = $(this).attr('data-order');		
		var orderVal = (order == 'desc') ? 'asc' : 'desc';
		$('#check_submit').val('sort_field');
		$('#order').val(orderVal);		
		$('#orderby').val(orderBy);
		$('#unify_product_post').submit();
	});


	// ######## OnHover Sorting Title Ends #########

});