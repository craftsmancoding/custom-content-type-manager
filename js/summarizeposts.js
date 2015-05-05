/*------------------------------------------------------------------------------
This is called by the Widget button click.
@param	storage_field id of hidden field that will store the search parameters
------------------------------------------------------------------------------*/
function widget_summarize_posts(storage_field) {
	// Make us a place for the thickbox
	jQuery('body').append('<div id="summarize_posts_thickbox"></div>');
	
	var search_parameters = jQuery('#'+storage_field).val();
	
	// Prepare the AJAX query
	var data = {
	        "action" : 'summarize_posts_widget',
	        "summarize_posts_widget_nonce" : cctm.ajax_nonce,
	        "storage_field": storage_field,
	        "search_parameters": search_parameters
	    };
	    
	jQuery.post(
	    cctm.ajax_url,
	    data,
	    function( response ) {
	    	
	    	// Write the response to the div -- TODO this for the widget
			jQuery('#summarize_posts_thickbox').html(response);

			var width = jQuery(window).width(), H = jQuery(window).height(), W = ( 720 < width ) ? 720 : width;
			W = W - 80;
			H = H - 114; // 84?
			// then thickbox the div
			tb_show('', '#TB_inline?width=' + W + '&height=' + H + '&inlineId=summarize_posts_thickbox' );			
	    }
	);
	
}

/*------------------------------------------------------------------------------

@param	string	form_id: the form that contains the search parameters
@param	string	storage_field: the id of the field that will store the search parameters
------------------------------------------------------------------------------*/
function save_widget_criteria(form_id, storage_field) {
	
	var search_parameters = jQuery('#'+form_id).serialize();
	
	// Write the search_parameters to the form
	jQuery('#'+storage_field).val(search_parameters);

	// Prepare the AJAX query: we use this to format the field results.
	var data = {
	        "action" : 'format_getpostsquery_args',
	        "format_getpostsquery_args_nonce" : cctm.ajax_nonce,
	        "search_parameters": search_parameters
	    };

	// Write Formatted search parameters to the page
	jQuery.post(
	    cctm.ajax_url,
	    data,
	    function( response ) {
	    	jQuery('#existing_'+storage_field).html(response);
	    }
	);

	tb_remove();
}