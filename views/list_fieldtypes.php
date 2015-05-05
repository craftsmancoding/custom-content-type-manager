<?php 
if (!defined('CCTM_PATH')) die('No direct script access allowed');
// Define some data for the header
$data->pagetitle = __('Choose Type of Custom Field', CCTM_TXTDOMAIN);
$data->help = 'https://code.google.com/p/wordpress-custom-content-type-manager/wiki/SupportedCustomFields';
$data->tab_customfields = ' cctm_active';
include dirname(__FILE__).'/chunks/header.php'; 
?>

<p><?php _e('Choose the type of field you want to create.', CCTM_TXTDOMAIN); ?></p>
<table class="wp-list-table widefat plugins" cellspacing="0">
<thead>
	<tr>
		<th scope="col" id="name" class=""  style="width: 20px;">&nbsp;</th>
		<th scope="col" id="description" class="manage-column column-description"  style=""><?php _e('Field Type Description', CCTM_TXTDOMAIN); ?></th>	
	</tr>
</thead>

<tfoot>
	<tr>
		<th scope="col" id="name" class=""  style="width: 20px;">&nbsp;</th>
		<th scope="col" id="description" class="manage-column column-description"  style=""><?php _e('Field Type Description', CCTM_TXTDOMAIN); ?></th>	
	</tr>
</tfoot>

<tbody id="custom-field-list">

	<?php foreach ($data->types as $t): ?>
	
	<?php endforeach; ?>
</tbody>
</table>

<?php include dirname(__FILE__).'/chunks/footer.php'; ?>