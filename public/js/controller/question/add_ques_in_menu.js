$(document).ready(function() {
	$("#btnCloseDialogAddNewQuestion1").click(function(){
		$.unblockUI();
	});
	$(".menu_btn_add").click(function(){
		$.blockUI({ 
			message: $('#menu-index-add-question-dialog-container'),
			overlayCSS:  { 
    			backgroundColor: "#808080", 
    			opacity:         0.6
			}
		  });	
		return false;
	});
	$('#menu-index-add-question-dialog-container').find("button").click(function(){
		window.location = $(this).attr("name");
	});
});