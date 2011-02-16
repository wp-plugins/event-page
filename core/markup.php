<?php
////////////////////////////////////////////////////////////////////////////////////////////////////
//
//		File:
//			markup.php
//		Description:
//			This file compiles and processes the plugin's configure mark-up page.
//		Actions:
//			1) compile plugin mark-up form
//			2) process and save plugin mark-up
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
if(!isset($_REQUEST['page']) or $_REQUEST['page'] !== 'tern-wp-event-page-mark-up') {
	return;
}
//                                *******************************                                 //
//________________________________** ADD EVENTS                **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
add_action('init','WP_event_page_markup_actions');
add_action('init','WP_event_page_markup_styles');
add_action('init','WP_event_page_markup_scripts');
//                                *******************************                                 //
//________________________________** SCRIPTS                   **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
function WP_event_page_markup_styles() {
	
}
function WP_event_page_markup_scripts() {
	wp_enqueue_script('TableDnD',get_bloginfo('home').'/wp-content/plugins/event-page/js/jquery.tablednd_0_5.js.php',array('jquery'),'0.5');
	wp_enqueue_script('event-page-markup',get_bloginfo('home').'/wp-content/plugins/event-page/js/markup.js');
}
//                                *******************************                                 //
//________________________________** ACTIONS                   **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
function WP_event_page_markup_actions() {
	global $getWP,$WP_event_page_defaults,$WP_event_page_markup_fields,$current_user;
	get_currentuserinfo();
	$o = $getWP->getOption('tern_wp_events',$WP_event_page_defaults);
	
	//Configure Mark-Up Page Actions
	if(wp_verify_nonce($_REQUEST['_wpnonce'],'tern_wp_event_nonce')) {
		switch($_REQUEST['action']) {
			//update all fields
			case 'update' :
				$o['fields'] = array();
				foreach($_REQUEST['field_names'] as $k => $v) {
					$v = stripslashes($v);
					$o['fields'][$v] = array(
						'field'		=>	$_REQUEST['fields'][$k],
						'markup'	=>	stripslashes($_REQUEST['field_markups'][$k])
					);
				}
				$o = $getWP->getOption('tern_wp_events',$o,true);
				$getWP->addError('<div id="message" class="updated fade"><p>Your order has been successfully saved.</p></div>');
				die();
			//add a field
			case 'add' :
				$n = $_REQUEST['new_field'];
				$f = empty($WP_event_page_markup_fields[$n]['field']) ? $n : $WP_event_page_markup_fields[$n]['field'];
				$o['fields'][$n] = array(
					'field'		=>	$f,
					'markup'	=>	'<div class="tern_wp_event_'.$f.'">%value%</div>'
				);
				$o = $getWP->getOption('tern_wp_events',$o,true);
			//delete a field
			case 'remove' :
				$a = array();
				foreach($o['fields'] as $k => $v) {
					if($k != $_REQUEST['fields'][0]) {
						$a[$k] = $v;
					}
				}
				$o['fields'] = $a;
				$o = $getWP->getOption('tern_wp_events',$o,true);
		}
	}
	
	//attempted to update all fields without nonce
	elseif($_REQUEST['action'] == 'update' or $_REQUEST['action'] == 'add' or $_REQUEST['action'] == 'remove') {
		$getWP->addError('<div id="message" class="updated fade"><p>There was an error while processing your request. Please try again.</p></div>');
	}
	
	//get sample mark-up
	if($_REQUEST['action'] == 'getmarkup') {
		$p = get_posts('numberposts=1&category='.$o['category']);
		ob_start();
		WP_event_page_markup($p[0]->ID);
		$s = ob_get_contents();
		ob_end_clean();
		echo htmlentities($s);
		die();
	}
}
//                                *******************************                                 //
//________________________________** SETTINGS                  **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
function WP_event_page_markup_options() {
	global $wpdb,$getWP,$ternSel,$WP_event_page_defaults,$tern_wp_msg,$WP_event_page_markup_fields,$tern_wp_meta_fields,$current_user,$tern_wp_event_post;
	$o = $getWP->getOption('tern_wp_events',$WP_event_page_defaults);
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
		<form class="field-form" action="" method="post">
			<p class="field-box">
				<label class="hidden" for="new-field-input">Add New Field:</label>
				<?php
					foreach((array)$WP_event_page_markup_fields as $k => $v) {
						foreach($o['fields'] as $w) {
							if($v['field'] == $w['name']) {
								continue 2;
							}
						}
						$a['Standard Fields'][] = array($k,$v['field']);
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
					echo $ternSel->create(array(
						'type'		=>	'tiered',
						'data'		=>	$a,
						'name'		=>	'new_field',
						'title'		=>	'Add New Field',
						'key'		=>	0,
						'value'		=>	0
					));
					
					//echo $getOPTS->selectTiered($a,0,0,'new_field','new_field','Add New Field','',false);
				?>
				<input type="hidden" name="page" value="tern-wp-event-page-mark-up" />
				<input type="submit" value="Add New Field" class="button" />
				<input type="hidden" name="action" value="add" />
				<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('tern_wp_event_nonce');?>" />
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
							<tr id='field-<?php echo $v['field'];?>'<?php echo $d;?>>
								<th scope='row' class='check-column'><input type='checkbox' name="" value='<?php echo $v['field'];?>' /></th>
								<td class="field column-field">
									<input type="hidden" name="fields%5B%5D" value="<?php echo $v['field'];?>" />
									<strong><?php echo $v['field'];?></strong><br />
									<div class="row-actions">
										<span class='edit tern_event_edit'><a href="javascript:tern_event_editField('field-<?php echo $v['field'];?>');">Edit</a> | </span>
										<span class='edit'><a href="admin.php?page=tern-wp-event-page-mark-up&fields%5B%5D=<?php echo $k;?>&action=remove&_wpnonce=<?php echo wp_create_nonce('tern_wp_event_nonce');?>">Remove</a></span>
									</div>
								</td>
								<td class="name column-name">
									<input type="hidden" name="field_names%5B%5D" value="<?php echo $k;?>" />
									<span class="field_titles"><?php echo $k;?></span>
								</td>
								<td class="markup column-markup">
									<textarea name="field_markups%5B%5D" class="tern_event_fields hidden" rows="4" cols="10"><?php echo $v['markup'];?></textarea><br class="tern_event_fields hidden" />
									<input type="button" value="Update Field" onclick="tern_event_renderField('field-<?php echo $v['field'];?>');return false;" class="tern_event_fields hidden button" />
									<span class="tern_event_fields field_markups"><?php echo htmlentities($v['markup']); ?></span>
								</td>
							</tr>
					<?php
						}
					?>
				</tbody>
			</table>
			<input type="hidden" name="action" value="update" />
			<input type="hidden" name="page" value="tern-wp-event-page-mark-up" />
			<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('tern_wp_event_nonce');?>" />
			<input type="hidden" name="_wp_http_referer" value="<?php wp_get_referer(); ?>" />
		</form>
		<h3>Your Mark-Up will look like this:</h3>
		<?php
			$p = get_posts('numberposts=1&category='.$o['category']);
			$tern_wp_event_post = $p[0]->ID;
			ob_start();
			WP_event_page_markup();
			$s = ob_get_contents();
			ob_end_clean();
			echo '<pre id="tern_event_sample_markup">'.htmlentities($s).'</pre>';
		?>
	</div>
<?php
}

/****************************************Terminate Script******************************************/
?>