function before_submit(){
	// lấy giá trị của tinyMCE => node 
	var node = $("<div>"+tinyMCE.activeEditor.getContent()+"</div>");
	// Gán ans_result là đoạn html rỗng
	$("#ans_result").html("");
	// tìm thẻ có class là fillspace cho thẻ input vào đoạn html và thêm đoạn html vào ans_result
	node.find(".fillspace").each(function(){
		html = "<input type='text' name='ans[]' value='"+$(this).val()+"'>";
		$("#ans_result").append(html);			
	});	
	$("#question-form").submit();
	return false;
}
$(document).ready(function() {
	// lấy giá trị phục vụ cho edit hoặc validate có lỗi
	
	var node = $($("#question-editor").val());
	$("#ans_result").html("");
	node.find(".fillspace").each(function(){
		html = "<input type='text' name='ans[]' value='"+$(this).val()+"'>";
		$("#ans_result").append(html);			
	});
	
	max_width = $(".content").width();
	initEditorById("question-editor",max_width,false,true);
	$(".editor").each(function(){
		id = $(this).attr("id");
		initEditorById(id,max_width,false,true);		
	});		

	$("#SelectOrNonSelectCompletion").click(function(){
		var content_select = tinyMCE.activeEditor.selection.getContent({format : 'html'});
		if(content_select!=""){
			var node = $(content_select);
			if(node.attr("class")== "fillspace"){
				if(confirm("Bạn có chắc chắn muốn hủy chọn không?")){
					val = node.val();
					id  = node.attr("id");
					tinyMCE.activeEditor.selection.setContent(val);
				}			
			}else{
				var content_select = tinyMCE.activeEditor.selection.getContent({format : 'text'});
				tinyMCE.activeEditor.selection.setContent('<input type="text" name="ans[]" class="fillspace" value="'+content_select+'" style="border: 1px dotted red;" readonly="readonly" >');
			}
		}else alert("Bạn phải chọn một đoạn văn bản hoặc ít nhất là một từ.");
		
	});
});