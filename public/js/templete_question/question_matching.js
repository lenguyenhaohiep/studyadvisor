$(document).ready(function() {
	/*$(".content").css({'width' : '1000px'});*/
	$(".content").addClass("resizeContentTemp");
	$(".resizeContentTemp").css({'width' : '1200px'});
	/*max_width = $(".content").width();*/
	max_width = 150;
	initEditorById("question-editor",max_width,false,true);
	$(".editor").each(function(){
		id = $(this).attr("id");
		initEditorById(id,max_width,false,true);		
	});		
	// Add answer 
     $('span.part1-add-answer-icon').click(function() {
            var maxsize =  $("#part1-matching").find("div.answer-box").size();                                       
            var editor_id = maxsize;
            editor_id = "part1_ans_" + editor_id;            
			var html_matching ='<div class="answer-box ui-state-default">';
				html_matching+='<table>';
				html_matching+='<tr>';
				html_matching+='<td style="text-align: center;">';
				html_matching+='<label>'+((maxsize*1)+1)+'</label>';
				html_matching+='</td>';
				html_matching+='</tr>';
				html_matching+='<tr>';
				html_matching+='<td>';
				html_matching+='<input type="hidden" name="ansID[]" value="">';
				html_matching+='<input type="hidden" name="part[]"  value="A">';				
        		html_matching+='<textarea id="'+editor_id+'" class="editor" name="ans[]" cols="20" rows="2"></textarea>';
				html_matching+='</td>';
				html_matching+='</tr>';
				html_matching+='</table>';
				html_matching+='</div>';        		
        		$("#part1-matching").append(html_matching);
        		initEditorById(editor_id,100,false,true);
        });  
     $('span.part2-add-answer-icon').click(function() {
            var maxsize =  $("#part2-matching").find("div.answer-box").size();                                       
            var editor_id = maxsize;
            editor_id = "part2_ans_" + editor_id;            
			var html_matching ='<div class="answer-box ui-state-default">';
				html_matching+='<table>';
				html_matching+='<tr>';
				html_matching+='<td style="text-align: center;">';
				html_matching+='<label>'+((maxsize*1)+1)+'</label>';
				html_matching+='</td>';
				html_matching+='</tr>';
				html_matching+='<tr>';
				html_matching+='<td>';
				html_matching+='<input type="hidden" name="ansID[]" value="">';
				html_matching+='<input type="hidden" name="part[]"  value="B">';				
        		html_matching+='<textarea id="'+editor_id+'" class="editor" name="ans[]" cols="20" rows="2"></textarea>';
				html_matching+='</td>';
				html_matching+='</tr>';
				html_matching+='</table>';
				html_matching+='</div>';        		
        		$("#part2-matching").append(html_matching);
        		initEditorById(editor_id,100,false,true);
        });  
    // auto update matching collum A Selection 
    $("#sltMatchingA").focus(function(){
    	var countAnsCollumA =  $("#part1-matching").find("div.answer-box").size();// số lượng câu hỏi bên cột A
    	var i;
    	var html="";
    	for(i=1;i<=countAnsCollumA;i++){
    		html+='<option value="A'+i+'">'+i+'</option>';
    	}
    	$("#sltMatchingA").html(html);
    });
    // auto update matching collum B Selection
    $("#sltMatchingB").focus(function(){
    	var countAnsCollumB =  $("#part2-matching").find("div.answer-box").size();// số lượng câu hỏi bên cột A
    	var i;
    	var html="";
    	for(i=1;i<=countAnsCollumB;i++){
    		html+='<option value="B'+i+'">'+i+'</option>';
    	}
    	$("#sltMatchingB").html(html);
    });
    /*
    $(".remove-matching-icon").click(function(){
    	$(this).parent()
    		   .remove();    	
    });
    */
    // add couple matching
    $("span.add-matching-icon").click(function(){
    	var sltA = 	$("#sltMatchingA").val();
    	var sltB = 	$("#sltMatchingB").val();
    	var tmp  = sltA + "-" + sltB;
    	var ok  = true;
    	var ok1 = true;
    	// validate khi insert một couple matching
    	$(".couple-matching").each(function(){
    		var tmp_couple_matching = $(this).val();
    		if(tmp == tmp_couple_matching)
    			ok= false;
    		// tách cặp đúng đã có trong phần các cặp đúng , so sánh với thêm mới vào 
    		// nếu một answer đã tồn tại trong cặp đúng thì cho ok1 = false 	
    		temp_arr = tmp_couple_matching.split("-");
    		if(sltA == temp_arr[0])
    			ok1 = false;
    		if(sltB == temp_arr[1])
    			ok1 = false; 
    	});
    	if(ok1==false){
    		alert("Một câu trả lời chỉ có thể nằm trong 1 cặp đúng.");
    	}else
    	if(ok == false)
    	   alert("Cặp đúng này đã có!");
    	else{
				html='<div>';
				html+='<span onclick="remove_couple_question(this)">'; 
				html+='<img class="fugue fugue-minus-circle" alt=""	src="'+$("#BASE_URL").val()+'/img/icons/space.gif" />'; 
				html+='</span>';														
				html+='<input class="couple-matching" name="couple-matching[]" type="text" value="'+tmp+'" readonly="readonly">';
				html+='</div>';
				$("#couple-matching-correct").append(html);
    	}    		
    });
});
// delete một couple matching
function remove_couple_question(e){
	$(e).parent()
    	.remove();
	return false;
}