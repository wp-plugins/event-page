<?php
////////////////////////////////////////////////////////////////////////////////////////////////////
//
//		File:
//			date-settings.php
//		Description:
//			This file compiles and processes the plugin's various settings pages.
//		Actions:
//			1) compile overall plugin settings form
//			2) process and save plugin settings
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
//________________________________** INITIALIZE                **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
if(!isset($_GET['page']) or $_GET['page'] !== 'tern-wp-event-page-date-time') {
	return;
}
//                                *******************************                                 //
//________________________________** ADD EVENTS                **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
add_action('init','WP_event_page_date_settings_actions');
add_action('init','WP_event_page_date_settings_styles');
add_action('init','WP_event_page_date_settings_scripts');
//                                *******************************                                 //
//________________________________** SCRIPTS                   **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
function WP_event_page_date_settings_styles() {
	
	
}
function WP_event_page_date_settings_scripts() {
	
	
}
//                                *******************************                                 //
//________________________________** ACTIONS                   **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
function WP_event_page_date_settings_actions() {
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
function WP_event_page_date_options() {
	global $getWP,$tern_wp_msg,$WP_event_page_defaults;
	$o = $getWP->getOption('tern_wp_events',$WP_event_page_defaults);
?>
<div class="wrap">
	<div id="icon-options-general" class="icon32"><br /></div>
	<h2>Date &amp; Time Settings</h2>
	<?php
		if(!empty($tern_wp_msg)) {
			echo '<div id="message" class="updated fade"><p>'.$tern_wp_msg.'</p></div>';
		}
	?>
	<form method="post" action="">
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><label for="show_time">Show time when viewing event list</label></th>
				<td>
					<input type="radio" name="show_time" value="1" <?php if($o['show_time']) { echo 'checked'; }?> /> yes 
					<input type="radio" name="show_time" value="0" <?php if(!$o['show_time']) { echo 'checked'; }?> /> no<br /> 
					<span class="setting-description">Would you like the time of each event displayed when displaying the events list?</span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="end_time">Show end date and/or time when viewing event list</label></th>
				<td>
					<input type="radio" name="end_time" value="1" <?php if($o['end_time']) { echo 'checked'; }?> /> yes 
					<input type="radio" name="end_time" value="0" <?php if(!$o['end_time']) { echo 'checked'; }?> /> no<br />
					<span class="setting-description">Would you like the end date and/or time of each event displayed when displaying the events list?</span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="d_2_t_sep">Date from time separating character</label></th>
				<td>
					<input type="text" name="d_2_t_sep" class="regular-text" value="<?php echo $o['d_2_t_sep'];?>" />
					<span class="setting-description">This string separates the date from the time and defaults to a single space.</span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="time_sep">Time Separating character</label></th>
				<td>
					<input type="text" name="time_sep" class="regular-text" value="<?php echo $o['time_sep'];?>" />
					<span class="setting-description">This character separates the start and end times if the event starts and finishes on the same day and defaults to a single dash with a space on both sides.</span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="date_sep">Date Separating character</label></th>
				<td>
					<input type="text" name="date_sep" class="regular-text" value="<?php echo $o['date_sep'];?>" />
					<span class="setting-description">This character separates the start and end dates if the event starts and finishes on different days and defaults to a double dash with a space on both sides.</span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="format">Date format:</label></th>
				<td>
					<input type="text" name="format" class="regular-text" value="<?php echo $o['format'];?>" />
					<span class="setting-description">This should be a string represetation of the date according to PHP/Wordpress' date formatting parameters. i.e. "l F j, Y". See <a href="http://us.php.net/manual/en/function.date.php">here</a>.</span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="time">Time format:</label></th>
				<td>
					<input type="text" name="time" class="regular-text" value="<?php echo $o['time'];?>" />
					<span class="setting-description">This should be a string represetation of the time according to PHP/Wordpress' date formatting parameters. i.e. "g:ia". See <a href="http://us.php.net/manual/en/function.date.php">here</a>.</span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="date_markup">Date mark-up:</label></th>
				<td>
					<textarea name="date_markup" style="width:100%;"><?php echo $o['date_markup'];?></textarea>
					<span class="setting-description">Use this in place of the date format fields. Use any mark-up you like and employ the '%' character around the PHP date formatting characters. e.g. &lt;span&gt;%l% %F% %j%, %Y%&lt;/span&gt;</span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="time_markup">Time mark-up:</label></th>
				<td>
					<textarea name="time_markup" style="width:100%;"><?php echo $o['time_markup'];?></textarea>
					<span class="setting-description">Use this in place of the time format fields. Use any mark-up you like and employ the '%' character around the PHP date formatting characters. e.g. &lt;span&gt;%g%:%i%%a%&lt;/span&gt;</span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="timezone">Timezone String to be displayed after dates:</label></th>
				<td>
					<input type="text" name="timezone" class="regular-text" value="<?php echo $o['timezone'];?>" />
					<span class="setting-description">e.g. EST or EDT</span>
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