/*------------------------------------------------------------------------------
Drives the functionality of the Post Content widget.
------------------------------------------------------------------------------*/

//------------------------------------------------------------------------------
//! FUNCTIONS
//------------------------------------------------------------------------------

/*------------------------------------------------------------------------------
Used for flipping through pages of thickbox'd search results.
------------------------------------------------------------------------------*/
function change_page(page_number) {

	jQuery('#page_number').val(page_number); // store the value so it can be serialized

	var post_id_field = jQuery('#post_id_field').val();
	var post_type = jQuery('#post_type').val();
	var target_id = jQuery('#target_id').val();
	
	var data = {
	        "action" : 'post_content_widget',
	        "post_content_widget_nonce" : cctm.ajax_nonce,
	       	"post_id_field" : post_id_field,
	        "post_type" : post_type,
	        "target_id" : target_id,
	    };
	    
	data.search_parameters = jQuery('#select_posts_form').serialize();

	jQuery.post(
	    cctm.ajax_url,
	    data,
	    function( response ) {
	    	// Write the response to the div
			jQuery('#cctm_thickbox_content').html(response);
	    }
	);
	return false;
}



/*------------------------------------------------------------------------------
Remove the associated image, media, or relation item.  This means the hidden 
field that stores the actual value must be set to null and the preview hmtl
must be cleared.
@param 	string	target_id is the hidden field id that needs to be nulled
@param	string	target_html is the id of the div whose html needs to be cleared
------------------------------------------------------------------------------*/
function remove_relation( target_id, target_html ) {
	jQuery('#'+target_id).val('');
	jQuery('#'+target_html).html('');	
}


/*------------------------------------------------------------------------------
Shows a search form from the "edit custom field" definition.
We send along the fieldname and fieldtype to allow for customizations.
On new definitions, the behavior defaults to the fieldtype because we don't 
yet have a fieldname.

@param	fieldname	string	name of the field
@param	fieldtyp	string	type of field (e.g. relation, image)
------------------------------------------------------------------------------*/
function search_form_display(fieldname,fieldtype) {
	var search_parameters = jQuery('#search_parameters').val();
	//alert(search_parameters);
	var data = {
	        "action" : 'get_search_form',
	        "post_type": jQuery('#post_type').val(),
	        "fieldname" : fieldname,
	        "fieldtype" : fieldtype,
	        "search_parameters" : search_parameters,
	        "get_search_form_nonce" : cctm.ajax_nonce
	    };
	    
	// data.search_parameters = jQuery('#select_posts_form').serialize();
	
	jQuery.post(
	    cctm.ajax_url,
	    data,
	    function( response ) {
	    	// Write the response to the div
			jQuery('#cctm_thickbox').html(response);	

			var width = jQuery(window).width(), H = jQuery(window).height(), W = ( 720 < width ) ? 720 : width;
			W = W - 80;
			H = H - 124;
			// then thickbox the div
			tb_show('', '#TB_inline?width=' + W + '&height=' + H + '&inlineId=cctm_thickbox' );			


	    }
	);
}



/*------------------------------------------------------------------------------
Inside the thickbox, select a post and send it back to WordPress.
Similar to the send_selected_posts_to_wp() function.
@param	integer	post_id is the ID of the attachment that has been selected
------------------------------------------------------------------------------*/
function send_single_post_to_wp( post_id ) {
	// It's easier to read it from a hidden field than it is to pass it to this function
	var target_id = jQuery('#target_id').val();
	var post_id_field = jQuery('#post_id_field').val();
	
	//console.log('here...' + fieldname);
	var data = {
	        "action" : 'get_widget_post_tpl',
	        "post_type": jQuery('#post_type').val(),
	        "get_widget_post_tpl_nonce" : cctm.ajax_nonce,
	       	"post_id": post_id
	    };

	jQuery('#'+post_id_field).val(post_id); // set the post_id value
	
	jQuery.post(
	    cctm.ajax_url,
	    data,
	    function( response ) {
	    	//alert('cctm_instance_wrapper_'+fieldname);
	    	// Write the response to the div
			jQuery('#'+target_id).html(response);
	    }
	);

	tb_remove();
	// jQuery('#default_value').val(post_id); //<-- used when setting default values in field defs
	return false;
}

/*------------------------------------------------------------------------------
Refining a search
Similar to thickbox_reset_search(), but this appends to the previous search params.
E.g. type a search term, press the "Filter" button.  Any current values on the 
search form are read and preserved.

@param	string	form_id: the form which contains the additional search parameters
------------------------------------------------------------------------------*/
function thickbox_refine_search() {
	// It's easier to read it from a hidden field than it is to pass it to this function
	var fieldname = jQuery('#fieldname').val();
	
	jQuery('#page_number').val('0');
	
	var data = 
		{
	        "action" : 'post_content_widget',
	        "post_type": jQuery('#post_type').val(),
	        "post_content_widget_nonce" : cctm.ajax_nonce
	    };
	// This is how we maintain our existing parameters.
	data.search_parameters = jQuery('#select_posts_form').serialize();

	jQuery.post(
	    cctm.ajax_url,
	    data,
	    function( response ) {
	    	// Write the response to the div
			jQuery('#cctm_thickbox_content').html(response);
			
	    }
	);	
	
}


/*------------------------------------------------------------------------------
Reset search -- back to the original results
@param	string	form_id: the form which contains the additional search parameters
------------------------------------------------------------------------------*/
function thickbox_reset_search() {

	var post_type = jQuery('#post_type').val();
	
	jQuery('#page_number').val('0');
	
	var data = 
		{
	        "action" : 'post_content_widget',
	        "post_content_widget_nonce" : cctm.ajax_nonce,
	        "post_type" : post_type
	    };
	
	jQuery.post(
	    cctm.ajax_url,
	    data,
	    function( response ) {
	    	// Write the response to the div
			jQuery('#cctm_thickbox_content').html(response);
			
	    }
	);
}

/*------------------------------------------------------------------------------
This is the generic CCTM thickbox showing selectable search results: 
i.e. the "Post Selector".  It allows user to select one or many posts for use 
in a relation field (image, media).

If omit_existing_values is passed as true, then the post-selector pop-up
will not display any posts that have already been selected. This creates 
a behavior similar to those web forms where you can move items from one
list to another.  We use it for the multi-select fields where we don't 
want the user adding the same post over and over again.

@param	string post_id_field	CSS id of where we need to store the selected post_id
@param	string target_id	CSS name of the div where we write the tpl displaying the selected post
@param	string post_type_field_id	the id of the select element containing the post-type options.
------------------------------------------------------------------------------*/
function select_post(post_id_field, target_id, post_type_field_id) {

	var post_type = jQuery('#'+post_type_field_id).val();;
	
	
	jQuery.post(
	    cctm.ajax_url,
	    {
	        "action" : 'post_content_widget',
	        "post_content_widget_nonce" : cctm.ajax_nonce,
	        "post_id_field" : post_id_field,
	        "post_type" : post_type,
	        "target_id" : target_id,
	        "wrap_thickbox": 1
	    },
	    function( response ) {
	    	// Write the response to the div
			jQuery('#thickbox_'+target_id).html(response);
			//tb_remove();
			var width = jQuery(window).width(), H = jQuery(window).height(), W = ( 720 < width ) ? 720 : width;
			W = W - 80;
			H = H - 84;
			// then thickbox the div
			tb_show('', '#TB_inline?width=' + W + '&height=' + H + '&inlineId=thickbox_'+target_id );			

	    }
	);	
}

/*------------------------------------------------------------------------------
Fired when a column header is clicked.  
@param	string	sort_column the column we want to sort by.
------------------------------------------------------------------------------*/
function thickbox_sort_results(sort_column) {
		// It's easier to read it from a hidden field than it is to pass it to this function
	var order = jQuery('#order').val();
	var orderby = jQuery('#orderby').val(); 
	
	// Toggle order if we're already sortying by the 'orderby' column
	if (orderby == sort_column){
		if (order == 'DESC') {
			jQuery('#order').val('ASC');
		}
		else {
			jQuery('#order').val('DESC');
		}
	}
	
	jQuery('#orderby').val(sort_column);
	jQuery('#page_number').val('0'); // go back to first page when we resort
	var data = 
		{
	        "action" : 'post_content_widget',
	        "post_type": jQuery('#post_type').val(),
	        "post_content_widget_nonce" : cctm.ajax_nonce
	    };

	data.search_parameters = jQuery('#select_posts_form').serialize();
	
	jQuery.post(
	    cctm.ajax_url,
	    data,
	    function( response ) {
	    	// Write the response to the div
			jQuery('#cctm_thickbox_content').html(response);
			
	    }
	);	
}