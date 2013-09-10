$(document).ready(function() {
	$("#btnCloseDialogAddNewQuestion").click(function(){
		$.unblockUI();
	});
	$(".btn_add").click(function(){
		$.blockUI({ 
			message: $('#add-question-dialog-container'),
			overlayCSS:  { 
    			backgroundColor: "#808080", 
    			opacity:         0.6
			}
		  });	
		return false;
	});
	$('#add-question-dialog-container').find("button").click(function(){
		window.location = $(this).attr("name");
	});
});