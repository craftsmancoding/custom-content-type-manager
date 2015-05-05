<script type="text/javascript">
	jQuery( document ).ready( function() {
		jQuery("[+id_prefix+][+id+]").addClass( "mceEditor" );
		if ( typeof( tinyMCE ) == "object" && typeof( tinyMCE.execCommand ) == "function" ) {
			tinyMCE.execCommand( "mceAddControl", false, "[+id_prefix+][+id+]" );
		}
		else {
			console.log('TinyMCE is not properly initialized.');
		}

		edCanvas = document.getElementById("[+id_prefix+][+id+]");

	});
</script>		
<p align="right">
  <a class="button" onclick="javascript:show_rtf_view('[+id_prefix+][+id+]');">Visual</a>
  <a class="button" onclick="javascript:show_html_view('[+id_prefix+][+id+]');">HTML</a>
</p>

<textarea name="[+name_prefix+][+name+]" class="cctm_wysiwyg [+class+]" id="[+id_prefix+][+id+]" [+extra+]>[+value+]</textarea>