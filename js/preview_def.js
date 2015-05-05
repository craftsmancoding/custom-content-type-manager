function preview_def(filename){
	jQuery.post(
	    cctm.ajax_url,
	    {
	        "action" : 'preview_def',
	        "file": filename,
	        "preview_def_nonce" : cctm.ajax_nonce
	    },
	    function( response ) { 
		    jQuery('#cctm_def_preview_target').html(response);
	    }
	);


/*
	jQuery.get('<?php printf( CCTM_URL.'/ajax-controllers/preview.php?_cctm_nonce=%s&file='
		, wp_create_nonce('cctm_preview_def')
		);?>'+filename, function(data) {
	  jQuery('#cctm_def_preview_target').html(data);
	});	
*/
}
