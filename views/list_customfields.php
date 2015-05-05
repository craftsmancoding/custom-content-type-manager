<?php 
if (!defined('CCTM_PATH')) die('No direct script access allowed');
// Define some data for the header
$data->pagetitle = __('Manage Custom Fields', CCTM_TXTDOMAIN);
$data->help = 'https://code.google.com/p/wordpress-custom-content-type-manager/wiki/SupportedCustomFields';
$data->tab_customfields = ' cctm_active';
include dirname(__FILE__).'/chunks/header.php'; 
?>

<?php if ($data->fields): ?>

    <table class="wp-list-table widefat plugins" cellspacing="0">
    <thead>
    	<tr>
    		<th scope="col" id="icon" class=""  style="width: 20px;"><?php _e('Type', CCTM_TXTDOMAIN); ?></th>
    		<th scope="col" id="name" class=""  style="width: 200px;"><?php _e('Field', CCTM_TXTDOMAIN); ?></th>
    		<th scope="col" id="name" class=""  style="width: 200px;"><?php _e('Post Types', CCTM_TXTDOMAIN); ?></th>
    		<th scope="col" id="options" class=""  style="width: 200px;"><?php _e('Options', CCTM_TXTDOMAIN); ?></th>
    		<th scope="col" id="description" class="manage-column column-description"  style=""><?php _e('Description', CCTM_TXTDOMAIN); ?></th>	
    	</tr>
    </thead>
    
    <tfoot>
    	<tr>
    		<th scope="col" id="icon" class=""  style="width: 20px;"><?php _e('Type', CCTM_TXTDOMAIN); ?></th>
    		<th scope="col" id="name" class=""  style="width: 200px;"><?php _e('Field', CCTM_TXTDOMAIN); ?></th>
    		<th scope="col" id="name" class=""  style="width: 200px;"><?php _e('Post Types', CCTM_TXTDOMAIN); ?></th>
    		<th scope="col" id="options" class=""  style="width: 200px;"><?php _e('Options', CCTM_TXTDOMAIN); ?></th>
    		<th scope="col" id="description" class="manage-column column-description"  style=""><?php _e('Description', CCTM_TXTDOMAIN); ?></th>	
    	</tr>
    </tfoot>
    
    <tbody id="custom-field-list">
    
    	<?php foreach ($data->fields as $f): ?>
    	
    	
    	<?php endforeach; ?>
    </tbody>
    </table>

<?php else: ?>

    <p>You have not defined any custom fields yet. <a href="<?php print \CCTM\Route::url('customfields/types'); ?>">Create a Custom Field</a></p>

<?php endif; ?>

<?php include dirname(__FILE__).'/chunks/footer.php'; ?>