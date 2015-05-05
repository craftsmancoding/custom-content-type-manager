<span class="cctm_relation" id="cctm_post_[+id+][+post_id+]">
	<input type="hidden" name="[+name_prefix+][+name+]" id="[+id_prefix+][+id+]" value="[+post_id+]"/>
	<table>
		<tr>
			<td>
				<a href="[+guid+]" target="_blank" title="[+preview+]">
					<img class="cctm_tiny_thumb" src="[+thumbnail_url+]" height="32" width="32" alt=""/>
				</a>
			</td>
			<td>
				<p>[+post_title+] <span class="cctm_id_label">([+post_id+])</span>
				<span class="cctm_close_rollover" onclick="javascript:remove_html('cctm_post_[+id+][+post_id+]');"></span><br/>
				[+post_date+]</p>
			</td>
		</tr>
	</table>
</span>