function initEditorById(id,maxwidth,readonly,resize){	  
		tinyMCE.init({
			mode : "exact",
			//mode : "specific_textareas",
			theme : "advanced",
			theme_advanced_buttons1 : "fontselect,fontsizeselect,formatselect,bold,italic,underline,strikethrough,separator,sub,sup,separator,cut,copy,paste,undo,redo",
			theme_advanced_buttons2 : "justifyleft,justifycenter,justifyright,justifyfull,separator,numlist,bullist,outdent,indent,separator,forecolor,backcolor,separator,hr,link,unlink,image,table,code,separator,asciimath,asciimathcharmap,asciisvg",
			theme_advanced_buttons3 : "",
			theme_advanced_fonts : "Arial=arial,helvetica,sans-serif,Courier New=courier new,courier,monospace,Georgia=georgia,times new roman,times,serif,Tahoma=tahoma,arial,helvetica,sans-serif,Times=times new roman,times,serif,Verdana=verdana,arial,helvetica,sans-serif",
			theme_advanced_toolbar_location : "top",
			theme_advanced_toolbar_align : "left",
			theme_advanced_statusbar_location : "bottom",
			plugins : 'imagemanager,safari,asciimath,asciisvg,table,inlinepopups',
		   
			AScgiloc : 'http://www.imathas.com/editordemo/php/svgimg.php',			      //change me  
			ASdloc : 'http://www.imathas.com/editordemo/jscripts/tiny_mce/plugins/asciisvg/js/d.svg',  //change me  				
			/*content_css : "css/content.css",*/
			content_css : "css/content.css",
			elements: id,
 			forced_root_block : false,
 			force_br_newlines : true,
			force_p_newlines : false,
			theme_advanced_resizing : resize,
			theme_advanced_font_sizes: "10px,12px,13px,14px,16px,18px,20px,22px,24px,26px",
			theme_advanced_resizing_max_width : maxwidth,
			readonly : readonly
		});
}


function initEditorByClass(className,maxwidth,readonly,resize){
// Cái maxwidth cần xem lại để cố định 
	if(className){		
			tinyMCE.init({
			mode : "specific_textareas",
			theme : "advanced",
			theme_advanced_buttons1 : "fontselect,fontsizeselect,formatselect,bold,italic,underline,strikethrough,separator,sub,sup,separator,cut,copy,paste,undo,redo",
			theme_advanced_buttons2 : "justifyleft,justifycenter,justifyright,justifyfull,separator,numlist,bullist,outdent,indent,separator,forecolor,backcolor,separator,hr,link,unlink,image,table,code,separator,asciimath,asciimathcharmap,asciisvg",
			theme_advanced_buttons3 : "",
			theme_advanced_fonts : "Arial=arial,helvetica,sans-serif,Courier New=courier new,courier,monospace,Georgia=georgia,times new roman,times,serif,Tahoma=tahoma,arial,helvetica,sans-serif,Times=times new roman,times,serif,Verdana=verdana,arial,helvetica,sans-serif",
			theme_advanced_toolbar_location : "top",
			theme_advanced_toolbar_align : "left",
			theme_advanced_statusbar_location : "bottom",
			plugins : 'imagemanager,safari,asciimath,asciisvg,table,inlinepopups',
		   
			AScgiloc : 'http://www.imathas.com/editordemo/php/svgimg.php',			      //change me  
			ASdloc : 'http://www.imathas.com/editordemo/jscripts/tiny_mce/plugins/asciisvg/js/d.svg',  //change me  				
			/*content_css : "../media/css/content.css",*/
			content_css : "css/content.css",
 			forced_root_block : false,
 			force_br_newlines : true,
			force_p_newlines : false,
			editor_selector : className,
			theme_advanced_resizing : resize,
			theme_advanced_font_sizes: "10px,12px,13px,14px,16px,18px,20px,22px,24px,26px",
			theme_advanced_resizing_max_width : maxwidth,
			readonly : readonly
		});	
	}	
}

function initEditorByClassShotcut(className,maxwidth,readonly,resize){
// Cái maxwidth cần xem lại để cố định 
			if(className){		
					tinyMCE.init({
					
					mode :"specific_textareas",
				    editor_selector : className,
					theme : "advanced",
					plugins : "media",
					//theme_advanced_buttons1 : "bold,italic,underline,separator,strikethrough,justifyleft,justifycenter,justifyright, justifyfull,bullist,numlist,undo,redo,link,unlink,image,media,anchor,formatselect",
					theme_advanced_buttons1 : "bold,italic,underline,separator,strikethrough,justifyleft,justifycenter,justifyright, justifyfull,bullist,numlist,undo,redo,link,unlink,anchor",
					theme_advanced_buttons2 : "",
					forced_root_block : false,
					force_br_newlines : true,
					//content_css : "css/content.css",
					force_p_newlines : false,
					theme_advanced_buttons3 : "",
					theme_advanced_toolbar_location : "top",
					theme_advanced_toolbar_align : "left",
					theme_advanced_statusbar_location : "bottom",
					theme_advanced_resizing : resize,
					theme_advanced_resizing_max_width : maxwidth,
					readonly : readonly
			});	
	}	
}

function initShortEditorByClass(className,maxwidth,readonly,resize){
// Cái maxwidth cần xem lại để cố định 
	if(className){		
			tinyMCE.init({
			mode : "specific_textareas",
			theme : "advanced",
			theme_advanced_buttons1 : "bold,italic,underline,separator,strikethrough,justifyleft,justifycenter,justifyright, justifyfull,bullist,numlist,undo,redo,link,unlink,anchor",
			theme_advanced_buttons2 : "",
			theme_advanced_buttons3 : "",
			theme_advanced_fonts : "",
			theme_advanced_toolbar_location : "top",
			theme_advanced_toolbar_align : "left",
			theme_advanced_statusbar_location : "bottom",
			//AScgiloc : 'http://www.imathas.com/editordemo/php/svgimg.php',			      //change me  
			//ASdloc : 'http://www.imathas.com/editordemo/jscripts/tiny_mce/plugins/asciisvg/js/d.svg',  //change me  				
			/*content_css : "../media/css/content.css",*/
			content_css : "css/content.css",
 			forced_root_block : false,
 			force_br_newlines : true,
			force_p_newlines : false,
			editor_selector : className,
			theme_advanced_resizing : resize,
			theme_advanced_font_sizes: "10px,12px,13px,14px,16px,18px,20px,22px,24px,26px",
			theme_advanced_resizing_max_width : maxwidth,
			readonly : readonly
		});	
	}	
}
