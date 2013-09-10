$(document).ready(function() {
	max_width = $(".content").width();
	initEditorById("question-editor",max_width,false,true);
	$(".editor").each(function(){
		id = $(this).attr("id");
		initEditorById(id,max_width,false,true);		
	});		
	// Add answer 
     $('span.add-answer-icon').click(function() {
            var max_id = -1;
            $("textarea[name='feedback[]']").each(function(){
            	id= $(this).attr('id');
            	id= id.replace("feedback_","");
            	if( max_id < 1*id )
            		max_id= id*1;
            });            
            var editor_id = max_id + 1;
            editor_id = "feedback_" + editor_id;      
                  
        		var html_multi_choise = '<div class="answer-box ui-state-default">';
        		html_multi_choise+='<table style="border: 3px solid orange">';
				html_multi_choise+='<tr><td>';
				html_multi_choise+='<p>';
				html_multi_choise+='<label>Điểm cho câu trả lời này &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
				html_multi_choise+='</label>'+$("#sltPerscoreHidden").html();
				html_multi_choise+='</p>';
				html_multi_choise+='</td></tr>';

        		html_multi_choise+='<tr>';       		
        		html_multi_choise+='<td>';
        		html_multi_choise+='<input type="hidden" name="ansID[]" value="">';
        			
        		html_multi_choise+='<td style="text-align: right;">';
        		html_multi_choise+='<span id="delete_'+editor_id+'" class="delete-answer-icon" onclick="bindAnswerDelete($(this).attr(\'id\'));"> <b><font color="red">Xóa câu trả lời</font></b>'; 
        		html_multi_choise+='<img class="fugue fugue-cross-circle" alt=""	src="'+$("#BASE_URL").val()+'/img/icons/space.gif" />';
        		html_multi_choise+='</span>';
        		html_multi_choise+='</td>';
        		html_multi_choise+='</tr>';
        		html_multi_choise+='<tr>';
        		html_multi_choise+='<td colspan="2">';
        		//html_multi_choise+='<textarea id="'+editor_id+'" class="editor" name="ans[]" cols="20" rows="2"></textarea>';
        		html_multi_choise+='<input type="text" name="ans[]" value="" size="70">';
        		html_multi_choise+='</td>';
        		html_multi_choise+='</tr>';
        		html_multi_choise+='<td colspan="2">';
        		html_multi_choise+='<label>Phản hồi cho câu trả lời</label>';
        		//html_multi_choise+='<textarea  name="feedback[]" rows="3" cols="20"></textarea>';
        		html_multi_choise+='<textarea  class="editor"  id="'+editor_id+'" name="feedback[]" rows="3" cols="20"></textarea>';
        		html_multi_choise+='</td>';
        		html_multi_choise+='</tr>';
        		
        		html_multi_choise+='</table>';  

        		
        		html_multi_choise+='</div>';
        		
        		box = $(html_multi_choise);
        		
        		box.insertAfter($("div.answer-box:last"));
        		initEditorById(editor_id,500,false,true);
        		fnOrderBoxAnswer();
        });  
});

// all function of 
function bindAnswerDelete(delete_id) {
        if ($('div.answer-box').size() > 2) {
            if (confirm('Bạn có thật sự muốn xóa không?'))
            	$('.answer-box').each(function(){ // tìm trong thẻ answer-box cái nào có chứa cái thẻ span delete_ans_ thì xóa đi
            		var ok = $(this).find("span[id='"+delete_id+"']").size();
            		if(ok>0)
            			$(this).remove();
            	});
            	fnOrderBoxAnswer();
        }
        else
            alert("Không thể xóa thêm nữa.");
}

function fnOrderBoxAnswer(){

	var index= 0;
	$('div.answer-box').each(function(){
		index++;
		var objNode = $(this).find("table:first");
		if(objNode.find(".orderAnsBox").size()==0)
			objNode.prepend("<tr><td class='orderAnsBox'>Đáp án "+index+"</td></tr>");
		else
			objNode.find(".orderAnsBox").html("Đáp án "+index);
	});
	
}