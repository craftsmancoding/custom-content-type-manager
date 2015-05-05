<?php
if ( ! defined('CCTM_PATH')) exit('No direct script access allowed');
/*------------------------------------------------------------------------------
Help page
------------------------------------------------------------------------------*/
$data 				= array();
$data['page_title']	= __('Help', CCTM_TXTDOMAIN);
$data['menu'] 		= sprintf('<a href="http://code.google.com/p/wordpress-custom-content-type-manager/" class="button" target="_blank">%s</a>'
	, __('Documentation', CCTM_TXTDOMAIN) );
$data['msg']		= '';
$data['content'] = '<p>'.__('You can click the information icon at the top right corner of any page to pull up a relevant help page from the wiki:', CCTM_TXTDOMAIN).' <img src="'.CCTM_URL.'/images/question-mark.gif" width="16" height="16" /></p>


<div style="border: 2px dotted green; width:70%; padding: 10px;">
	<img src="'.CCTM_URL.'/images/help-large.png" width="48" height="48" style="float:left; padding:10px;"/>
	<p>'.__('Support for this plugin is provided through the forum and bug-tracker.',CCTM_TXTDOMAIN).'<br/><br/></p>
	</div>';
$data['content'] .=  '<p>';
$data['content'] .= __('This plugin is open source software freely available to WordPress users.', CCTM_TXTDOMAIN) . ' ';
$data['content'] .= __('Use the following links to get more information about how to use this plugin.', CCTM_TXTDOMAIN);
$data['content'] .=  '<ul>';
$data['content'] .= '<li>'.__('<a href="http://code.google.com/p/wordpress-custom-content-type-manager/">Official Documentation</a> -- available on the project\'s Wiki page.', CCTM_TXTDOMAIN) . '</li>';
$data['content'] .= '<li>'.__('<a href="http://wordpress.org/tags/custom-content-type-manager?forum_id=10">Plugin Forum</a> -- General questions can be asked in the forum.', CCTM_TXTDOMAIN) . '</li>';
$data['content'] .= '<li>'.__('<a href="http://wpcctm.com/">Official Site</a>.', CCTM_TXTDOMAIN) . '</li>';
$data['content'] .= '</ul></p>';




print CCTM::load_view('templates/default.php', $data);
/*EOF*/