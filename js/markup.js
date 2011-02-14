/**************************************************************************************************/
/*
/*		File:
/*			admin.js
/*		Description:
/*			This file contains Javascript for the administrative aspects of the plugin.
/*		Date:
/*			Added on February 14th 2011
/*		Copyright:
/*			Copyright (c) 2011 Matthew Praetzel.
/*		License:
/*			License:
/*			This software is licensed under the terms of the GNU Lesser General Public License v3
/*			as published by the Free Software Foundation. You should have received a copy of of
/*			the GNU Lesser General Public License along with this software. In the event that you
/*			have not, please visit: http://www.gnu.org/licenses/gpl-3.0.txt
/*
/**************************************************************************************************/

/*-----------------------
	Initialize
-----------------------*/
jQuery(document).ready(function() {
	jQuery('#event_list_fields').tableDnD({
		onDrop : function () {
			jQuery('#fields tr:even').addClass('alternate');
			jQuery('#fields tr:odd').removeClass('alternate');
			tern_event_submitForm();
		}
	});
});
/*-----------------------
	Forms
-----------------------*/
function tern_event_submitForm() {
	var p = tern_event_getFormPost('tern_wp_event_list_fm');
	jQuery.ajax({
		async : false,
		type : 'POST',
		url : tern_wp_root+'/wp-admin/admin.php',
		dataType : 'text',
		data : p,
		success : function (m) {
			jQuery('#tern_wp_message').html(m);
		},
		error : function () {
			jQuery('#tern_wp_message').html('There was an error while processing your request. Please try again.');
		}
	});
	jQuery('#tern_event_sample_markup').load(tern_wp_root+'/wp-admin/admin.php','page=tern-wp-event-page-mark-up&action=getmarkup',function () {});
}
function tern_event_editField(i) {
	var p = document.getElementById(i);
	var n = jQuery('#'+i+' .tern_event_fields').toggleClass('hidden');
	var o = jQuery('#'+i+' .tern_event_edit');
	o.html = o.html() == 'Edit' ? 'Quit Editing' : 'Edit';
}
function tern_event_renderField(i) {
	var a = ['field_titles','field_markups'];
	jQuery('#'+i+' .tern_event_fields').each(function() {
		var n = this.name ? this.name.replace('%5B%5D','') : '';
		for(k in a) {
			if(this.name && n == a[k]) {
				jQuery('#'+i+' .'+n).text(this.value);
				break;
			}
		}
	});
	tern_event_submitForm();
	tern_event_editField(i);
}
function tern_event_getFormPost(f) {
	var f = document.getElementById(f),e = f.elements,p = '',v;
	for(var i=0;i<e.length;i++) {
		if(e[i].name) {
			if((this.tern_event_inputType(e[i]) == 'radio' || this.tern_event_inputType(e[i]) == 'checkbox') && !e[i].checked) {
				continue;
			}
			v = e[i].name + '=' + escape(e[i].value);
			p += p.length > 0 ? '&' + v : v;
		}
	}
	return p;
}
function tern_event_inputType(i) {
	if(i && i.type) { return i.type; }
	return '';
}