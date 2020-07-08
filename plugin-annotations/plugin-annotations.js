// makes annotation textarea visible
function edit_annotation(annotation_id) {
	document.getElementById("annotation_"+annotation_id).style.display = "none";
	document.getElementById("edit_annotation_"+annotation_id).style.display = "";
}

// saves new/updated annotation via WP ajax script - handled by PHP function update_annotation_db()
function save_annotation(annotation_id) {
	var new_note_text = document.getElementById("annotation_text_"+annotation_id).value;
	
	jQuery.ajax({
		url: ajaxurl,
		data: { 'action': 'update_annotation_db', 
				'plugin_annotated': annotation_id,
				'new_note_text': new_note_text },
		success:function(response) {
			// reload the page to show the new annotation
			location.reload();
		},
		error:function(errorThrown) {
			console.log(errorThrown);
		}
	});
}

// cancels out of textarea, returns to either previous or no annotation
function cancel_annotation(annotation_id, note_exists) {
	document.getElementById("edit_annotation_"+annotation_id).style.display = "none";
	if (note_exists) {
		document.getElementById("annotation_"+annotation_id).style.display = "";
	}
	else {
		document.getElementById("annotation_"+annotation_id).style.display = "none";
	}
}
