<?php
////////////////////////////////////////////////////////////////////////////////////////////////////
//
//		File:
//			meta.php
//		Description:
//			This file compiles and processes the plugin's meta fields.
//		Actions:
//			1) compile meta data form for posts
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
$pages = array('post.php','edit.php','post-new.php','page.php','page-new.php');
if(!in_array($GLOBALS['pagenow'],$pages)) {
	return;
}
//                                *******************************                                 //
//________________________________** ADD EVENTS                **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
add_action('admin_menu','WP_event_page_box');
add_action('save_post','WP_event_page_save');
add_action('publish_post','WP_event_page_save');
//                                *******************************                                 //
//________________________________** ACTIONS                   **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
function WP_event_page_save($i) {
	global $WP_event_page_fields;
	$i = wp_is_post_revision($i);
	if(!wp_verify_nonce($_POST['tern_wp_event_nonce'],plugin_basename(__FILE__)) or !$i) {
		return;
	}
	if(!current_user_can('edit_post',$i)) {
		return;
	}
	foreach($WP_event_page_fields as $v) {
		$n = $v['name'];
		if($n == '_tern_wp_event_start_date') {
			$h = $_POST['tern_wp_event_start_hour'];
			if($_POST['tern_wp_event_start_meridiem'] == 'pm' and intval($_POST['tern_wp_event_start_hour']) != 12) {
				$h = intval($_POST['tern_wp_event_start_hour'])+12;
			}
			elseif($_POST['tern_wp_event_start_meridiem'] == 'am' and intval($_POST['tern_wp_event_start_hour']) == 12) {
				$h = 0;
			}
			$start = gmmktime($h,$_POST['tern_wp_event_start_minute'],0,$_POST['tern_wp_event_start_month'],$_POST['tern_wp_event_start_day'],$_POST['tern_wp_event_start_year']);
			update_post_meta($i,$n,$start);
		}
		elseif($n == '_tern_wp_event_end_date') {
			$h = $_POST['tern_wp_event_end_hour'];
			if($_POST['tern_wp_event_end_meridiem'] == 'pm' and intval($_POST['tern_wp_event_end_hour']) != 12) {
				$h = intval($_POST['tern_wp_event_end_hour'])+12;
			}
			elseif($_POST['tern_wp_event_end_meridiem'] == 'am' and intval($_POST['tern_wp_event_end_hour']) == 12) {
				$h = 0;
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
//                                *******************************                                 //
//________________________________** META BOXES                **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
function WP_event_page_box() {
	add_meta_box('WP_event_page_meta','Event Information','WP_event_page_meta','post','advanced');
}
function WP_event_page_meta() {
	global $ternSel,$getTIME,$post,$offset,$WP_event_page_fields;
	$o = intval(get_option('gmt_offset'))*3600;
	$n = $getTIME->clientTime($o);
	foreach($WP_event_page_fields as $v) {
		$$v['name'] = get_post_meta($post->ID,$v['name'],true);
	}
	//
	foreach($WP_event_page_fields as $k => $v) {
		if($v['meta']) {
			echo '<label for"'.$v['name'].'">'.$k.':</label><br />';
			echo '<input type="text" name="'.$v['name'].'" id="'.$v['name'].'" size="30" value="'.$$v['name'].'" />';
		}
	}
	//
	echo '<div>';
	echo '<label for="tern_wp_event_start_month">';
	$m = empty($_tern_wp_event_start_date) ? intval(gmdate('n',$n)) : intval(gmdate('n',$_tern_wp_event_start_date));
	echo $ternSel->create(array(
		'type'		=>	'numbers',
		'start'		=>	1,
		'finish'	=>	12,
		'name'		=>	'tern_wp_event_start_month',
		'id'		=>	'tern_wp_event_start_month',
		'title'		=>	'Start Month',
		'selected'	=>	array($m)
	));
	//echo $getOPTS->createNumberOptions(1,12,'tern_wp_event_start_month','tern_wp_event_start_month','Start Month','',array($m));
	echo '<br /><span>start month</span></label>';
	
	echo '<label for="tern_wp_event_start_day">';
	$d = empty($_tern_wp_event_start_date) ? intval(gmdate('j',$n)) : intval(gmdate('j',$_tern_wp_event_start_date));
	echo $ternSel->create(array(
		'type'		=>	'numbers',
		'start'		=>	1,
		'finish'	=>	31,
		'name'		=>	'tern_wp_event_start_day',
		'id'		=>	'tern_wp_event_start_day',
		'title'		=>	'Start Day',
		'selected'	=>	array($d)
	));
	//echo $getOPTS->createNumberOptions(1,31,'tern_wp_event_start_day','tern_wp_event_start_day','Start Day','',array($d));
	echo '<br /><span>start day</span></label>';
	
	echo '<label for="tern_wp_event_start_year">';
	$y = empty($_tern_wp_event_start_date) ? intval(gmdate('Y',$n)) : intval(gmdate('Y',$_tern_wp_event_start_date));
	echo $ternSel->create(array(
		'type'		=>	'numbers',
		'start'		=>	2011,
		'finish'	=>	2020,
		'name'		=>	'tern_wp_event_start_year',
		'id'		=>	'tern_wp_event_start_year',
		'title'		=>	'Start Year',
		'selected'	=>	array($y)
	));
	//echo $getOPTS->createNumberOptions(2009,2015,'tern_wp_event_start_year','tern_wp_event_start_year','Start Year','',array($y));
	echo '<br /><span>start year</span></label>';
	
	echo '<label for="tern_wp_event_start_hour">';
	//$h = empty($tern_wp_event_start_date) ? intval(gmdate('h',$n)) : intval(gmdate('h',$tern_wp_event_start_date));
	$h = empty($_tern_wp_event_start_date) ? 12 : intval(gmdate('h',$_tern_wp_event_start_date));
	echo $ternSel->create(array(
		'type'		=>	'numbers',
		'start'		=>	1,
		'finish'	=>	12,
		'name'		=>	'tern_wp_event_start_hour',
		'id'		=>	'tern_wp_event_start_hour',
		'title'		=>	'Start Hour',
		'selected'	=>	array($h)
	));
	//echo $getOPTS->createNumberOptions(1,12,'tern_wp_event_start_hour','tern_wp_event_start_hour','Start Hour','',array($h));
	echo '<br /><span>start hour</span></label>';
	
	echo '<label for="tern_wp_event_start_minute">';
	//$i = empty($tern_wp_event_start_date) ? intval(gmdate('i',$n)) : intval(gmdate('i',$tern_wp_event_start_date));
	$i = empty($_tern_wp_event_start_date) ? 0 : intval(gmdate('i',$_tern_wp_event_start_date));
	echo $ternSel->create(array(
		'type'		=>	'numbers',
		'start'		=>	00,
		'finish'	=>	59,
		'name'		=>	'tern_wp_event_start_minute',
		'id'		=>	'tern_wp_event_start_minute',
		'title'		=>	'Start Minute',
		'selected'	=>	array($i),
		'zeros'		=>	true
	));
	//echo $getOPTS->createNumberOptions(00,59,'tern_wp_event_start_minute','tern_wp_event_start_minute','Start Minute','',array($i),'',false,true);
	echo '<br /><span>start minute</span></label>';
	
	echo '<label for="tern_wp_event_start_meridiem">';
	$a = empty($_tern_wp_event_start_date) ? 'pm' : gmdate('a',$_tern_wp_event_start_date);
	echo $ternSel->create(array(
		'type'		=>	'select',
		'data'		=>	array('am','pm'),
		'name'		=>	'tern_wp_event_start_meridiem',
		'id'		=>	'tern_wp_event_start_meridiem',
		'title'		=>	'Start Meridiem',
		'selected'	=>	array($a)
	));
	//echo $getOPTS->select(array('am','pm'),'tern_wp_event_start_meridiem','tern_wp_event_start_meridiem','Start Meridiem','',false,array($a));
	echo '<br /><span>start meridiem</span></label>';
	echo '</div>';
	
	echo '<div>';
	echo '<label for="tern_wp_event_end_month">';
	$m = empty($_tern_wp_event_end_date) ? intval(gmdate('n',$n)) : intval(gmdate('n',$_tern_wp_event_end_date));
	echo $ternSel->create(array(
		'type'		=>	'numbers',
		'start'		=>	1,
		'finish'	=>	12,
		'name'		=>	'tern_wp_event_end_month',
		'id'		=>	'tern_wp_event_end_month',
		'title'		=>	'End Month',
		'selected'	=>	array($m)
	));
	//echo $getOPTS->createNumberOptions(1,12,'tern_wp_event_end_month','tern_wp_event_end_month','End Month','',array($m));
	echo '<br /><span>end month</span></label>';
	
	echo '<label for="tern_wp_event_end_day">';
	$d = empty($_tern_wp_event_end_date) ? intval(gmdate('j',$n)) : intval(gmdate('j',$_tern_wp_event_end_date));
	echo $ternSel->create(array(
		'type'		=>	'numbers',
		'start'		=>	1,
		'finish'	=>	31,
		'name'		=>	'tern_wp_event_end_day',
		'id'		=>	'tern_wp_event_end_day',
		'title'		=>	'End Day',
		'selected'	=>	array($d)
	));
	//echo $getOPTS->createNumberOptions(1,31,'tern_wp_event_end_day','tern_wp_event_end_day','End Day','',array($d));
	echo '<br /><span>end day</span></label>';
	
	echo '<label for="tern_wp_event_end_year">';
	$y = empty($_tern_wp_event_end_date) ? intval(gmdate('Y',$n)) : intval(gmdate('Y',$_tern_wp_event_end_date));
	echo $ternSel->create(array(
		'type'		=>	'numbers',
		'start'		=>	2011,
		'finish'	=>	2020,
		'name'		=>	'tern_wp_event_end_year',
		'id'		=>	'tern_wp_event_end_year',
		'title'		=>	'End Year',
		'selected'	=>	array($y)
	));
	//echo $getOPTS->createNumberOptions(2009,2015,'tern_wp_event_end_year','tern_wp_event_end_year','End Year','',array($y));
	echo '<br /><span>end year</span></label>';
	
	echo '<label for="tern_wp_event_end_hour">';
	//$h = empty($tern_wp_event_end_date) ? intval(gmdate('h',$n)) : intval(gmdate('h',$tern_wp_event_end_date));
	$h = empty($_tern_wp_event_end_date) ? 12 : intval(gmdate('h',$_tern_wp_event_end_date));
	echo $ternSel->create(array(
		'type'		=>	'numbers',
		'start'		=>	1,
		'finish'	=>	12,
		'name'		=>	'tern_wp_event_end_hour',
		'id'		=>	'tern_wp_event_end_hour',
		'title'		=>	'End Hour',
		'selected'	=>	array($h)
	));
	//echo $getOPTS->createNumberOptions(1,12,'tern_wp_event_end_hour','tern_wp_event_end_hour','End Hour','',array($h));
	echo '<br /><span>end hour</span></label>';
	
	echo '<label for="tern_wp_event_end_minute">';
	//$i = empty($tern_wp_event_end_date) ? intval(gmdate('i',$n)) : intval(gmdate('i',$tern_wp_event_end_date));
	$i = empty($_tern_wp_event_end_date) ? 0 : intval(gmdate('i',$_tern_wp_event_end_date));
	echo $ternSel->create(array(
		'type'		=>	'numbers',
		'start'		=>	00,
		'finish'	=>	59,
		'name'		=>	'tern_wp_event_end_minute',
		'id'		=>	'tern_wp_event_end_minute',
		'title'		=>	'End Minute',
		'selected'	=>	array($i),
		'zeros'		=>	true
	));
	//echo $getOPTS->createNumberOptions(00,59,'tern_wp_event_end_minute','tern_wp_event_end_minute','End Minute','',array($i),'',false,true);
	echo '<br /><span>end minute</span></label>';
	
	echo '<label for="tern_wp_event_end_meridiem">';
	$a = empty($_tern_wp_event_end_date) ? 'pm' : gmdate('a',$_tern_wp_event_end_date);
	echo $ternSel->create(array(
		'type'		=>	'select',
		'data'		=>	array('am','pm'),
		'name'		=>	'tern_wp_event_end_meridiem',
		'id'		=>	'tern_wp_event_end_meridiem',
		'title'		=>	'End Meridiem',
		'selected'	=>	array($a)
	));
	//echo $getOPTS->select(array('am','pm'),'tern_wp_event_end_meridiem','tern_wp_event_end_meridiem','End Meridiem','',false,array($a));
	echo '<br /><span>end meridiem</span></label>';
	echo '</div>';
		
	echo '<input type="hidden" name="tern_wp_event_nonce" id="tern_wp_event_nonce" value="'.wp_create_nonce(plugin_basename(__FILE__)).'" />';
}

/****************************************Terminate Script******************************************/
?>