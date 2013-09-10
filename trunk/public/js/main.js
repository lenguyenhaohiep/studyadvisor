
$(document).ready(function() {
	initShortEditorByClass("short-editor",400,false,true);
});

	// Kiểm tra giá trị điểm nhập vào phải là kiểu số 
	function validateNum(this_)
	{
		var value = $(this_).val();
		if(isNaN(value))
			$(this_).val("0");
		else
			$(this_).val(value*1); 
	}
	
$(function(){
		$('#datepicker1').datepicker({
				 changeMonth: true,
				 changeYear : true,
				 dateFormat:"dd-mm-yy",
				 gotoCurrent: true,
				 dayNamesMin: ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'],
				 monthNamesShort: ['Tháng 1','Tháng 2','Tháng 3','Tháng 4','Tháng 5','Tháng 6','Tháng 7','Tháng 8','Tháng 9','Tháng 10','Tháng 11','Tháng 12']
	
		});

		$('#datepicker2').datepicker({
				 changeMonth: true,
				 changeYear : true,
				 dateFormat:"dd-mm-yy",
				 gotoCurrent: true,
				 dayNamesMin: ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'],
				 monthNamesShort: ['Tháng 1','Tháng 2','Tháng 3','Tháng 4','Tháng 5','Tháng 6','Tháng 7','Tháng 8','Tháng 9','Tháng 10','Tháng 11','Tháng 12']
	
		});

		$('#datepicker3').datepicker({
				 changeMonth: true,
				 changeYear : true,
				 dateFormat:"dd-mm-yy",
				 gotoCurrent: true,
				 dayNamesMin: ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'],
				 monthNamesShort: ['Tháng 1','Tháng 2','Tháng 3','Tháng 4','Tháng 5','Tháng 6','Tháng 7','Tháng 8','Tháng 9','Tháng 10','Tháng 11','Tháng 12']
	
		});

		$('#datepicker4').datepicker({
				 changeMonth: true,
				 changeYear : true,
				 dateFormat:"dd-mm-yy",
				 gotoCurrent: true,
				 dayNamesMin: ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'],
				 monthNamesShort: ['Tháng 1','Tháng 2','Tháng 3','Tháng 4','Tháng 5','Tháng 6','Tháng 7','Tháng 8','Tháng 9','Tháng 10','Tháng 11','Tháng 12']
	
		});



});

