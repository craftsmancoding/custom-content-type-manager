<p><?php _e("The CCTM gives precedence to user-created files inside of uploads/cctm/ over built-in files inside the plugin's directory. This page highlights which files have been overridden by local customizations.", CCTM_TXTDOMAIN); ?></p>

<p>IN PROGRESS...</p>

<h2>tpls</h2>

<ul>
<?php print $data['tpls']; ?>
</ul>

<h2>Config Files</h2>
<ul>
<?php print $data['configs']; ?>
</ul>


<h2>Fields</h2>
<ul>
<?php print $data['fields']; ?>
</ul>


<h2>Filters</h2>
<ul>
<?php print $data['filters']; ?>
</ul>
