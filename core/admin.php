<?php
////////////////////////////////////////////////////////////////////////////////////////////////////
//
//		File:
//			admin.php
//		Description:
//			This file runs the plugin's administrative tasks.
//		Actions:
//			1) enqueue syles and scripts
//			2) compile administrative menus
//			3) compile and render errors
//		Date:
//			Added on February 14th 2011
//		Copyright:
//			Copyright (c) 2011 Matthew Praetzel.
//		License:
//			This software is licensed under the terms of the GNU Lesser General Public License v3
//			as published by the Free Software Foundation. You should have received a copy of of
//			the GNU Lesser General Public License along with this software. In the event that you
//			have not, please visit: http://www.gnu.org/licenses/gpl-3.0.txt
//
////////////////////////////////////////////////////////////////////////////////////////////////////

/****************************************Commence Script*******************************************/

//                                *******************************                                 //
//________________________________** ADD EVENTS                **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
add_action('admin_menu','WP_event_page_menu');
add_action('admin_enqueue_scripts','WP_event_page_scripts');
add_action('wp_print_scripts','WP_event_page_js');
add_action('admin_enqueue_scripts','WP_event_page_styles');
add_action('wp_enqueue_scripts','WP_event_page_styles');
add_action('admin_head','WP_event_page_errors');
//                                *******************************                                 //
//________________________________** SCRIPTS                   **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
function WP_event_page_styles() {
	if(is_admin()) {
		wp_enqueue_style('event-page-admin',get_bloginfo('wpurl').'/wp-content/plugins/event-page/css/admin.css');
	}
	else {
		wp_enqueue_style('event-page-style',get_bloginfo('wpurl').'/wp-content/plugins/event-page/css/style.css');
	}
}
function WP_event_page_scripts() {
	if(is_admin()) {
		
	}
}
function WP_event_page_js() {
	echo '<script type="text/javascript">var tern_wp_root = "'.get_bloginfo('home').'";</script>'."\n";
}
//                                *******************************                                 //
//________________________________** MENUS                     **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
function WP_event_page_menu() {
	if(function_exists('add_menu_page')) {
		add_menu_page('Event Page','Event Page','manage_options','tern-wp-event-page-settings','WP_event_page_options');
		add_submenu_page('tern-wp-event-page-settings','Event Page','Settings','manage_options','tern-wp-event-page-settings','WP_event_page_options');
		add_submenu_page('tern-wp-event-page-settings','Date Time Setings','Date Time Setings','manage_options','tern-wp-event-page-date-time','WP_event_page_date_options');
		add_submenu_page('tern-wp-event-page-settings','Configure Event Mark-Up','Configure Event Mark-Up','manage_options','tern-wp-event-page-mark-up','WP_event_page_markup_options');
	}
}
//                                *******************************                                 //
//________________________________** ERRORS                    **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
function WP_event_page_errors() {
	global $getWP;
	$getWP->renderErrors();
}

/****************************************Terminate Script******************************************/
?>