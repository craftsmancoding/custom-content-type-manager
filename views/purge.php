<?php
/*------------------------------------------------------------------------------
Used for basic pages.

$results	mixed
$rows	integer -- number of records returned
------------------------------------------------------------------------------*/
?>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
  google.load("visualization", "1", {packages:["corechart"]});
  google.setOnLoadCallback(drawDistributionChart);
  google.setOnLoadCallback(drawActiveInactiveChart);
        
  function drawDistributionChart() {
    var data = new google.visualization.DataTable();
    data.addColumn("string", "<?php _e('Post Type', CCTM_TXTDOMAIN); ?>");
    data.addColumn("number", "<?php _e('Records', CCTM_TXTDOMAIN); ?>");
    data.addRows(<?php print $data['in_use_cnt']; ?>);
<?php
foreach ($data['results'] as $i => $r):
?>
    data.setValue(<?php print $i; ?>, 0, "<?php print $r->post_type; ?>");
    data.setValue(<?php print $i; ?>, 1, <?php print $r->cnt; ?>);
<?php		
endforeach;
?>
    var distributionChart = new google.visualization.PieChart(document.getElementById("post_type_distribution_chart"));
    distributionChart.draw(data, {width: 450, height: 300, title: "<?php _e('Distribution of Posts', CCTM_TXTDOMAIN); ?>"});
    
  }

  function drawActiveInactiveChart() {
    var data = new google.visualization.DataTable();
    data.addColumn("string", "<?php _e('Status', CCTM_TXTDOMAIN); ?>");
    data.addColumn("number", "<?php _e('Count', CCTM_TXTDOMAIN); ?>");
    data.addRows(2);
    data.setValue(0, 0, "<?php _e('Number Active', CCTM_TXTDOMAIN); ?>");
    data.setValue(0, 1, <?php print $data['active_cnt']; ?>);
    data.setValue(1, 0, "<?php _e('Number Inactive', CCTM_TXTDOMAIN); ?>");
    data.setValue(1, 1, <?php print $data['inactive_cnt']; ?>);

    var activeInactiveChart = new google.visualization.PieChart(document.getElementById("active_inactive_chart"));
    activeInactiveChart.draw(data, {width: 450, height: 300, title: "<?php _e('Active vs. Inactive Post Types', CCTM_TXTDOMAIN); ?>"});
    
  }
</script>

<table>
	<tr>
		<td><div id="post_type_distribution_chart"></div></td>
		<td><div id="active_inactive_chart"></div></td>
	</tr>	
</table>

<?php
//------------------------------------------------------------------------------
// FORM HERE
//------------------------------------------------------------------------------
if ( !isset($data['cancel_target_url']) ) {
	$data['cancel_target_url'] = '?page=cctm_tools';
}

?>
<?php if (isset($data['style'])) { print $data['style']; } ?>
	

<form id="custom_post_type_manager_basic_form" method="post">

	<?php print $data['content']; ?>

	<?php wp_nonce_field($data['action_name'], $data['nonce_name']); ?>
<br/>
	<div class="custom_content_type_mgr_form_controls">
		<input type="submit" name="Submit" class="button-primary" value="<?php print $data['submit']; ?>" />
		<a class="button" href="<?php print $data['cancel_target_url']; ?>"><?php _e('Cancel'); ?></a> 
	</div>

</form>