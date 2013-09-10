var oTable;
function InitJqueryDataTable(selector,custom_filter,role_user){
	TableToolsInit.sSwfPath = $("#BASE_URL").val()+"/js/jdatatable/extras/TableTools/media/swf/ZeroClipboard.swf";
	// Nếu là table question trong create test thì ta ẩn đi một số cột.
	// Đoạn if này chỉ xử lý với table table_question_in_test còn lại tất cả 
	// CÁc table cms khác thì vẫn như cũ  
	if(selector=='#table_question_in_test'){
			oTable = $(selector).dataTable( {
					"sDom": '<"H"lrfCT>t<"F"ip>', // cho phép kéo thả vị trí các cột
					"oColVis": {
						"buttonText": '<img src="'+$("#BASE_URL").val()+'/img/icons/space.gif" alt="ẩn hiện cột" class="fugue fugue-table--pencil">'
					},
					"bJQueryUI": true,
			        "sPaginationType": "full_numbers", //phân trang dạng đầy đủ
			        "oLanguage":{        	
						"sProcessing":   "",//
			        	//"sLengthMenu":   "Xem _MENU_ mục",
						"sLengthMenu": 'Xem <select><option value="20">20</option><option value="30">30</option><option value="40">40</option><option value="50">50</option><option value="100">100</option><option value="200">200</option></select> mục',        	
						//"sLengthMenu": 'Xem <select><option value="20">20</option><option value="30">30</option><option value="40">40</option><option value="50">50</option><option value="100">100</option><option value="-1">All</option></select> mục',
			        	"sZeroRecords":  "Không tìm thấy dòng nào phù hợp",
			        	"sInfo":         "Đang xem _START_ đến _END_ trong tổng số _TOTAL_ mục",
			        	"sInfoEmpty":    "Đang xem 0 đến 0 trong tổng số 0 mục",
			        	"sInfoFiltered": "(được lọc từ _MAX_ mục)",
			        	"sInfoPostFix":  "",
			        	"sSearch":       "Tìm:",
			        	"sUrl":          "",
			        	"oPaginate": {
			        		"sFirst":    "Đầu",
			        		"sPrevious": "Trước",
			        		"sNext":     "Tiếp",
			        		"sLast":     "Cuối"
			        	}
			        },
			        // Begin table question trong create test thì ta ẩn đi một số cột.
			        "aoColumnDefs": [ 
			        	{ "bVisible": false, "aTargets": [ 2 ] },
			        	{ "bVisible": false, "aTargets": [ 3 ] },
			        	{ "bVisible": false, "aTargets": [ 4 ] }
			        	],
			        // End table question trong create test thì ta ẩn đi một số cột.
			        "iDisplayLength": 20, // Number of rows to display on a single page when using pagination. 
					"bProcessing": true, // 
					"bServerSide": true, // serverside dùng ajax
					"sAjaxSource": $("#CMS_SERVERSIDE").val()+"/token/"+fnGenToken(),
					/* POST data to server */
					"fnServerData": function ( sSource, aoData, fnCallback ) {
						for(i=0;i<custom_filter.length;i++)					
							aoData.push(custom_filter[i]);
						$.ajax( {
							"dataType": 'json', 
							"type": "GET", 
							"url": sSource, 
							"data": aoData,
							"async": false, // giải quyết cơ chế bất đồng bộ, ajax không phải đợi success trả về mà vẫn thực hiện đoạn if bên dưới
							"success": fnCallback
						});
						$(".dataTables_filter").remove();
						if(selector=='#table_test_sheduleexam'){
							$(".remove-icon").remove();
							$(".action-table").remove();
							$(".edit-icon").remove();
						}
						/* Đoạn này xử lý disable một số chức năng mà user giang viên không được quyền truy cập
						*/
						if(role_user==3){
							$(".action-table").remove();
							$(".remove-icon").remove();
							$(".edit-icon").remove();
						}			
					}
				});	

	}else{
			oTable = $(selector).dataTable( {
				"sDom": '<"H"lrfCT>t<"F"ip>', // cho phép kéo thả vị trí các cột
				"oColVis": {
					"buttonText": '<img src="'+$("#BASE_URL").val()+'/img/icons/space.gif" alt="ẩn hiện cột" class="fugue fugue-table--pencil">'
				},
				"bJQueryUI": true,
		        "sPaginationType": "full_numbers", //phân trang dạng đầy đủ
		        "oLanguage":{        	
					"sProcessing":   "",//
		        	//"sLengthMenu":   "Xem _MENU_ mục",
					//"sLengthMenu": 'Xem <select><option value="20">20</option><option value="30">30</option><option value="40">40</option><option value="50">50</option><option value="100">100</option><option value="-1">All</option></select> mục',
					"sLengthMenu": 'Xem <select><option value="20">20</option><option value="30">30</option><option value="40">40</option><option value="50">50</option><option value="100">100</option><option value="200">200</option></select> mục',        	
		        	"sZeroRecords":  "Không tìm thấy dòng nào phù hợp",
		        	"sInfo":         "Đang xem _START_ đến _END_ trong tổng số _TOTAL_ mục",
		        	"sInfoEmpty":    "Đang xem 0 đến 0 trong tổng số 0 mục",
		        	"sInfoFiltered": "(được lọc từ _MAX_ mục)",
		        	"sInfoPostFix":  "",
		        	"sSearch":       "Tìm:",
		        	"sUrl":          "",
		        	"oPaginate": {
		        		"sFirst":    "Đầu",
		        		"sPrevious": "Trước",
		        		"sNext":     "Tiếp",
		        		"sLast":     "Cuối"
		        	}
		        },
		        "iDisplayLength": 20, // Number of rows to display on a single page when using pagination. 
				"bProcessing": true, // 
				"bServerSide": true, // serverside dùng ajax
				"sAjaxSource": $("#CMS_SERVERSIDE").val()+"/token/"+fnGenToken(),
				/* POST data to server */
				"fnServerData": function ( sSource, aoData, fnCallback ) {
					for(i=0;i<custom_filter.length;i++)					
						aoData.push(custom_filter[i]);
					$.ajax( {
						"dataType": 'json', 
						"type": "GET", 
						"url": sSource, 
						"data": aoData,
						"async": false, // giải quyết cơ chế bất đồng bộ, ajax không phải đợi success trả về mà vẫn thực hiện đoạn if bên dưới
						"success": fnCallback
					});
					$(".dataTables_filter").remove();
					if(selector=='#table_test_sheduleexam'){
						$(".remove-icon").remove();
						$(".action-table").remove();
						$(".edit-icon").remove();
					}
					/* Đoạn này xử lý disable một số chức năng mà user giang viên không được quyền truy cập
					*/
					
					if(selector=='#table_test' || selector=='#table_question'|| selector=='#table_test_sheduleexam' || selector=='#table_classs' ){
						var dfjdfkdf ='';
					}else	
						$(".view-icon").remove();
					
					if(role_user==3){
						$(".action-table").remove();
						$(".remove-icon").remove();
						$(".edit-icon").remove();
					}
					if(selector=='#table_groupuser'){
						$(".remove-icon").remove();
						$(".action-table").remove();
					}
								
				}
			});	
	}	
	bindActionJqTable(selector);
}

function bindActionJqTable(selector){

	$("#th-action").find("span").remove();
	$("#th-checkall").find("span").remove();
	$("#th-action").unbind("click");
	$("#th-checkall").unbind("click");
	$("#th-checkall").click(function(){});
	//$(".inline-search").keyup( function () {
    $(".inline-search").change( function () {
        oTable.fnFilter( this.value,$(this).attr("name").replace("inline-search-","")*1);        
    });
    // view modal close 
    $("#btnCloseViewDetail").click(function(){
    	 $.unblockUI();
    	 $("#view-detail-iframe").attr("src","");
    });
    // Check all
    $(".checkall").click(function(){    
    	is_check = $(this).attr("checked");
    	if(is_check == true){
    		$(selector +" tbody").find(".check_row").each(function(){    			
    			$(this).attr("checked",true);
    			checkRow($(this).attr("id"));
    			
    		});
    	}else{
    		$(selector+" tbody").find(".check_row").each(function(){
    			$(this).attr("checked",false);
    			checkRow($(this).attr("id"));
    		});
    	}
    })
    // delete select
    $('.btn_delete_select').click(function(){
    	href_delete = $(this).attr('href');
    	arrItem = new Array();
    	$(selector+" tbody").find(".check_row").each(function(){
			is_check = $(this).attr("checked");
			if(is_check==true){
				checkbox_id  = $(this).attr('id');
				arrItem.push(checkbox_id.replace("checkbox_row",""));
			}    		
		});  
    	if(arrItem.length==0)
    		alert("Chưa chọn dòng dữ liệu nào.");
    	else{
    		if(confirm("Bạn có chắc chắn muốn xóa những dòng đã chọn không?")==true){
    				var data_ = {"id[]":arrItem};
					ajaxTotalAction = arrItem.length;
    				$.ajax({
    					   type: "POST",
    					   url: href_delete,
    					   data: data_,
    					   dataType: "json",
    					   success: function(data){
    						 if(data['success']==true){
								for(ii=0;ii<arrItem.length;ii++){
				    					removeRow(arrItem[ii]);
				    					$(".checkall").attr('checked',false);
				    			}
//				    			alert("xóa thành công.");
    						 }
    						 else
    							 alert("Có lỗi "+data['error']); 
    					   }
    					 });
    		}
    	}
    	return false;
    });	
}	


function editPopup(href){
	newwindow=window.open(href,'name','scrollbars=yes,resizable=yes,height=540,width=700', 0);
	if (window.focus) {newwindow.focus()}
}

function previewTestPopup(href){
	newwindow=window.open(href,'name','scrollbars=yes,resizable=yes,height=540,width=700', 0);
	if (window.focus) {newwindow.focus()}
}

function viewClick(href){
	newwindow=window.open(href,'name','scrollbars=yes,resizable=yes,height=540,width=700', 0);
	if (window.focus) {newwindow.focus()}

}
function deleteClick(this_,href_delete){
		if(confirm("Bạn có chắc chắn muốn xóa những dòng đã chọn không?")==true){
			iddelete = $(this_).parent("td")
					.parent("tr")
					.find(".check_row")
					.attr("id").replace("checkbox_row","");
				$.ajax({
					   type: "POST",
					   url: href_delete,
					   dataType: "json",
					   success: function(data){
						 if(data['success']==true){
					  		removeRow(iddelete);
//					  		alert("xóa thành công.");
						 }
						 else
    						 alert("Có lỗi "+data['error']); 
					   }
					 });
		 }
}


function checkRow(id_checkbox){
	check_obj = $("#"+id_checkbox);
	is_check  = check_obj.attr("checked");
	if(is_check == true){ // if check
		check_obj.parent("td").parent("tr").addClass("row_selected");
		check_obj.parent("td").parent("tr").find("td").removeClass().addClass("row_selected");
	}else{  // if not check
		check_obj.parent("td").parent("tr").removeClass("row_selected");
		check_obj.parent("td").parent("tr").find("td").removeClass("row_selected");
	}	
}

function removeRow(id_checkbox){
	check_obj = $("#checkbox_row"+id_checkbox);
	check_obj.parent("td").parent("tr").remove();
}

	// hàm cập nhật các question được edit khi ta soạn test
	// dùng trong queston controller và create test 
	function fnUpdateQuestion(question_id){
		    			$.ajax({
    					   type: "POST",
    					   url: $("#BASE_URL").val()+"/question/getonequestion",
    					   data: "id="+question_id,
    					   dataType: "json",
    					   success: function(data){
    						 if(data['success']==true){
    						 	var textTemp = "test-has-question-"+question_id; 
    						 	$("span.test-has-question-"+question_id).html(data["data"]["question_title"]);
    						 	$(".scoreOneQuestion-"+question_id).val(data["data"]["question_score"]);
    						 	//question_score
								UpdateSumScoreOfTest(); // Cập nhật lại tổng điểm của bài test trên giao diện --- không save vào database
								UpdateSumQuestionOfTest();	// Cập nhật lại tổng số câu hỏi của bài test trên giao diện --- không save vào database
    						 }
    						 else
    							 alert("Có lỗi: "+data['error']); 
    					   }
    					 });
}

function getChapterObj(){
			var chapter_id = $("#chapter_id").val();
			 $.ajax({
			   type: "POST",
			   url: $("#BASE_URL").val()+"/chaptersubject/ajaxgetchaptersubjectobj"+"/token/"+fnGenToken(),
			   data: "chapter_id="+chapter_id,
			   dataType: "json",
			   success: function(data){
					if(data['success']==true){
							var str_p = '<p>'+data['chapter']['note']+'</p>';
								$(".chapterSubject_description").html(str_p);
    						 }
    						 else
    							 alert("Có lỗi: "+data['error']); 
    				}			      
			 });
			 
}



