<?php
if ( ! defined('WP_CONTENT_DIR')) exit('No direct script access allowed');
// See http://weblogs.asp.net/dwahlin/dynamically-loading-controllers-and-views-with-angularjs-and-requirejs
?>
<script type="text/javascript" >
    jQuery(document).ready(function($) {

//        var data = {
//            'action': 'cctm',
//            'whatever': 1234,
//            '_resource':'fields'
//
//        };
        var data = {};
        //alert(data);
        // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
//        $.post(ajaxurl, data, function(response) {
//            //alert("response...");
//            console.log('This is the response', response);
////        $.post(ajaxurl+'?action=cctm', data, function(response) {
//            //var obj = jQuery.parseJSON(response);
//           //alert('Got this from the server: ' + obj.hash);
//        });

        $.ajax({
            type: "POST",
            url: ajaxurl+'?action=cctm&_resource=fields',
            data: data,
            success: function(msg){
                console.info(msg);
            },
            error: function(xhr, textStatus, errorThrown) {
                //alert(xhr.responseText);
                console.error(xhr.responseText);
            }
        });
    });
</script>
<h2>CCTM</h2>
<div ng-app="cctmApp">

    <a href="#/main" ng-class="{active: nav.isActive('/main')}">Post Types</a>
    <a href="#/settings" ng-class="{active: nav.isActive('/settings')}">Settings</a>


    <div ng-view></div>

</div>
