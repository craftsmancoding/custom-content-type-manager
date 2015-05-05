<?php
/*------------------------------------------------------------------------------
Pretty much the same as thickbox.php, but without the cctm_thickbox div wrapper:
used when flipping through paginated result sets.
------------------------------------------------------------------------------*/
?>

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


	<!--form id="select_posts_form" onkeypress="return disableEnterKey(event)"-->
	<form id="select_posts_form">
		<input type="hidden" name="fieldname" id="fieldname" value="<?php print $data['fieldname']; ?>" />
		<input type="hidden" name="fieldtype" id="fieldtype" value="<?php print $data['fieldtype']; ?>" />
		<input type="hidden" name="page_number" id="page_number" value="<?php print $data['page_number']; ?>" />
		<input type="hidden" name="orderby" id="orderby" value="<?php print $data['orderby']; ?>" />
		<input type="hidden" name="order" id="order" value="<?php print $data['order']; ?>" />
		
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