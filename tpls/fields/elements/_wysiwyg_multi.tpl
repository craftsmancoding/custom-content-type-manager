<span class="cctm_wysiwyg_wrapper" id="instance_[+id_prefix+][+id+][+i+]">
	<script type="text/javascript">
		jQuery( document ).ready( function() {
			jQuery("[+id_prefix+][+id+][+i+]").addClass( "mceEditor" );
			if ( typeof( tinyMCE ) == "object" && typeof( tinyMCE.execCommand ) == "function" ) {
				tinyMCE.execCommand( "mceAddControl", false, "[+id_prefix+][+id+][+i+]" );
			}
			else {
				console.log('TinyMCE is not properly initialized.');
			}
	
			edCanvas = document.getElementById("[+id_prefix+][+id+][+i+]");
	
		});
	</script>		
	<p align="right">
	  <a class="button" onclick="javascript:show_rtf_view('[+id_prefix+][+id+][+i+]');">Visual</a>
	  <a class="button" onclick="javascript:show_html_view('[+id_prefix+][+id+][+i+]');">HTML</a>
	  <span class="cctm_text cctm_close_rollover [+class+]" onclick="javascript:remove_html('instance_[+id_prefix+][+id+][+i+]');"></span>
	</p>
	
	<textarea name="[+name_prefix+][+name+][]" class="cctm_wysiwyg [+class+]" id="[+id_prefix+][+id+][+i+]" [+extra+]>[+value+]</textarea>
	
	<br/><br/>
</span>