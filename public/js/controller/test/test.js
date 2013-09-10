function send_result(question_id){
	// get data từ bên trong thẻ div có id là "question-"+question_id
	// sau đó đẩy ajax lên server và chấm điểm
	// cắt khoảng trắng của id câu hỏi(nếu có)
	question_id = $.trim(question_id); 
	var data 			= "question_id="+question_id;
	var answer_of_user  = "";
	// câu hỏi dạng điền khuyết
	$("#question-"+question_id).find('.fillspace')
							   .each(function(){
								   answer_of_user+="&answer_of_user[]="+$(this).val();
							   });
	// các câu hỏi còn lại
	$(".class-input-question-"+question_id).each(function(){
		switch(this.nodeName){
			case "INPUT": // checkbox, radio ,textbox
				if($(this).attr("type")=="radio" || $(this).attr("type")=="checkbox"){
					if($(this).attr("checked")==true)
						answer_of_user+="&answer_of_user[]="+$(this).val();
				}
				if($(this).attr("type")=="text")
					answer_of_user+="&answer_of_user[]="+$(this).val();
			break;
			case "SELECT": //select
				answer_of_user+="&answer_of_user[]="+$(this).val();
				break;
			case "TEXTAREA": // textarea
				answer_of_user+="&answer_of_user[]="+tinyMCE.activeEditor.getContent();				
				break;
		}
	});
	data= data + answer_of_user; 
	if($("#testID").val()+"" != "undefined")
		data+= "&testID="+$("#testID").val();
	
	// Xử lý giữ đoạn text "Câu 1" khi send ajax chấm điểm 1 câu hỏi 
	$("#question-title-"+question_id).find("span").remove();
	var question_title_order  = $("#question-title-"+question_id).text();	
	data+= "&question_title_order="+question_title_order;	

	$("#question-"+question_id).block({
		message: '<span><b>Đang xử lý...</b></span>',                 
        overlayCSS:  {  
            '-webkit-border-radius': '3px', 
            '-moz-border-radius':    '3px',	
            'padding-bottom': '3px',
            backgroundColor: '#fff'
        }
	});

	$.ajax({
		   type: "POST",
		   url: $("#BASE_URL").val()+'/test/testonequestion'+'/token/'+fnGenToken(),
		   data: data,
		   success: function(msg){
				$("#question-"+question_id).html("");
				$("#question-"+question_id).replaceWith(msg);
				$("#question-"+question_id).unblock();	
				
				//  Begin Xử lý ký tự toán học khi SEndResult() bị mất 
				AMtranslated = false;								
				translate("AM");
				// End Xử lý ký tự toán học khi SEndResult() bị mất
				//initEditorByClass("editor",500,false, false);
				//initEditorByClass("editor_readonly",500,true, false);
		   }
		 });	
		 
		 
	return true;
}
$(document).ready(function() {
	//initEditorByClass("editor",500,false, false);
	//initEditorByClass("editor_readonly",500,true, false);
});