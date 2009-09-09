<?php
/*
Plugin Name: Event Page
Plugin URI: http://www.ternstyle.us/products/plugins/wordpress/wordpress-event-page-plugin
Description: The Event Page Plugin allows you to create a page, category page or post on your wordpress blog that lists all your events.
Author: Matthew Praetzel
Version: 2.0.3
Author URI: http://www.ternstyle.us/
Licensing : http://www.ternstyle.us/license.html
*/

////////////////////////////////////////////////////////////////////////////////////////////////////
////	File:
////		tern_wp_events.php
////	Actions:
////		1) compile event list
////	Account:
////		Added on September 2nd 2008
////	Version:
////		2.0.3
////
////	Written by Matthew Praetzel. Copyright (c) 2008 Matthew Praetzel.
////////////////////////////////////////////////////////////////////////////////////////////////////

/****************************************Commence Script*******************************************/

//                                *******************************                                 //
//________________________________** INITIALIZE VARIABLES      **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
$tern_wp_event_defaults = array(
	'show_time'		=>	1,
	'end_time'		=>	0,
	'format'		=>	'l F t, Y',
	'time'			=>	'g:ia',
	'date_markup'	=>	'',
	'time_markup'	=>	'',
	
	'd_2_t_sep'		=>	' ',
	'time_sep'		=>	' - ',
	'date_sep'		=>	' -- ',
	
	'url'			=>	get_bloginfo('home').'/events',
	'category'		=>	1,
	'limit'			=>	5,
	'order'			=> 'desc',
	'pages'			=>	0,
	'fields'		=>	array(
		'Post Title'	=>	array(
			'name'		=>	'post_title',
			'markup'	=>	'<div class="tern_wp_event_post_title"><h3><a href="%post_url%">%value%</a></h3></div>'
		),
		'Event Date'	=>	array(
			'name'		=>	'tern_wp_event_date',
			'markup'	=>	'<div class="tern_wp_event_date">%value%</div>'
		),
		'Post Content'	=>	array(
			'name'		=>	'post_content',
			'markup'	=>	'<div class="tern_wp_event_post_content">%value%</div>'
		)
	)
);
$tern_wp_event_fields = array(
	'Start Date'	=>	array(
		'name'	=>	'_tern_wp_event_start_date',
		'meta'	=>	false
	),
	'End Date'		=>	array(
		'name'	=>	'_tern_wp_event_end_date',
		'meta'	=>	false
	),
	'Event type'	=>	array(
		'name'	=>	'_tern_wp_event_type',
		'meta'	=>	true
	),
	'Hosted By'		=>	array(
		'name'	=>	'_tern_wp_event_host',
		'meta'	=>	true
	),
	'Location'		=>	array(
		'name'	=>	'_tern_wp_event_location',
		'meta'	=>	true
	)
	,
	'Event URL'		=>	array(
		'name'	=>	'_tern_wp_event_url',
		'meta'	=>	true
	)
);
$tern_wp_event_markup_fields = array(
	'Post Title'	=>	array(
		'field'	=>	'post_title',
		'func'	=>	'get_the_title',
		'args'	=>	'id'
	),
	'Event Date'	=>	array(
		'field'	=>	'tern_wp_event_date',
		'func'	=>	'tern_wp_event_date',
		'args'	=>	'id'
	),
	'Post Content'	=>	array(
		'field'	=>	'post_content',
		'func'	=>	'get_the_content',
		'args'	=>	'"read more..."'
	),
	'Post Excerpt'	=>	array(
		'field'	=>	'post_excerpt',
		'func'	=>	'get_the_excerpt',
		'args'	=>	false
	),
	'Post Author'	=>	array(
		'field'	=>	'post_author',
		'func'	=>	'get_the_author',
		'args'	=>	false
	),
	'Post Status'	=>	array(
		'field'	=>	'post_status',
		'func'	=>	false
	),
	'Comment Count'	=>	array(
		'field'	=>	'comment_count',
		'func'	=>	'get_comments_number',
		'args'	=>	false
	)
);

$tern_wp_date_formats = array(
	'Thu January 31st, 1970 12:00pm - Thu January 31st, 1970 4:00pm'	=>	array(
		'format'	=>	'D F jS, Y gi:a',
		'sep'		=>	' - '
	),
	'January 31st, 1970 12:00pm - January 31st, 1970 4:00pm'	=>	array(
		'format'	=>	'F jS, Y gi:a',
		'sep'		=>	' - '
	),
	'Thu Jan 31st, 1970 12:00pm - Thu January 31st, 1970 4:00pm'	=>	array(
		'format'	=>	'M j, Y ga',
		'sep'		=>	' - '
	),
	'Jan 31, 1970 12pm - Jan 31, 1970 4pm'	=>	array(
		'format'	=>	'M j, Y ga',
		'sep'		=>	' - '
	)
	
);
$tern_wp_event_is_list = false;
$tern_wp_event_post = NULL;
//                                *******************************                                 //
//________________________________** INCLUDES                  **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
require_once(ABSPATH.'wp-content/plugins/event-page/ternstyle/class/wordpress.php');
require_once(ABSPATH.'wp-content/plugins/event-page/ternstyle/class/pagination.php');
require_once(ABSPATH.'wp-content/plugins/event-page/ternstyle/class/select.php');
require_once(ABSPATH.'wp-content/plugins/event-page/ternstyle/class/time.php');
//                                *******************************                                 //
//________________________________** ADD EVENTS                **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
add_action('init','tern_wp_event_actions');
add_action('init','tern_wp_event_js');
add_action('wp_print_scripts','tern_wp_events_scripts');
add_action('admin_menu','tern_wp_events_box');
add_action('admin_menu','tern_wp_event_menu');
add_action('save_post','tern_wp_events_save');
//                                *******************************                                 //
//________________________________** SCRIPTS                   **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
function tern_wp_events_scripts() {
	echo '<link rel="stylesheet" href="/wp-content/plugins/event-page/tern_wp_events.css" type="text/css" media="all" />' . "\n";
	echo '<script type="text/javascript">var tern_wp_root = "'.get_bloginfo('home').'";</script>';
}
function tern_wp_event_js() {
	if($_REQUEST['page'] == 'Configure Mark-Up') {
		wp_enqueue_script('TableDnD',get_bloginfo('home').'/wp-content/plugins/event-page/js/jquery.tablednd_0_5.js.php',array('jquery'),'0.5');
		wp_enqueue_script('members-list',get_bloginfo('home').'/wp-content/plugins/event-page/js/event-page.js');
	}
}
//                                *******************************                                 //
//________________________________** ACTIONS                   **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
function tern_wp_events_save($i) {
	global $tern_wp_event_fields;
	$i = wp_is_post_revision($i);
	if(!wp_verify_nonce($_POST['tern_wp_event_nonce'],plugin_basename(__FILE__)) or !$i) {
		return;
	}
	if(!current_user_can('edit_post',$i)) {
		return;
	}
	foreach($tern_wp_event_fields as $v) {
		$n = $v['name'];
		if($n == '_tern_wp_event_start_date') {
			$h = $_POST['tern_wp_event_start_hour'];
			if($_POST['tern_wp_event_start_meridiem'] == 'pm' and intval($_POST['tern_wp_event_start_hour']) != 12) {
				$h = intval($_POST['tern_wp_event_start_hour'])+12;
			}
			$start = gmmktime($h,$_POST['tern_wp_event_start_minute'],0,$_POST['tern_wp_event_start_month'],$_POST['tern_wp_event_start_day'],$_POST['tern_wp_event_start_year']);
			update_post_meta($i,$n,$start);
		}
		elseif($n == '_tern_wp_event_end_date') {
			$h = $_POST['tern_wp_event_end_hour'];
			if($_POST['tern_wp_event_end_meridiem'] == 'pm' and intval($_POST['tern_wp_event_end_hour']) != 12) {
				$h = intval($_POST['tern_wp_event_end_hour'])+12;
			}
			$end = gmmktime($h,$_POST['tern_wp_event_end_minute'],0,$_POST['tern_wp_event_end_month'],$_POST['tern_wp_event_end_day'],$_POST['tern_wp_event_end_year']);
			$end = $start > $end ? $start : $end;
			update_post_meta($i,$n,$end);
		}
		else {
			update_post_meta($i,$n,$_POST[$n]);
		}
	}
}
function tern_wp_event_actions() {
	global $getWP,$tern_wp_event_defaults,$current_user;
	get_currentuserinfo();
	$o = $getWP->getOption('tern_wp_events',$tern_wp_event_defaults);
	//Configure Mark-Up Page Actions
	if($_REQUEST['page'] == 'Configure Mark-Up') {
		if(wp_verify_nonce($_REQUEST['_wpnonce'],'tern_wp_event_nonce')) {
			switch($_REQUEST['action']) {
				//update all fields
				case 'update' :
					$o['fields'] = array();
					foreach($_REQUEST['field_titles'] as $k => $v) {
						$v = stripslashes($v);
						$o['fields'][$v] = array(
							'name'		=>	$_REQUEST['field_names'][$k],
							'markup'	=>	stripslashes($_REQUEST['field_markups'][$k])
						);
					}
					$o = $getWP->getOption('tern_wp_events',$o,true);
					echo '<div id="message" class="updated fade"><p>Your order has been successfully saved.</p></div>';
					die();
				//add a field
				case 'add' :
					$f = $_REQUEST['new_field'];
					$o['fields'][$f] = array(
						'name'		=>	$f,
						'markup'	=>	'<div class="tern_wp_event_'.$f.'">%value%</div>'
					);
					$o = $getWP->getOption('tern_wp_events',$o,true);
				//delete a field
				case 'remove' :
					$a = array();
					foreach($o['fields'] as $k => $v) {
						if($v['name'] != $_REQUEST['fields'][0]) {
							$a[$k] = $v;
						}
					}
					$o['fields'] = $a;
					$o = $getWP->getOption('tern_wp_events',$o,true);
			}
		}
		//attempted to update all fields without nonce
		elseif($_REQUEST['action'] == 'update' or $_REQUEST['action'] == 'add' or $_REQUEST['action'] == 'delete') {
			echo '<div id="message" class="updated fade"><p>There was an error whil processing your request. Please try again.</p></div>';
			die();
		}
		//get sample mark-up
		if($_REQUEST['action'] == 'getmarkup') {
			$p = get_posts('numberposts=1&category='.$o['category']);
			echo htmlentities(tern_wp_event_markup($p[0]->ID));
			die();
		}
	}
}
//                                *******************************                                 //
//________________________________** MENUS                     **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
function tern_wp_event_menu() {
	if(function_exists('add_menu_page')) {
		add_menu_page('Event Page','Event Page',10,__FILE__,'tern_wp_event_options');
		add_submenu_page(__FILE__,'Event Page','Settings',10,__FILE__,'tern_wp_event_options');
		add_submenu_page(__FILE__,'Date Time Setings','Date Time Setings',10,'Date Time Setings','tern_wp_event_date_options');
		add_submenu_page(__FILE__,'Configure Mark-Up','Configure Mark-Up',10,'Configure Mark-Up','tern_wp_event_markup_options');
	}
}
//                                *******************************                                 //
//________________________________** SETTINGS                  **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
function tern_wp_event_options() {
	global $getWP,$getOPTS,$tern_wp_msg,$tern_wp_event_defaults;
	$o = $getWP->updateOption('tern_wp_events',$tern_wp_event_defaults,'tern_wp_event_nonce');
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
					<input type="text" name="url" class="regular-text" value="<?=$o['url'];?>" />
					<span class="setting-description">http://blog.ternstyle.us/events</span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="category">What is the term id of the category your events will be filed under?</label></th>
				<td>
					<input type="text" name="category" class="regular-text" value="<?=$o['category'];?>" />
					<span class="setting-description">This should be a number. i.e. 1 or 46.</span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="limit">Number of viewable events at one time</label></th>
				<td>
					<?php
						$a = array(1,2,3,4,5,10,15,20,25,50,100,200);
						echo $getOPTS->select($a,'limit','limit','','',false,array($o['limit']));
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
		<input type="hidden" id="_wpnonce" name="_wpnonce" value="<?=wp_create_nonce('tern_wp_event_nonce');?>" />
		<input type="hidden" name="_wp_http_referer" value="<?php wp_get_referer(); ?>" />
	</form>
</div>
<?php
}
function tern_wp_event_date_options() {
	global $getWP,$getOPTS,$tern_wp_msg,$tern_wp_event_defaults;
	$o = $getWP->updateOption('tern_wp_events',$tern_wp_event_defaults,'tern_wp_event_nonce');
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
					<input type="text" name="d_2_t_sep" class="regular-text" value="<?=$o['d_2_t_sep'];?>" />
					<span class="setting-description">This string separates the date from the time and defaults to a single space.</span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="time_sep">Time Separating character</label></th>
				<td>
					<input type="text" name="time_sep" class="regular-text" value="<?=$o['time_sep'];?>" />
					<span class="setting-description">This character separates the start and end times if the event starts and finishes on the same day and defaults to a single dash with a space on both sides.</span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="date_sep">Date Separating character</label></th>
				<td>
					<input type="text" name="date_sep" class="regular-text" value="<?=$o['date_sep'];?>" />
					<span class="setting-description">This character separates the start and end dates if the event starts and finishes on different days and defaults to a double dash with a space on both sides.</span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="format">Date format:</label></th>
				<td>
					<input type="text" name="format" class="regular-text" value="<?=$o['format'];?>" />
					<span class="setting-description">This should be a string represetation of the date according to PHP/Wordpress' date formatting parameters. i.e. "l F j, Y". See <a href="http://us.php.net/manual/en/function.date.php">here</a>.</span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="time">Time format:</label></th>
				<td>
					<input type="text" name="time" class="regular-text" value="<?=$o['time'];?>" />
					<span class="setting-description">This should be a string represetation of the time according to PHP/Wordpress' date formatting parameters. i.e. "g:ia". See <a href="http://us.php.net/manual/en/function.date.php">here</a>.</span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="date_markup">Date mark-up:</label></th>
				<td>
					<textarea name="date_markup" style="width:100%;"><?=$o['date_markup'];?></textarea>
					<span class="setting-description">Use this in place of the date format fields. Use any mark-up you like and employ the '%' character around the PHP date formatting characters. e.g. &lt;span&gt;%l% %F% %t%, %Y%&lt;/span&gt;</span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="time_markup">Time mark-up:</label></th>
				<td>
					<textarea name="time_markup" style="width:100%;"><?=$o['time_markup'];?></textarea>
					<span class="setting-description">Use this in place of the time format fields. Use any mark-up you like and employ the '%' character around the PHP date formatting characters. e.g. &lt;span&gt;%g%:%i%%a%&lt;/span&gt;</span>
				</td>
			</tr>
		</table>
		<p class="submit"><input type="submit" name="submit" class="button-primary" value="Save Changes" /></p>
		<input type="hidden" name="action" value="update" />
		<input type="hidden" id="_wpnonce" name="_wpnonce" value="<?=wp_create_nonce('tern_wp_event_nonce');?>" />
		<input type="hidden" name="_wp_http_referer" value="<?php wp_get_referer(); ?>" />
	</form>
</div>
<?php
}
//                                *******************************                                 //
//________________________________** MARK-UP                   **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
function tern_wp_event_markup_options() {
	global $wpdb,$getWP,$getOPTS,$tern_wp_event_defaults,$tern_wp_msg,$tern_wp_event_markup_fields,$tern_wp_meta_fields,$current_user,$tern_wp_event_post;
	$o = $getWP->getOption('tern_wp_events',$tern_wp_event_defaults);
	get_currentuserinfo();
?>
	<div class="wrap">
		<div id="icon-options-general" class="icon32"><br /></div>
		<h2>Configure Your Event Page Mark-Up</h2>
		<p>
			Below you can configure what fields are shown when viewing your event list. Add fields to be displayed and edit their names, 
			mark-up and order. When editing their mark-up, use the string %value% to place the respective value for each field and use the string 
			%post_url% to add the url (e.g. http://blog.ternstyle.us/?p=100) for each respective post or page.
		</p>
		<div id="tern_wp_message">
		<?php
			if(!empty($tern_wp_msg)) {
				echo '<div id="message" class="updated fade"><p>'.$tern_wp_msg.'</p></div>';
			}
		?>
		</div>
		<form class="field-form" action="" method="get">
			<p class="field-box">
				<label class="hidden" for="new-field-input">Add New Field:</label>
				<?php
					foreach($tern_wp_event_markup_fields as $k => $v) {
						foreach($o['fields'] as $w) {
							if($v['field'] == $w['name']) {
								continue 2;
							}
						}
						$a['Standard Fields'][] = array($k,$v);
					}
					$r = $wpdb->get_col("select distinct meta_key from $wpdb->postmeta");
					foreach($r as $v) {
						foreach($o['fields'] as $w) {
							if($v == $w['name']) {
								continue 2;
							}
						}
						$a['Available Meta Fields'][] = array($v,$v);
					}
					echo $getOPTS->selectTiered($a,1,0,'new_field','new_field','Add New Field','',false);
				?>
				<input type="hidden" id="page" name="page" value="Configure Mark-Up" />
				<input type="submit" value="Add New Field" class="button" />
				<input type="hidden" name="action" value="add" />
				<input type="hidden" id="_wpnonce" name="_wpnonce" value="<?=wp_create_nonce('tern_wp_event_nonce');?>" />
				<input type="hidden" name="_wp_http_referer" value="<?php wp_get_referer(); ?>" />
			</p>
		</form>
		<form id="tern_wp_event_list_fm" method="post" action="">
			<table id="event_list_fields" class="widefat fixed" cellspacing="0">
				<thead>
				<tr class="thead">
					<th scope="col" id="cb" class="manage-column column-cb check-column" style=""><input type="checkbox" /></th>
					<th scope="col" id="field" class="manage-column column-field" style="width:20%;">Database Field</th>
					<th scope="col" id="name" class="manage-column column-name" style="width:20%;">Field Name</th>
					<th scope="col" id="markup" class="manage-column column-markup" style="">Mark-Up</th>
				</tr>
				</thead>
				<tfoot>
				<tr class="thead">
					<th scope="col" class="manage-column column-cb check-column" style=""><input type="checkbox" /></th>
					<th scope="col" id="field" class="manage-column column-field" style="">Database Field</th>
					<th scope="col" class="manage-column column-name" style="">Field Name</th>
					<th scope="col" class="manage-column column-markup" style="">Mark-Up</th>
				</tr>
				</tfoot>
				<tbody id="fields" class="list:fields field-list">
					<?php
						foreach($o['fields'] as $k => $v) {
							$d = empty($d) ? ' class="alternate"' : '';
					?>
							<tr id='field-<?=$v['name'];?>'<?=$d;?>>
								<th scope='row' class='check-column'><input type='checkbox' name='fields[]' id='field_<?=$v['name'];?>' value='<?=$v['name'];?>' /></th>
								<td class="field column-field">
									<input type="hidden" name="field_names%5B%5D" value="<?=$v['name'];?>" />
									<strong><?=$v['name'];?></strong><br />
									<div class="row-actions">
										<span class='edit tern_event_edit'><a href="javascript:tern_event_editField('field-<?=$v['name'];?>');">Edit</a> | </span>
										<span class='edit'><a href="admin.php?page=Configure%20Mark-Up&fields%5B%5D=<?=$v['name'];?>&action=remove&_wpnonce=<?=wp_create_nonce('tern_wp_event_nonce');?>">Remove</a></span>
									</div>
								</td>
								<td class="name column-name">
									<input type="text" name="field_titles%5B%5D" class="tern_event_fields hidden" value="<?=$k;?>" /><br class="tern_event_fields hidden" />
									<input type="button" value="Update Field" onclick="tern_event_renderField('field-<?=$v['name'];?>');return false;" class="tern_event_fields hidden button" />
									<span class="tern_event_fields field_titles"><?=$k;?></span>
								</td>
								<td class="markup column-markup">
									<textarea name="field_markups%5B%5D" class="tern_event_fields hidden" rows="4" cols="10"><?=$v['markup'];?></textarea><br class="tern_event_fields hidden" />
									<input type="button" value="Update Field" onclick="tern_event_renderField('field-<?=$v['name'];?>');return false;" class="tern_event_fields hidden button" />
									<span class="tern_event_fields field_markups"><?php echo htmlentities($v['markup']); ?></span>
								</td>
							</tr>
					<?php
						}
					?>
				</tbody>
			</table>
			<input type="hidden" name="action" value="update" />
			<input type="hidden" id="page" name="page" value="Configure Mark-Up" />
			<input type="hidden" id="_wpnonce" name="_wpnonce" value="<?=wp_create_nonce('tern_wp_event_nonce');?>" />
			<input type="hidden" name="_wp_http_referer" value="<?php wp_get_referer(); ?>" />
		</form>
		<h3>Your Mark-Up will look like this:</h3>
		<?php
			$p = get_posts('numberposts=1&category='.$o['category']);
			$tern_wp_event_post = $p[0]->ID;
			echo '<pre id="tern_event_sample_markup">'.htmlentities(tern_wp_event_markup()).'</pre>';
		?>
	</div>
<?php
}
//                                *******************************                                 //
//________________________________** COMPILE META FIELDS       **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
function tern_wp_events_box() {
	add_meta_box('tern_wp_event_meta','Event Information','tern_wp_event_meta','post','advanced');
}
function tern_wp_event_meta() {
	global $getOPTS,$getTIME,$post,$offset,$tern_wp_event_fields;
	$o = intval(get_option('gmt_offset'))*3600;
	$n = $getTIME->clientTime($o);
	foreach($tern_wp_event_fields as $v) {
		$$v['name'] = get_post_meta($post->ID,$v['name'],true);
	}
	//
	foreach($tern_wp_event_fields as $k => $v) {
		if($v['meta']) {
			echo '<label for"'.$v['name'].'">'.$k.':</label><br />';
			echo '<input type="text" name="'.$v['name'].'" id="'.$v['name'].'" size="30" value="'.$$v['name'].'" />';
		}
	}
	//
	echo '<div>';
	echo '<label for="tern_wp_event_start_month">';
	$m = empty($_tern_wp_event_start_date) ? intval(gmdate('n',$n)) : intval(gmdate('n',$_tern_wp_event_start_date));
	echo $getOPTS->createNumberOptions(1,12,'tern_wp_event_start_month','tern_wp_event_start_month','Start Month','',array($m));
	echo '<br /><span>start month</span></label>';
	
	echo '<label for="tern_wp_event_start_day">';
	$d = empty($_tern_wp_event_start_date) ? intval(gmdate('j',$n)) : intval(gmdate('j',$_tern_wp_event_start_date));
	echo $getOPTS->createNumberOptions(1,31,'tern_wp_event_start_day','tern_wp_event_start_day','Start Day','',array($d));
	echo '<br /><span>start day</span></label>';
	
	echo '<label for="tern_wp_event_start_year">';
	$y = empty($_tern_wp_event_start_date) ? intval(gmdate('Y',$n)) : intval(gmdate('Y',$_tern_wp_event_start_date));
	echo $getOPTS->createNumberOptions(2009,2015,'tern_wp_event_start_year','tern_wp_event_start_year','Start Year','',array($y));
	echo '<br /><span>start year</span></label>';
	
	echo '<label for="tern_wp_event_start_hour">';
	//$h = empty($tern_wp_event_start_date) ? intval(gmdate('h',$n)) : intval(gmdate('h',$tern_wp_event_start_date));
	$h = empty($_tern_wp_event_start_date) ? 12 : intval(gmdate('h',$_tern_wp_event_start_date));
	echo $getOPTS->createNumberOptions(1,12,'tern_wp_event_start_hour','tern_wp_event_start_hour','Start Hour','',array($h));
	echo '<br /><span>start hour</span></label>';
	
	echo '<label for="tern_wp_event_start_minute">';
	//$i = empty($tern_wp_event_start_date) ? intval(gmdate('i',$n)) : intval(gmdate('i',$tern_wp_event_start_date));
	$i = empty($_tern_wp_event_start_date) ? 0 : intval(gmdate('i',$_tern_wp_event_start_date));
	echo $getOPTS->createNumberOptions(00,59,'tern_wp_event_start_minute','tern_wp_event_start_minute','Start Minute','',array($i),'',false,true);
	echo '<br /><span>start minute</span></label>';
	
	echo '<label for="tern_wp_event_start_meridiem">';
	$a = empty($_tern_wp_event_start_date) ? 'pm' : gmdate('a',$_tern_wp_event_start_date);
	echo $getOPTS->select(array('am','pm'),'tern_wp_event_start_meridiem','tern_wp_event_start_meridiem','Start Meridiem','',false,array($a));
	echo '<br /><span>start meridiem</span></label>';
	echo '</div>';
	
	echo '<div>';
	echo '<label for="tern_wp_event_end_month">';
	$m = empty($_tern_wp_event_end_date) ? intval(gmdate('n',$n)) : intval(gmdate('n',$_tern_wp_event_end_date));
	echo $getOPTS->createNumberOptions(1,12,'tern_wp_event_end_month','tern_wp_event_end_month','End Month','',array($m));
	echo '<br /><span>end month</span></label>';
	
	echo '<label for="tern_wp_event_end_day">';
	$d = empty($_tern_wp_event_end_date) ? intval(gmdate('j',$n)) : intval(gmdate('j',$_tern_wp_event_end_date));
	echo $getOPTS->createNumberOptions(1,31,'tern_wp_event_end_day','tern_wp_event_end_day','End Day','',array($d));
	echo '<br /><span>end day</span></label>';
	
	echo '<label for="tern_wp_event_end_year">';
	$y = empty($_tern_wp_event_end_date) ? intval(gmdate('Y',$n)) : intval(gmdate('Y',$_tern_wp_event_end_date));
	echo $getOPTS->createNumberOptions(2009,2015,'tern_wp_event_end_year','tern_wp_event_end_year','End Year','',array($y));
	echo '<br /><span>end year</span></label>';
	
	echo '<label for="tern_wp_event_end_hour">';
	//$h = empty($tern_wp_event_end_date) ? intval(gmdate('h',$n)) : intval(gmdate('h',$tern_wp_event_end_date));
	$h = empty($_tern_wp_event_end_date) ? 12 : intval(gmdate('h',$_tern_wp_event_end_date));
	echo $getOPTS->createNumberOptions(1,12,'tern_wp_event_end_hour','tern_wp_event_end_hour','End Hour','',array($h));
	echo '<br /><span>end hour</span></label>';
	
	echo '<label for="tern_wp_event_end_minute">';
	//$i = empty($tern_wp_event_end_date) ? intval(gmdate('i',$n)) : intval(gmdate('i',$tern_wp_event_end_date));
	$i = empty($_tern_wp_event_end_date) ? 0 : intval(gmdate('i',$_tern_wp_event_end_date));
	echo $getOPTS->createNumberOptions(00,59,'tern_wp_event_end_minute','tern_wp_event_end_minute','End Minute','',array($i),'',false,true);
	echo '<br /><span>end minute</span></label>';
	
	echo '<label for="tern_wp_event_end_meridiem">';
	$a = empty($_tern_wp_event_end_date) ? 'pm' : gmdate('a',$_tern_wp_event_end_date);
	echo $getOPTS->select(array('am','pm'),'tern_wp_event_end_meridiem','tern_wp_event_end_meridiem','End Meridiem','',false,array($a));
	echo '<br /><span>end meridiem</span></label>';
	echo '</div>';
		
	echo '<input type="hidden" name="tern_wp_event_nonce" id="tern_wp_event_nonce" value="'.wp_create_nonce(plugin_basename(__FILE__)).'" />';
}
//                                *******************************                                 //
//________________________________** COMPILE EVENTS PAGE       **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
function tern_wp_events() {
	global $getWP,$tern_wp_event_defaults,$wpdb,$tern_wp_event_is_list,$tern_wp_event_post;
	$tern_wp_event_is_list = true;
	$o = $getWP->getOption('tern_wp_events',$tern_wp_event_defaults);
	//
	$page = empty($_GET['page']) ? (tern_wp_event_page()-1)*$o['limit'] : (intval($_GET['page'])-1)*$o['limit'];
	$p = $wpdb->get_results("select ID from $wpdb->posts as p join $wpdb->term_relationships as r on (r.object_id = p.ID and r.term_taxonomy_id = ".$o['category'].") left join $wpdb->postmeta as m on (p.ID = m.post_id and m.meta_key = '_tern_wp_event_start_date') where m.meta_value > ".time()." order by m.meta_value ".$o['order']." limit ".$page.','.$o['limit']);
	//
	$t = $wpdb->get_var("select COUNT(*) from $wpdb->posts as p join $wpdb->term_relationships as r on (r.object_id = p.ID and r.term_taxonomy_id = ".$o['category'].") left join $wpdb->postmeta as m on (p.ID = m.post_id and m.meta_key = '_tern_wp_event_start_date') where m.meta_value > ".time());
	if(!empty($p)) {
		//pagination
		$n = new pagination(array(
			'total'	=>	$t,
			'limit'	=>	$o['limit'],
			'url'	=>	$o['url'],
			'seo'	=>	$o['pages']
		));
		//
		echo '<ul class="tern_wp_events">';
		if(is_array($p)) {
			$c = 1;
			foreach($p as $v) {
				$tern_wp_event_post = $v->ID;
				echo '<li class="tern_wp_event_'.$c.' post">'.tern_wp_event_markup().'</li>';
				$c++;
			}
			$tern_wp_event_post = NULL;
		}
		echo '</ul>';
		//
		$n = new pagination(array(
			'total'	=>	$t,
			'limit'	=>	$o['limit'],
			'url'	=>	$o['url'],
			'seo'	=>	$o['pages']
		));
	}
	else {
		echo '<h3>Sorry, currently there are no upcoming events.</h3>';
	}
}
function tern_wp_event_next_upcoming() {
	global $getWP,$wpdb,$tern_wp_event_defaults,$tern_wp_event_post;
	$o = $getWP->getOption('tern_wp_events',$tern_wp_event_defaults);
	//
	$p = $wpdb->get_var("select ID from $wpdb->posts as p join $wpdb->postmeta as m on (p.ID = m.post_id and m.meta_key = '_tern_wp_event_start_date' and m.meta_value > ".time().") left join $wpdb->term_relationships as r on (r.object_id = p.ID) where term_taxonomy_id = ".$o['category']." order by m.meta_value asc limit 1");
	$tern_wp_event_post = $p;
	//
	echo tern_wp_event_markup();
	$tern_wp_event_post = NULL;
}
function tern_wp_event_page() {
	$u = explode('/',$_SERVER['REQUEST_URI']);
	foreach($u as $k => $v) {
		if(empty($v)) {
			unset($u[$k]);
		}
	}
	$u = array_values($u);
	$v = $u[count($u)-1];
	$v = ereg('^[0-9]+$',$v) ? $v : 1;
	return $v;
}
function tern_wp_event_markup() {
	global $tern_wp_event_post,$getWP,$tern_wp_event_defaults,$tern_wp_event_markup_fields,$post;
	if($tern_wp_event_post) {
		$o = $getWP->getOption('tern_wp_events',$tern_wp_event_defaults);
		$post = get_post($tern_wp_event_post);
		setup_postdata($post);
		//
		foreach($o['fields'] as $k => $v) {
			$args = '';
			if($tern_wp_event_markup_fields[$k]['args']) {
				$args = $tern_wp_event_markup_fields[$k]['args'] == 'id' ? $p->ID : $tern_wp_event_markup_fields[$k]['args'];
			}
			eval('$w = '.$tern_wp_event_markup_fields[$k]['func'].'('.$args.');');
			$w = $tern_wp_event_markup_fields[$k]['func'] !== false ? $w : $post->$v['name'];
			$s .= "\n        ".str_replace('%post_url%',get_permalink($p),str_replace('%value%',$w,$v['markup']));
		}
		return $s;
	}
}
function tern_wp_event_date($a=array()) {
	global $getWP,$getTIME,$tern_wp_event_defaults,$post,$tern_wp_event_is_list,$tern_wp_event_date;
	$o = $getWP->getOption('tern_wp_events',$tern_wp_event_defaults);
	$p = !$a['id'] ? $post->ID : $a['id'];
	$b = get_post_meta($p,'_tern_wp_event_start_date',true);
	$e = get_post_meta($p,'_tern_wp_event_end_date',true);
	//
	$c = array(
		'id'	=>	false,
		'echo'	=>	false,
		'stamp'	=>	NULL
	);
	foreach($c as $k => $v) {
		if(!isset($a[$k])) {
			$a[$k] = $v;
		}
	}
	$single = false;
	if(!empty($a['stamp'])) {
		$b = $a['stamp'];
		$single = true;
	}
	//
	if(empty($o['date_markup'])) {
		$s .= gmdate($o['format'],$b);
		//
		if($o['show_time'] or !$tern_wp_event_is_list) {
			$s .= empty($o['d_2_t_sep']) ? ' ' : $o['d_2_t_sep'];
			$s .= gmdate($o['time'],$b);
		}
		//
		if(($o['end_time'] or !$tern_wp_event_is_list) and !$single) {
			if($getTIME->atStartStamp($b) == $getTIME->atStartStamp($e)) {
				$s .= empty($o['time_sep']) ? ' - ' : $o['time_sep'];
				$s .= gmdate($o['time'],$e);
			}
			else {
				$s .= empty($o['date_sep']) ? ' -- ' : $o['date_sep'];
				$s .= gmdate($o['format'],$e);
				$s .= empty($o['d_2_t_sep']) ? ' ' : $o['d_2_t_sep'];
				$s .= gmdate($o['time'],$e);
			}
		}
	}
	else {
		$tern_wp_event_date = $b;
		$s .= preg_replace_callback('/\%([a-zA-Z]+)\%/','tern_wp_event_date_markup',$o['date_markup']);
		//
		if($o['show_time'] or !$tern_wp_event_is_list) {
			$s .= empty($o['d_2_t_sep']) ? ' ' : $o['d_2_t_sep'];
			$s .= preg_replace_callback('/\%([a-zA-Z]+)\%/','tern_wp_event_date_markup',$o['time_markup']);
		}
		//
		$tern_wp_event_date = $e;
		if(($o['end_time'] or !$tern_wp_event_is_list) and !$single) {
			if($getTIME->atStartStamp($b) == $getTIME->atStartStamp($e)) {
				$s .= empty($o['time_sep']) ? ' - ' : $o['time_sep'];
				$s .= preg_replace_callback('/\%([a-zA-Z]+)\%/','tern_wp_event_date_markup',$o['time_markup']);
			}
			else {
				$s .= empty($o['date_sep']) ? ' -- ' : $o['date_sep'];
				$s .= preg_replace_callback('/\%([a-zA-Z]+)\%/','tern_wp_event_date_markup',$o['date_markup']);
				$s .= empty($o['d_2_t_sep']) ? ' ' : $o['d_2_t_sep'];
				$s .= preg_replace_callback('/\%([a-zA-Z]+)\%/','tern_wp_event_date_markup',$o['time_markup']);
			}
		}
	}
	if($f) {
		echo $s;
	}
	return $s;
}
function tern_wp_event_date_markup($m) {
	global $tern_wp_event_date;
	return gmdate($m[1],$tern_wp_event_date);
}
function tern_wp_event_meta_fields($e=false) {
	global $getWP,$tern_wp_event_defaults,$post,$tern_wp_event_fields;
	$o = $getWP->getOption('tern_wp_events',$tern_wp_event_defaults);
	$p = !$p ? $post->ID : $p;
	//
	$s = '<div class="tern_wp_event_meta">';
	foreach($tern_wp_event_fields as $k => $v) {
		if($v['meta']) {
			$m = get_post_meta($p,$v['name'],true);
			if(!empty($m)) {
				$s .= '<div class="tern_wp_event_meta_data"><label>'.$k.':</label>'.$m.'</div>';
			}
		}
	}
	$s .= '<div class="tern_wp_event_meta_data"><label>Start Date:</label>'.tern_wp_event_date(array('stamp'=>get_post_meta($p,'_tern_wp_event_start_date',true))).'</div>';
	$s .= '<div class="tern_wp_event_meta_data"><label>End Date:</label>'.tern_wp_event_date(array('stamp'=>get_post_meta($p,'_tern_wp_event_end_date',true))).'</div>';
	$s.= '</div>';
	if($e) {
		echo $s;
	}
	return $s;
}
function tern_wp_event_has_upcoming() {
	global $getWP,$wpdb,$tern_wp_event_defaults;
	$o = $getWP->getOption('tern_wp_events',$tern_wp_event_defaults);
	$p = $wpdb->get_var("select ID from $wpdb->posts as p join $wpdb->postmeta as m on (p.ID = m.post_id and m.meta_key = '_tern_wp_event_start_date' and m.meta_value > ".time().") left join $wpdb->term_relationships as r on (r.object_id = p.ID) where term_taxonomy_id = ".$o['category']." order by m.meta_value asc limit 1");
	if($p) {
		return true;
	}
	return false;
}
//                                *******************************                                 //
//________________________________** MISCELLANEOUS             **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
function tern_wp_sort($a,$o='asc',$c=false) {
	global $getTIME,$meta_field,$meta_field_end;
	$t = $getTIME->atStartStamp($getTIME->utcNow());
	$r = array();
	foreach($a as $v) {
		if(empty($v->d)) {
			$v->d = get_post_meta($v->ID,$meta_field,true);
			$v->e = get_post_meta($v->ID,$meta_field_end,true);
			$v->e = $v->e ? $v->e : $v->d;
			$v->t = $v->d ? $getTIME->atStartStamp($v->d) : false;
		}
		if(!$c or ($c and $v->e>=$t)) {
			if(empty($r)) {
				$r[] = $v;
			}
			else {
				for($b=0;$b<count($r);$b++) {
					if($v->d < $r[$b]->d or $v->d == $r[$b]->d) {
						$n = array($v);
						array_splice($r,$b,0,$n);
						break;
					}
					elseif($v->d > $r[$b]->d and $b == (count($r)-1)) {
						array_push($r,$v);
						break;
					}
				}
			}
		}
	}
	if($o == 'desc') {
		$r = array_reverse($r);
	}
	return $r;
}

/****************************************Terminate Script******************************************/
?>