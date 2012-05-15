<?php
////////////////////////////////////////////////////////////////////////////////////////////////////
//
//		File:
//			event-page.php
//		Description:
//			This file compiles the event list.
//		Actions:
//			1) compile event list
//			2) render event mark-up
//			3) render event meta data
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
//________________________________** COMPILE EVENTS PAGE       **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
function tern_wp_events() {
	WP_event_page_events();
}
function WP_event_page_events() {
	global $getWP,$WP_event_page_defaults,$wpdb,$WP_event_page_is_list,$tern_wp_event_post,$getTIME;
	$WP_event_page_is_list = true;
	$o = $getWP->getOption('tern_wp_events',$WP_event_page_defaults);
	//
	$page = empty($_GET['page']) ? (WP_event_page_paged()-1)*$o['limit'] : (intval($_GET['page'])-1)*$o['limit'];
	$p = $wpdb->get_results("select ID from $wpdb->posts as p join $wpdb->term_relationships as r join $wpdb->term_taxonomy as t on (r.object_id = p.ID and r.term_taxonomy_id = t.term_taxonomy_id and t.term_id = ".$o['category'].") left join $wpdb->postmeta as o on (p.ID = o.post_id and o.meta_key = '_tern_wp_event_start_date') left join $wpdb->postmeta as m on (p.ID = m.post_id and m.meta_key = '_tern_wp_event_end_date') where m.meta_value >= ".$getTIME->atStartStamp(time())." order by o.meta_value ".$o['order']." limit ".$page.','.$o['limit']);
	//
	$t = $wpdb->get_var("select COUNT(*) from $wpdb->posts as p join $wpdb->term_relationships as r join $wpdb->term_taxonomy as t on (r.object_id = p.ID and r.term_taxonomy_id = t.term_taxonomy_id and t.term_id = ".$o['category'].") left join $wpdb->postmeta as m on (p.ID = m.post_id and m.meta_key = '_tern_wp_event_end_date') where m.meta_value >= ".$getTIME->utcNow());
	if(!empty($p)) {
		//pagination
		$n = new pagination(array(
			'total'	=>	$t,
			'limit'	=>	$o['limit'],
			'url'	=>	get_permalink($o['url']),
			'seo'	=>	$o['pages']
		));
		//
		echo '<ul class="tern_wp_events">';
		if(is_array($p)) {
			$c = 1;
			foreach($p as $v) {
				$tern_wp_event_post = $v->ID;
				echo '<li class="tern_wp_event_'.$c.' post">';
				WP_event_page_markup();
				echo '</li>';
				$c++;
			}
			$tern_wp_event_post = NULL;
		}
		echo '</ul>';
		//
		$n = new pagination(array(
			'total'	=>	$t,
			'limit'	=>	$o['limit'],
			'url'	=>	get_permalink($o['url']),
			'seo'	=>	$o['pages']
		));
	}
	else {
		echo '<h3>Sorry, currently there are no upcoming events.</h3>';
	}
}
function tern_wp_event_next_upcoming() {
	WP_event_page_next_upcoming();
}
function WP_event_page_next_upcoming() {
	global $getWP,$wpdb,$WP_event_page_defaults,$tern_wp_event_post,$getTIME;
	$o = $getWP->getOption('tern_wp_events',$WP_event_page_defaults);
	//
	$p = $wpdb->get_var("select ID from $wpdb->posts as p join $wpdb->postmeta as m on (p.ID = m.post_id and m.meta_key = '_tern_wp_event_end_date' and m.meta_value >= ".$getTIME->atStartStamp(time()).") left join $wpdb->postmeta as o on (p.ID = o.post_id and o.meta_key = '_tern_wp_event_start_date') left join $wpdb->term_relationships as r on (r.object_id = p.ID) where term_taxonomy_id = ".$o['category']." order by o.meta_value asc limit 1");
	$tern_wp_event_post = $p;
	//
	WP_event_page_markup();
	$tern_wp_event_post = NULL;
}
function WP_event_page_paged() {
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
function WP_event_page_markup() {
	global $tern_wp_event_post,$getWP,$WP_event_page_defaults,$WP_event_page_markup_fields,$post;
	if($tern_wp_event_post) {
		$o = $getWP->getOption('tern_wp_events',$WP_event_page_defaults);
		$post = get_post($tern_wp_event_post);
		setup_postdata($post);
		//
		foreach($o['fields'] as $k => $v) {
			$args = '';
			if($WP_event_page_markup_fields[$k]['args']) {
				$args = $WP_event_page_markup_fields[$k]['args'] == 'id' ? array($post->ID) : $WP_event_page_markup_fields[$k]['args'];
			}
			//
			echo "\n";
			//
			$s = str_replace('%post_url%',get_permalink($post->ID),$v['markup']);
			$s = explode('%value%',$s);
			echo $s[0];
			//
			if(function_exists($WP_event_page_markup_fields[$k]['func'])) {
				if($args) {
					call_user_func_array($WP_event_page_markup_fields[$k]['func'],$args);
				}
				else {
					call_user_func($WP_event_page_markup_fields[$k]['func']);
				}
			}
			elseif($WP_event_page_markup_fields[$k]['func'] === false and $post->$v['name']) {
				echo $post->$v['name'];
			}
			elseif($v['field'] == '_tern_wp_event_url') {
				$m = get_post_meta($post->ID,$v['field'],true);
				echo '<a href="'.$m.'">'.$m.'</a>';
			}
			elseif(get_post_meta($post->ID,$v['field'],true)) {
				echo get_post_meta($post->ID,$v['field'],true);
			}
			echo $s[1];
		}
		return $s;
	}
}
function WP_event_page_date($i,$d=false,$f=true) {
	global $getWP,$getTIME,$WP_event_page_defaults,$post,$WP_event_page_is_list,$WP_event_page_date,$post;
	$o = $getWP->getOption('tern_wp_events',$WP_event_page_defaults);
	$p = !$i ? $post->ID : $i;
	if(!empty($d)) {
		$b = $e = $d;
	}
	else {
		$b = get_post_meta($p,'_tern_wp_event_start_date',true);
		$e = get_post_meta($p,'_tern_wp_event_end_date',true);
	}
	//
	$a = array('id'=>$p);
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
	if(empty($o['date_markup']) or !empty($d)) {
		$s .= gmdate($o['format'],$b);
		//
		if($o['show_time'] or !$WP_event_page_is_list) {
			$s .= empty($o['d_2_t_sep']) ? ' ' : $o['d_2_t_sep'];
			$s .= gmdate($o['time'],$b);
		}
		if(!empty($o['timezone'])) { $s .= ' '.$o['timezone']; }
		//
		if(($o['end_time'] or !$WP_event_page_is_list) and !$single) {
			if($getTIME->atStartStamp($b) == $getTIME->atStartStamp($e) and empty($d)) {
				$s .= empty($o['time_sep']) ? ' - ' : $o['time_sep'];
				$s .= gmdate($o['time'],$e);
				if(!empty($o['timezone'])) { $s .= ' '.$o['timezone']; }
			}
			elseif(empty($d)) {
				$s .= empty($o['date_sep']) ? ' -- ' : $o['date_sep'];
				$s .= gmdate($o['format'],$e);
				$s .= empty($o['d_2_t_sep']) ? ' ' : $o['d_2_t_sep'];
				$s .= gmdate($o['time'],$e);
				if(!empty($o['timezone'])) { $s .= ' '.$o['timezone']; }
			}
		}
	}
	else {
		$WP_event_page_date = $b;
		$s .= preg_replace_callback('/\%([a-zA-Z]+)\%/','WP_event_page_date_markup',$o['date_markup']);
		//
		if($o['show_time'] or !$WP_event_page_is_list) {
			$s .= empty($o['d_2_t_sep']) ? ' ' : $o['d_2_t_sep'];
			$s .= preg_replace_callback('/\%([a-zA-Z]+)\%/','WP_event_page_date_markup',$o['time_markup']);
		}
		if(!empty($o['timezone'])) { $s .= ' '.$o['timezone']; }
		//
		$WP_event_page_date = $e;
		if(($o['end_time'] or !$WP_event_page_is_list) and !$single) {
			if($getTIME->atStartStamp($b) == $getTIME->atStartStamp($e)) {
				$s .= empty($o['time_sep']) ? ' - ' : $o['time_sep'];
				$s .= preg_replace_callback('/\%([a-zA-Z]+)\%/','WP_event_page_date_markup',$o['time_markup']);
				if(!empty($o['timezone'])) { $s .= ' '.$o['timezone']; }
			}
			else {
				$s .= empty($o['date_sep']) ? ' -- ' : $o['date_sep'];
				$s .= preg_replace_callback('/\%([a-zA-Z]+)\%/','WP_event_page_date_markup',$o['date_markup']);
				$s .= empty($o['d_2_t_sep']) ? ' ' : $o['d_2_t_sep'];
				$s .= preg_replace_callback('/\%([a-zA-Z]+)\%/','WP_event_page_date_markup',$o['time_markup']);
				if(!empty($o['timezone'])) { $s .= ' '.$o['timezone']; }
			}
		}
	}
	if($f) {
		echo $s;
	}
	return $s;
}
function WP_event_page_date_markup($m) {
	global $WP_event_page_date;
	return gmdate($m[1],$WP_event_page_date);
}
function tern_wp_event_meta_fields($e=false) {
	WP_event_page_meta_fields($e);
}
function WP_event_page_meta_fields($e=false) {
	global $getWP,$WP_event_page_defaults,$post,$WP_event_page_fields,$WP_event_page_is_list;
	$WP_event_page_is_list = false;
	$o = $getWP->getOption('tern_wp_events',$WP_event_page_defaults);
	$p = !$p ? $post->ID : $p;
	//
	$s = '<div class="tern_wp_event_meta">';
	foreach($WP_event_page_fields as $k => $v) {
		if($v['meta']) {
			$m = get_post_meta($p,$v['name'],true);
			if(!empty($m)) {
				$s .= '<div class="tern_wp_event_meta_data"><label>'.$k.': </label>'.$m.'</div>';
			}
		}
	}
	$s .= '<div class="tern_wp_event_meta_data"><label>Start Date: </label>'.WP_event_page_date($p,get_post_meta($p,'_tern_wp_event_start_date',true),false).'</div>';
	$s .= '<div class="tern_wp_event_meta_data"><label>End Date: </label>'.WP_event_page_date($p,get_post_meta($p,'_tern_wp_event_end_date',true),false).'</div>';
	$s.= '</div>';
	if($e) {
		echo $s;
	}
	return $s;
}
function WP_event_page_has_upcoming() {
	global $getWP,$wpdb,$WP_event_page_defaults;
	$o = $getWP->getOption('tern_wp_events',$WP_event_page_defaults);
	$p = $wpdb->get_var("select ID from $wpdb->posts as p join $wpdb->postmeta as m on (p.ID = m.post_id and m.meta_key = '_tern_wp_event_start_date' and m.meta_value > ".time().") left join $wpdb->term_relationships as r on (r.object_id = p.ID) where term_taxonomy_id = ".$o['category']." order by m.meta_value asc limit 1");
	if($p) {
		return true;
	}
	return false;
}

/****************************************Terminate Script******************************************/
?>