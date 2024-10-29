<?php

/*
Plugin Name: Antispam Extra
Plugin URI: http://www.budhiman.com/wordpress-plugin/antispam-extra.html
Description: Provides options to disable the commenter's website and deactivate links in comments. Extra protection from spammers, best used with Akismet. 
Version: 0.2
License: GPL
Author: Budhiman
Author URI: http://www.budhiman.com/


Copyright 2011 Budhiman (email: contact@budhiman.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

//error_reporting(E_ALL);
define('ANTISPAMEXTRA_PLUGIN_NAME', 'antispam-extra');
define('ANTISPAMEXTRA_PLUGIN_DIR', WP_PLUGIN_DIR . '/'.ANTISPAMEXTRA_PLUGIN_NAME.'/');
define('ANTISPAMEXTRA_PLUGIN_FILE', WP_PLUGIN_DIR . '/'.ANTISPAMEXTRA_PLUGIN_NAME.'/'.ANTISPAMEXTRA_PLUGIN_NAME.'.php');
define('ANTISPAMEXTRA_PLUGIN_BASENAME', plugin_basename(ANTISPAMEXTRA_PLUGIN_FILE));

if (isset($_POST['antispam-extra-action'])) {
	
	if (isset($_POST['antispamextra_hide_website_input'])) $antispamextra_hide_website_input = 1;
	else $antispamextra_hide_website_input = 0;
	if (isset($_POST['antispamextra_disable_website'])) $antispamextra_disable_website = 1;
	else $antispamextra_disable_website = 0;
	if (isset($_POST['antispamextra_deactivate_links'])) $antispamextra_deactivate_links = 1;
	else $antispamextra_deactivate_links = 0;
	if (isset($_POST['antispamextra_disallow_nonreferers'])) $antispamextra_disallow_nonreferers = 1;
	else $antispamextra_disallow_nonreferers = 0;
	if (isset($_POST['antispamextra_spam_response_mode'])) $antispamextra_spam_response_mode = 1;
	else $antispamextra_spam_response_mode = 0;
	if (isset($_POST['antispamextra_message'])) update_option('antispamextra_message', $_POST['antispamextra_message']);	
	
	update_option('antispamextra_hide_website_input', $antispamextra_hide_website_input);
	update_option('antispamextra_disable_website', $antispamextra_disable_website);
	update_option('antispamextra_deactivate_links', $antispamextra_deactivate_links);
	update_option('antispamextra_disallow_nonreferers', $antispamextra_disallow_nonreferers);
	update_option('antispamextra_spam_response_mode', $antispamextra_spam_response_mode);	
	
	// hook the admin notices action
	add_action( 'admin_notices', array('AntispamExtra', 'notice_update'), 9 );	
}

class AntispamExtra {

	// Add stylesheet
	function add_stylesheet() {
	
		if (get_option('antispamextra_hide_website_input')) {
		
			$style_url = WP_PLUGIN_URL . '/antispam-extra/style.plugin.css';
			$style_file = WP_PLUGIN_DIR . '/antispam-extra/style.plugin.css';
			if ( file_exists($style_file) ) {
				wp_register_style('antispam-extra.style.plugin', $style_url);
				wp_enqueue_style( 'antispam-extra.style.plugin');
			}
		}
	}

	// No hand crafted comments posts with URLS
	function no_comment_website() {
		
		if (get_option('antispamextra_hide_website_input')) {
			// if (isset($_POST['url']) && strlen($_POST['url']) > 0) {
			if (!empty($_POST['url'])) {

				$antispamextra_spamcount = get_option('antispamextra_spamcount');
				$antispamextra_spamcount++;
				update_option('antispamextra_spamcount', $antispamextra_spamcount);
				
				if (get_option('antispamextra_spam_response_mode')) {				
					header('HTTP/1.1 403 Forbidden');			
					die(get_option('antispamextra_message'));
				}
				else die();
			}
		}
	}

	// No comments without proper HTTP referer
	function check_referrer() {

		if (get_option('antispamextra_disallow_nonreferers')) {
			if (!isset($_SERVER['HTTP_REFERER']) || $_SERVER['HTTP_REFERER'] == '' || strpos($_SERVER['HTTP_REFERER'], get_option('siteurl')) < 0) {
			
				$antispamextra_spamcount = get_option('antispamextra_spamcount');
				$antispamextra_spamcount++;
				update_option('antispamextra_spamcount', $antispamextra_spamcount);
				
				if (get_option('antispamextra_spam_response_mode')) {
					header('HTTP/1.1 403 Forbidden');
					die(get_option('antispamextra_message'));				
				}
				else die();
			}
		}
	}
	
	// Disable links in comments
	function deactivate_comment_urls($comment) {	
		
		//make_clickable is renamed to followmylinks_make_clickable if you install Follow My Links
		
		if (get_option('antispamextra_deactivate_links')) {
			remove_filter('comment_text', 'make_clickable', 9);
			$comment = preg_replace('/<a href=\"(.*?)\">(.*?)<\/a>/', "\\2", $comment);
		}
		return $comment;	
	}
	
	// Set commenter website to null even if supplied
	function no_comment_author_url($comment) {
		
		if (get_option('antispamextra_hide_website_input')) {
			$comment[comment_author_url] = null;
		}
		return $comment;
	}
	
	// Disable existing author website URLs
	function no_comment_url($comment_url) {
		
		if (get_option('antispamextra_disable_website')) {
			$comment_url = null;
		}
		return $comment_url;
	}
	

	function init() {		
		
		if (!is_admin() && get_option('antispamextra_hide_website_input')) {			
			wp_register_script(ANTISPAMEXTRA_PLUGIN_NAME, WP_PLUGIN_URL . '/antispam-extra/script.js', array('jquery'));
			wp_enqueue_script(ANTISPAMEXTRA_PLUGIN_NAME);
		}
	}	
	
	/* Plugin options page */

	function admin_init() {
       /* Register our stylesheet. */
       wp_register_style( 'antispam-extra.style.admin', WP_PLUGIN_URL . '/antispam-extra/style.admin.css' );
   }
   
   function admin_menu() { 
   
		$page = add_options_page('Antispam Extra', 'Antispam Extra', 'administrator', ANTISPAMEXTRA_PLUGIN_NAME, array('AntispamExtra', 'options_menu'));
	    /* Using registered $page handle to hook stylesheet loading */	   
		add_action( 'admin_print_styles-' . $page, array('AntispamExtra', 'admin_styles' ));	
   }
   
   function admin_styles() {
       wp_enqueue_style( 'antispam-extra.style.admin' );
   }   

   function options_menu() {
		require_once(ANTISPAMEXTRA_PLUGIN_DIR . 'inc/admin.php');
   }

	function notice_update() {
		echo "<div class = 'updated'><p>" . __("Antispam options updated.", ANTISPAMEXTRA_PLUGIN_NAME) ."</p></div>";
	} 	

	function spamcount() {
		$spamcount = get_option('antispamextra_spamcount');
		if ($spamcount) echo '<p><a href="http://www.budhiman.com/wordpress-plugin/antispam-extra.html"><b>Antispam Extra</b></a> prevented '. $spamcount .' automated spam attempts.</p>';
	}
	
	function settings_link($links, $file) {
		
		if ($file == ANTISPAMEXTRA_PLUGIN_BASENAME) {
			
			$settings_link = '<a href="options-general.php?page='.ANTISPAMEXTRA_PLUGIN_NAME.'">'.__('Settings').'</a>';
			array_unshift( $links, $settings_link ); // before other links			
		}
		
		return $links;	
	}
	
	// On plugin activation
	function on_activation() {

		add_option('antispamextra_hide_website_input', 1, '', 'yes');
		add_option('antispamextra_disable_website', 1, '', 'yes');
		add_option('antispamextra_deactivate_links', 1, '', 'yes');		
		add_option('antispamextra_disallow_nonreferers', 1, '', 'yes');	
		add_option('antispamextra_spam_response_mode', 0, '', 'yes');
		add_option('antispamextra_spamcount', 0, '', 'yes');	
		add_option('antispamextra_message', "Don't spam me, bro!", '', 'yes');
	}
	
	// On plugin deactivation
	function on_deactivation() {

		delete_option('antispamextra_hide_website_input');
		delete_option('antispamextra_disable_website');
		delete_option('antispamextra_deactivate_links');		
		delete_option('antispamextra_disallow_nonreferers');	
		delete_option('antispamextra_spam_response_mode');
		delete_option('antispamextra_spamcount');	
		delete_option('antispamextra_message');
	}
	
}


// Actions
	// plugin functionality
add_action('wp_print_styles', array('AntispamExtra', 'add_stylesheet'));
add_action('check_comment_flood',  array('AntispamExtra', 'no_comment_website'));
add_action('check_comment_flood', array('AntispamExtra', 'check_referrer'));
add_action('preprocess_comment', array('AntispamExtra', 'no_comment_author_url'));
add_action('init', array('AntispamExtra', 'init'));
add_action('activity_box_end', array('AntispamExtra', 'spamcount'));

	// plugin options
add_action('admin_init', array('AntispamExtra', 'admin_init'));
add_action('admin_menu', array('AntispamExtra', 'admin_menu'));
add_filter('plugin_action_links', array('AntispamExtra', 'settings_link'), 10, 2);

// Filters
add_filter('comment_text', array('AntispamExtra', 'deactivate_comment_urls'), 8);
add_filter('get_comment_author_url', array('AntispamExtra', 'no_comment_url'), 1);


// Activation / deactivation
register_activation_hook(ANTISPAMEXTRA_PLUGIN_FILE, array('AntispamExtra', 'on_activation'));
register_deactivation_hook(ANTISPAMEXTRA_PLUGIN_FILE, array('AntispamExtra', 'on_deactivation'));
