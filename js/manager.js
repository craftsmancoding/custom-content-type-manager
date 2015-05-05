/*------------------------------------------------------------------------------
Note that the incrementor cctm[fieldname] is set in wrapper/_text_multi.tpl
@param	string fieldname is the CSS ID of the field we're adding to.
------------------------------------------------------------------------------*/
function add_field_instance(fieldname) {
	// Increment the instance
	cctm[fieldname] = cctm[fieldname] + 1;
	
	var data = {
	        "action" : 'get_tpl',
	        "fieldname" : fieldname,
	        "instance" : cctm[fieldname],
	        "get_tpl_nonce" : cctm.ajax_nonce
	    };

	jQuery.post(
	    cctm.ajax_url,
	    data,
	    function( response ) {
	    	//alert('cctm_instance_wrapper_'+fieldname);
	    	// Write the response to the div
			jQuery('#cctm_instance_wrapper_'+fieldname).append(response);
	    }
	);
	
	return false;
}

/*------------------------------------------------------------------------------
Used to add fields in bulk
@param	string field_type
------------------------------------------------------------------------------*/
function add_bulk_field(field_type) {
	var data = {
	        "action" : 'bulk_add',
	        "field_type" : field_type,
	        "bulk_add_nonce" : cctm.ajax_nonce
	    };

	jQuery.post(
	    cctm.ajax_url,
	    data,
	    function( response ) {
	    	// Write the response to the div
			jQuery('#custom-field-list').append(response);
	    }
	);
	
	return false;
}

/*------------------------------------------------------------------------------
Pastes the shortcode back into WP.
Copied from wp-admin/media-upload.js send_to_editor() function -- I couldn't 
find where that JS is queued up, so I just copied this one function.
------------------------------------------------------------------------------*/
function insert_shortcode(h) {
	var ed;

	if ( typeof tinyMCE != 'undefined' && ( ed = tinyMCE.activeEditor ) && !ed.isHidden() ) {
		// restore caret position on IE
		if ( tinymce.isIE && ed.windowManager.insertimagebookmark )
			ed.selection.moveToBookmark(ed.windowManager.insertimagebookmark);

		if ( h.indexOf('[caption') === 0 ) {
			if ( ed.plugins.wpeditimage )
				h = ed.plugins.wpeditimage._do_shcode(h);
		} else if ( h.indexOf('[gallery') === 0 ) {
			if ( ed.plugins.wpgallery )
				h = ed.plugins.wpgallery._do_gallery(h);
		} else if ( h.indexOf('[embed') === 0 ) {
			if ( ed.plugins.wordpress )
				h = ed.plugins.wordpress._setEmbed(h);
		}

		ed.execCommand('mceInsertContent', false, h);

	} else if ( typeof edInsertContent == 'function' ) {
		edInsertContent(edCanvas, h);
	} else {
		jQuery( edCanvas ).val( jQuery( edCanvas ).val() + h );
	}

	tb_remove();
}

/*------------------------------------------------------------------------------
When a specific validator is selected (when creating/editing a field def), this 
handles the Ajax request to get the necessary html

Requires that the #validator selection option is set.
------------------------------------------------------------------------------*/
function get_validator_options(){
	
	var validator = jQuery('#validator option:selected').val(); // the selected validator
	
	// no action when there's no validator.
	if (!validator) {
		jQuery('#validator_options').html('');
		return;
	}
	
	var data = {
	        "action" : 'get_validator_options',
	        "get_validator_options_nonce" : cctm.ajax_nonce,
	        "validator": validator
	    };
	    
	jQuery.post(
	    cctm.ajax_url,
	    data,
	    function( response ) {
	    	//
	    	//alert(response);
	    	jQuery('#validator_options').html(response);
	    }
	);
}

/*------------------------------------------------------------------------------
Handles dynamically adding a field to filter by, e.g. my_custom_field=value

@param	string	id of the dropdown field that it determining which field to create
@param	string id of div where new field elements will be appended to
------------------------------------------------------------------------------*/
function generate_field_filter(dropdown_id, target_id) {
	var raw = jQuery('#'+dropdown_id).val();

	var fieldname = raw; // default
	var fieldlabel = raw;

	var matches = raw.match(/^(.*?):(.*)$/);
	if (matches != null) {
		fieldname = matches[1];
		fieldlabel = matches[2];
	}


	var form_element = '<div id="'+fieldname+'_wrapper"><label for="'+fieldname+'">'+fieldlabel+'</label><input type="text" id="'+fieldname+'" name="'+fieldname+'" value=""/><span class="cctm_close_rollover" onclick="javascript:remove_html(\''+fieldname+'_wrapper\');"></span><br/></div>';
	
	jQuery('#'+target_id).append(form_element);
}

/*------------------------------------------------------------------------------
When the summarize posts thickbox submits, this is what takes the submission 
and converts it into a shortcode.
------------------------------------------------------------------------------*/
function generate_shortcode(form_id) {
	var data = {
	        "action" : 'get_shortcode',
	        "get_shortcode_nonce" : cctm.ajax_nonce
	    };
	    
	data.search_parameters = jQuery('#'+form_id).serialize();
	
	jQuery.post(
	    cctm.ajax_url,
	    data,
	    function( response ) {
	    	return insert_shortcode(response);
	    }
	);

}



/*------------------------------------------------------------------------------
Generic function. Remove the HTML identified by the target_id
@param	string	target_id -- CSS id of the item to be removed.
------------------------------------------------------------------------------*/
function remove_html( target_id ) {
	jQuery('#'+target_id).remove();
	jQuery('#default_value').val(''); // <-- used in the field definitions
}

/*------------------------------------------------------------------------------
Remove all selected posts from the repeatable field
@param	string	CSS field id, e.g. cctm_myimage
------------------------------------------------------------------------------*/
function remove_all_relations(field_id) {
	jQuery('#cctm_instance_wrapper_'+field_id).html('');
}


/*------------------------------------------------------------------------------
This is called by the TinyMCE button click.  Make sure this function name 
matched the one in editor_plugin.js!
------------------------------------------------------------------------------*/
function show_custom_fields() {
	// Read post-type from the page (WP stores it in a hidden field)
	var post_type = jQuery('#post_type').val();

	// Make us a place for the thickbox
	jQuery('body').append('<div id="cctm_fields_thickbox"></div>');

	// Prepare the AJAX query
	var data = {
	        "action" : 'list_custom_fields',
	        "list_custom_fields_nonce" : cctm.ajax_nonce,
	        "post_type":post_type
	    };
	    
	jQuery.post(
	    cctm.ajax_url,
	    data,
	    function( response ) {
	    	
	    	// Write the response to the div
			jQuery('#cctm_fields_thickbox').html(response);

			var width = jQuery(window).width(), H = jQuery(window).height(), W = ( 720 < width ) ? 720 : width;
			W = W - 80;
			H = H - 114; // 84?
			// then thickbox the div
			tb_show('', '#TB_inline?width=' + W + '&height=' + H + '&inlineId=cctm_fields_thickbox' );			
	    }
	);
	
}

/*------------------------------------------------------------------------------
This is called by the TinyMCE button click.  Make sure this function name 
matched the one in editor_plugin.js!
------------------------------------------------------------------------------*/
function show_summarize_posts() {
	// Make us a place for the thickbox
	jQuery('body').append('<div id="summarize_posts_thickbox"></div>');

	// Prepare the AJAX query
	var data = {
	        "action" : 'summarize_posts_form',
	        "summarize_posts_form_nonce" : cctm.ajax_nonce
	    };
	    
	jQuery.post(
	    cctm.ajax_url,
	    data,
	    function( response ) {
	    	
	    	// Write the response to the div
			jQuery('#summarize_posts_thickbox').html(response);

			var width = jQuery(window).width(), H = jQuery(window).height(), W = ( 720 < width ) ? 720 : width;
			W = W - 80;
			H = H - 114; // 84?
			// then thickbox the div
			tb_show('', '#TB_inline?width=' + W + '&height=' + H + '&inlineId=summarize_posts_thickbox' );			
	    }
	);
	
}


