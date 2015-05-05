<script type="text/javascript">
	jQuery('.checkall').click(function () {
		jQuery(this).parents('fieldset:eq(0)').find(':checkbox').attr('checked', this.checked);
	});
</script>

<div class="cctm_thickbox_menu">
	<span class="button primary" onclick="javascript:send_selected_posts_to_wp();">[+add_to_post+]</span> <span class="button" onclick="javascript:save_and_close();">[+add_to_post_and_close+]</span>
</div>

<!-- fieldset used by the checkall function -->
<fieldset>
<table class="wp-list-table widefat">
	<thead>
		<tr>
			<th><input type="checkbox" class="checkall" /></th>
			<th class="manage-column column-title sortable"><a href="javascript:thickbox_sort_results('post_title');">[+post_title+]</a></th>
			<th class="manage-column column-title sortable"><a href="javascript:thickbox_sort_results('post_type');">[+post_type+]</a></th>
			<th class="manage-column column-title sortable"><a href="javascript:thickbox_sort_results('post_date');">[+post_date+]</a></th>
			<th>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		[+content+]
	</tbody>

	<tfoot>
		<tr>
			<th>&nbsp;</th>
			<th class="manage-column column-title sortable"><a href="javascript:thickbox_sort_results('post_title');">[+post_title+]</a></th>
			<th class="manage-column column-title sortable"><a href="javascript:thickbox_sort_results('post_type');">[+post_type+]</a></th>
			<th class="manage-column column-title sortable"><a href="javascript:thickbox_sort_results('post_date');">[+post_date+]</a></th>
			<th>&nbsp;</th>
		</tr>
	</tfoot>
</table>
</fieldset>