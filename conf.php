<?php
////////////////////////////////////////////////////////////////////////////////////////////////////
//
//		File:
//			conf.php
//		Description:
//			This file configures the Wordpress Plugin - Event Page
//		Actions:
//			1) initialize pertinent variables
//			2) load classes and functions
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
//________________________________** INITIALIZE VARIABLES      **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
$WP_event_page_defaults = array(
	'show_time'		=>	1,
	'end_time'		=>	0,
	'format'		=>	'l F j, Y',
	'time'			=>	'g:ia',
	'date_markup'	=>	'<small>%l% %F% <span>%j%</span>, %Y%</small>',
	'time_markup'	=>	'<span>%g%:%i%%a%</span>',
	'timezone'		=>	'',
	
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
			'field'		=>	'post_title',
			'markup'	=>	'<div class="tern_wp_event_post_title"><h3><a href="%post_url%">%value%</a></h3></div>'
		),
		'Event Date'	=>	array(
			'field'		=>	'event_date',
			'markup'	=>	'<div class="tern_wp_event_date">%value%</div>'
		),
		'Post Excerpt'	=>	array(
			'field'		=>	'post_excerpt',
			'markup'	=>	'<div class="tern_wp_event_post_excerpt">%value%</div>'
		)
	)
);
$WP_event_page_fields = array(
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
$WP_event_page_markup_fields = array(
	'Post Title'	=>	array(
		'field'	=>	'post_title',
		'func'	=>	'the_title',
		'args'	=>	false
	),
	'Event Date'	=>	array(
		'field'	=>	'event_date',
		'func'	=>	'WP_event_page_date',
		'args'	=>	'id'
	),
	'Post Content'	=>	array(
		'field'	=>	'post_content',
		'func'	=>	'the_content',
		'args'	=>	array('read more...')
	),
	'Post Excerpt'	=>	array(
		'field'	=>	'post_excerpt',
		'func'	=>	'the_excerpt',
		'args'	=>	array('read more...')
	),
	'Post Author'	=>	array(
		'field'	=>	'post_author',
		'func'	=>	'the_author',
		'args'	=>	false
	),
	'Post Status'	=>	array(
		'field'	=>	'post_status',
		'func'	=>	false
	),
	'Comment Count'	=>	array(
		'field'	=>	'comment_count',
		'func'	=>	'comments_number',
		'args'	=>	false
	)
);

$WP_event_page_date_formats = array(
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
$WP_event_page_is_list = false;
$tern_wp_event_post = NULL;
//                                *******************************                                 //
//________________________________** FILE CLASS                **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
require_once(dirname(__FILE__).'/class/file.php');
$getFILE = new fileClass;
//                                *******************************                                 //
//________________________________** LOAD CLASSES              **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
$l = $getFILE->directoryList(array(
	'dir'	=>	dirname(__FILE__).'/class/',
	'rec'	=>	true,
	'flat'	=>	true,
	'depth'	=>	1
));
if(is_array($l)) {
	foreach($l as $k => $v) {
		require_once($v);
	}
}
//                                *******************************                                 //
//________________________________** INITIALIZE INCLUDES       **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
$l = $getFILE->directoryList(array(
	'dir'	=>	dirname(__FILE__).'/core/',
	'rec'	=>	true,
	'flat'	=>	true,
	'depth'	=>	1
));
if(is_array($l)) {
	foreach($l as $k => $v) {
		require_once($v);
	}
}

/****************************************Terminate Script******************************************/
?>