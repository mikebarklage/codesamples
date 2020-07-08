<?php
/*
Plugin Name: Plugin Annotations
Plugin URI: http://www.mikebarklage.com
Description: Add custom annotations to your plugin listings
Version: 1.0
Author: Mike Barklage
Author URI: http://www.mikebarklage.com
License: GPL v2 or later
*/

// add "annotate" link to each plugin
function add_annotate_link( $plugin_actions, $plugin_file ) {
 
    $new_actions = array();
	$new_actions['cl_settings'] = '<a href="javascript: void(0);" onClick="edit_annotation(\''.$plugin_file.'\');">Annotate</a>';
	
	// places new link after others
    return array_merge( $plugin_actions, $new_actions );
	
}

// add display and edit rows under plugin listing - may be hidden
function add_annotation_row($plugin_file, $plugin_data, $status) {
	
	// get associative array of all current annotations from DB
	$annotation_exists_onload = "true";
	$annotations = get_option("plugin-annotations", array());
	
	// new rows should use the same CSS class as their plugins
	$annotation_class = (is_plugin_active($plugin_file)) ? "active" : "inactive";
	
	// build display row
	$annotation_output = '<tr id="annotation_'.$plugin_file.'" class="'.$annotation_class.'"';
	// if no current annotation, then hide the display row
	if(!array_key_exists($plugin_file, $annotations)) {
		$annotation_exists_onload = "false";
		$annotation_output .= ' style="display: none;"';
	}
	$annotation_output .= '><td>&nbsp;</td><td colspan="2">';
	$annotation_output .= '<span class="plugin-annotations-style">'.esc_attr($annotations[$plugin_file]).'</span>';
	$annotation_output .= '</td></tr>';
	
	// build edit row - always starts hidden
	$annotation_output .= '<tr id="edit_annotation_'.$plugin_file.'" class="'.$annotation_class.'" style="display: none;">';
	$annotation_output .= '<td>&nbsp;</td><td colspan="2">';
	$annotation_output .= '<textarea id="annotation_text_'.$plugin_file.'" rows=2 cols=80>'.$annotations[$plugin_file].'</textarea><br>';
	// calls javascript in plugin-annotations.js
	$annotation_output .= '<button type="button" onClick="save_annotation(\''.$plugin_file.'\')">Save</button>';
	$annotation_output .= '<button type="button" onClick="cancel_annotation(\''.$plugin_file.'\', '.$annotation_exists_onload.')">Cancel</button>';
	$annotation_output .= '</td></tr>';
	
	echo $annotation_output;
}

// handles the AJAX call from JS to update DB
function update_annotation_db() {
	// get data from ajax call
	$plugin_file = $_REQUEST['plugin_annotated'];
	$new_note_text = trim(sanitize_textarea_field($_REQUEST['new_note_text']));
	
	// get associative array of all current annotations from DB
	$annotations = get_option("plugin-annotations", array());
	
	if (($new_note_text == "") && (array_key_exists($plugin_file, $annotations))) {
		// empty textarea means this note is being deleted - remove from array
		unset($annotations[$plugin_file]);
	}
	else {
		// add/update note - also remove the slashes WP adds to escape characters
		$annotations[$plugin_file] = wp_unslash($new_note_text);
	}
	
	// save updated array to DB
	update_option("plugin-annotations", $annotations);
	
}

// add above functions, JS, and CSS via appropriate WP hooks
add_filter( 'plugin_action_links', 'add_annotate_link', 10, 2 );
add_action( 'after_plugin_row', 'add_annotation_row', 10, 3);
// enqueues JS to footer instead of header
wp_enqueue_script('plugin-annotations-js', plugin_dir_url(__FILE__) . 'plugin-annotations.js', array(), false, true);
wp_enqueue_style('plugin-annotations-css', plugin_dir_url(__FILE__) . 'plugin-annotations.css');
add_action( 'wp_ajax_update_annotation_db', 'update_annotation_db' );


