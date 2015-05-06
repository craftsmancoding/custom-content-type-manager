<?php
/*------------------------------------------------------------------------------
This template is used as the basis for thickboxes launched by relation fields.
The form there is important: select_posts_form.  That id is referenced by several
javscript functions, so it should not be changed.  The form's variables are 
used to pass search parameters from page to page of the paginated results via
serialization.
------------------------------------------------------------------------------*/
?>
<div id="cctm_thickbox">

	<script type="text/javascript">
		// http://www.bloggingdeveloper.com/post/Disable-Form-Submit-on-Enter-Key-Press.aspx
		function disableEnterKey(e)
		{
		     var key;      
		     if(window.event)
		          key = window.event.keyCode; //IE
		     else
		          key = e.which; //firefox      
		
		     return (key != 13);
		}
	</script>


	<form id="select_posts_form" onkeypress="return disableEnterKey(event)">
		<input type="hidden" name="fieldname" id="fieldname" value="<?php print $data['fieldname']; ?>" />
		<input type="hidden" name="fieldtype" id="fieldtype" value="<?php print $data['fieldtype']; ?>" />
		<input type="hidden" name="page_number" id="page_number" value="<?php print $data['page_number']; ?>" />
		<input type="hidden" name="orderby" id="orderby" value="<?php print $data['orderby']; ?>" />
		<input type="hidden" name="order" id="order" value="<?php print $data['order']; ?>" />
		<input type="hidden" name="exclude" id="exclude" value="<?php //print implode(',',$data['exclude']); ?>" />

		<div id="cctm_thickbox_menu">
			<?php print $data['menu']; ?>	
		</div>
	
		<div id="cctm_search_posts_form">
			<?php print $data['search_form']; ?>	
		</div>
	
		<div id="cctm_thickbox_content">
			<?php print $data['content']; ?>
		</div>
	</form>
</div>