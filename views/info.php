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

<div style="border: 1px dotted green; padding:10px;">
	<p>The Custom Content Type Manager (<a href="http://wpcctm.com/">wpCCTM.com</a>) was written by Everett Griffiths in part for the book <a href="http://www.packtpub.com/wordpress-3-plugin-development-essentials/book">WordPress 3 Plugin Development Essentials</a>, published by Packt. It is licensed under the GPL and its development has been fueled primarily by contracts that payed for feature development and by <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=FABHDKPU7P6LN">donations</a> from users.  Its architecture was influenced in part by <a href="http://modx.com/">MODx</a>. Many of the icons come from the Crystal set by <a href="http://www.everaldo.com/crystal/">Everaldo Coelho</a> and are licensed under the GPL.</p>
</div>

<p>
	<strong>Plugin Version:</strong> <?php print CCTM::version; print '-'; print CCTM::version_meta; ?><br />
	<strong>WordPress Version:</strong> <?php global $wp_version; print $wp_version; ?><br />
	<strong>PHP Version:</strong> <?php print phpversion(); ?><br />
	<strong>MySQL Version:</strong> <?php 
		global $wpdb;
		$result = $wpdb->get_results( 'SELECT VERSION() as ver' );
		print $result[0]->ver; ?><br />
	<strong>Server OS:</strong> <?php print PHP_OS; ?><br/>
</p>
<table>
	<tr>
		<td><div id="post_type_distribution_chart"></div></td>
		<td><div id="active_inactive_chart"></div></td>
	</tr>	
</table>

    