<?php
////////////////////////////////////////////////////////////////////////////////////////////////////
//
//		File:
//			settings.php
//		Description:
//			This file compiles and processes the plugin's various settings pages.
//		Actions:
//			1) compile overall plugin settings form
//			2) process and save plugin settings
//		Date:
//			Added on September 15th 2011
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
//________________________________** INITIALIZE                **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
if(!isset($_GET['page']) or $_GET['page'] !== 'tern-wp-event-page-settings') {
	return;
}
//                                *******************************                                 //
//________________________________** ADD EVENTS                **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
add_action('init','WP_event_page_settings_actions');
add_action('init','WP_event_page_settings_styles');
add_action('init','WP_event_page_settings_scripts');
//                                *******************************                                 //
//________________________________** SCRIPTS                   **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
function WP_event_page_settings_styles() {
	
	
}
function WP_event_page_settings_scripts() {
	
	
}
//                                *******************************                                 //
//________________________________** ACTIONS                   **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
function WP_event_page_settings_actions() {
	global $getWP,$WP_event_page_defaults,$current_user;
	get_currentuserinfo();
	$o = $getWP->getOption('tern_wp_events',$WP_event_page_defaults);
	
	if($_REQUEST['action'] == 'update') {
		$o = $getWP->updateOption('tern_wp_events',$WP_event_page_defaults,'tern_wp_event_nonce');
	}
}
//                                *******************************                                 //
//________________________________** SETTINGS                  **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
function WP_event_page_options() {
	global $getWP,$ternSel,$tern_wp_msg,$WP_event_page_defaults;
	$o = $getWP->getOption('tern_wp_events',$WP_event_page_defaults);
?>
<div class="wrap">
	<div id="icon-options-general" class="icon32"><br /></div>
	<h2>Event Page Settings</h2>
	<?php
		if(!empty($tern_wp_msg)) {
			echo '<div id="message" class="updated fade"><p>'.$tern_wp_msg.'</p></div>';
		}
	?>
	<form method="post" action="">
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><label for="url">URL for event page</label></th>
				<td>
					<?php wp_dropdown_pages(array(
						'selected'	=>	$o['url'],
						'name'		=>	'url'
					)); ?>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="category">What category will your events be filed under?</label></th>
				<td>
					<?php wp_dropdown_categories('show_option_none=select&hide_empty=0&orderby=name&name=category&selected='.$o['category']); ?>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="limit">Number of viewable events at one time</label></th>
				<td>
					<?php
						$a = array('1','2','3','4','5','10','15','20','25','50','100','200');
						echo $ternSel->create(array(
							'type'		=>	'select',
							'data'		=>	$a,
							'name'		=>	'limit',
							'title'		=>	'Limit',
							'selected'	=>	array($o['limit'])
						));
						//echo $getOPTS->select($a,'limit','limit','','',false,array($o['limit']));
					?>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="order">Sort event list originally in this order</label></th>
				<td>
					<input type="radio" name="order" value="asc" <?php if($o['order'] == 'asc') { echo 'checked'; } ?> /> Ascending
					<input type="radio" name="order" value="desc" <?php if($o['order'] == 'desc') { echo 'checked'; } ?> /> Descending
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="show_time">Use search engine friendly pagination?</label></th>
				<td>
					<input type="radio" name="pages" value="1" <?php if($o['show_time']) { echo 'checked'; }?> /> yes 
					<input type="radio" name="pages" value="0" <?php if($o['show_time']) { echo 'checked'; }?> /> no<br />
					<span class="setting-description">If you select this option you will need to alter your .htaccess file.</span>
				</td>
			</tr>
		</table>
		<p class="submit"><input type="submit" name="submit" class="button-primary" value="Save Changes" /></p>
		<input type="hidden" name="action" value="update" />
		<input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo wp_create_nonce('tern_wp_event_nonce');?>" />
		<input type="hidden" name="_wp_http_referer" value="<?php wp_get_referer(); ?>" />
	</form>
</div>
<?php
}

/****************************************Terminate Script******************************************/
?>