$(document).ready(function() {
	max_width = $(".content").width();
	initEditorById("question-editor",max_width,false,true);
	initEditorByClass("editor",max_width,false,true);
});