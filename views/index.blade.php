<?php
if ( ! defined('WP_CONTENT_DIR')) exit('No direct script access allowed');
?>
<script type="text/javascript" >
    jQuery(document).ready(function($) {

        var data = {
            'action': 'cctm',
            'whatever': 1234
        };

        // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
        $.post(ajaxurl, data, function(response) {
            var obj = jQuery.parseJSON(response);
            alert('Got this from the server: ' + obj.hash);
        });
    });
</script>

<h2>{{ $foo }}</h2>