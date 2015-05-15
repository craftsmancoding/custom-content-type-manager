<?php
if ( ! defined('WP_CONTENT_DIR')) exit('No direct script access allowed');
// See http://weblogs.asp.net/dwahlin/dynamically-loading-controllers-and-views-with-angularjs-and-requirejs
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
           // alert('Got this from the server: ' + obj.hash);
        });
    });
</script>
<h2>CCTM</h2>
<div ng-app="cctmApp">

    <a href="#/main" ng-class="{active: nav.isActive('/main')}">Post Types</a>
    <a href="#/settings" ng-class="{active: nav.isActive('/settings')}">Settings</a>


    <div ng-view></div>

</div>
