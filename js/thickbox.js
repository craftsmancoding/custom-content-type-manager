/**
 * This is a modified version of the Thickbox file: we use it to override the built-in 
 * thickbox's "tb_remove" function because it does not play well with JQuery tabs.
 * See http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=192&sort=milestone
 *
 * To see if this is having the desired effect, create or edit a custom post-type,
 * then click one of the "help" icons (?).  After doing this, verify that the jQuery
 * tabs still operate (e.g. try clicking on the "Advanced" tab).
 */

function tb_remove() {
 	jQuery("#TB_imageOff").unbind("click");
	jQuery("#TB_closeWindowButton").unbind("click");
//	jQuery("#TB_window").fadeOut("fast",function(){jQuery('#TB_window,#TB_overlay,#TB_HideSelect').trigger("unload").unbind().remove();});
	jQuery("#TB_window").fadeOut("fast",function(){jQuery('#TB_window,#TB_overlay,#TB_HideSelect').unload("#TB_ajaxContent").unbind().remove();});
	jQuery("#TB_load").remove();
	if (typeof document.body.style.maxHeight == "undefined") {//if IE 6
		jQuery("body","html").css({height: "auto", width: "auto"});
		jQuery("html").css("overflow","");
	}
	jQuery(document).unbind('.thickbox');
	return false;
}