/*------------------------------------------------------------------------------
 Include this file to fire off an Ajax request to download a CCTM file.
 This doesn't work because the response comes back to *this* page, when it 
 needs to go back to the user's browser.
------------------------------------------------------------------------------*/
jQuery(document).ready(function() {
	jQuery.post(
	    cctm.ajax_url,
	    {
	        action : 'download_def',
	        download_def_nonce : cctm.ajax_nonce
	    },
	    function( response ) { 
	    	print response;
	    }
	);
});
