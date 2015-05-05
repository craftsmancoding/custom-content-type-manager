<label for="[+id_prefix+][+search_term.id+]" class="[+label_class+]" id="[+search_term.id+]_label">[+search_term.label+]</label>
<input class="[+input_class+] input_field" type="text" name="[+name_prefix+][+search_term.id+]" id="[+id_prefix+][+search_term.id+]" value="[+search_term.value+]" />

<input type="hidden" name="post_type" id="post_type" value="[+post_type+]" />
<input type="hidden" name="target_id" id="target_id" value="[+target_id+]" />
<input type="hidden" name="post_id_field" id="post_id_field" value="[+post_id_field+]" />

<select size="[+yearmonth.size+]" name="[+name_prefix+][+yearmonth.name+]" class="[+input_class+]" id="[+id_prefix+][+yearmonth.id+]">
	<option value="">[+show_all_dates+]</option>
	[+yearmonth.options+]
</select>


<span class="button" onclick="javascript:thickbox_refine_search();">[+filter+]</span>

<span class="button" onclick="javascript:thickbox_reset_search();">[+show_all+]</span>
