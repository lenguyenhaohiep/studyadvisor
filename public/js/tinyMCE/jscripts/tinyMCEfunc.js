function initEditorById(id,maxwidth,readonly,resize){	  
		tinyMCE.init({
			mode : "exact",
			//mode : "specific_textareas",
			theme : "advanced",
			theme_advanced_buttons1 : "fontselect,fontsizeselect,formatselect,bold,italic,underline,strikethrough,separator,sub,sup,separator,cut,copy,paste,undo,redo",
			theme_advanced_buttons2 : "justifyleft,justifycenter,justifyright,justifyfull,separator,numlist,bullist,outdent,indent,separator,forecolor,backcolor,separator,hr,link,unlink,image,table,code,separator,asciimath,asciimathcharmap,asciisvg",
			theme_advanced_buttons3 : "pastetext,pasteword,selectall",
			theme_advanced_fonts : "Arial=arial,helvetica,sans-serif,Courier New=courier new,courier,monospace,Georgia=georgia,times new roman,times,serif,Tahoma=tahoma,arial,helvetica,sans-serif,Times=times new roman,times,serif,Verdana=verdana,arial,helvetica,sans-serif",
			theme_advanced_toolbar_location : "top",
			theme_advanced_toolbar_align : "left",
			theme_advanced_statusbar_location : "bottom",
			plugins : 'paste,imagemanager,safari,asciimath,asciisvg,table,inlinepopups',
		   
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
			paste_preprocess : function(pl, o) {
	            // Content string containing the HTML from the clipboard
	            //alert(o.content);
	            //o.content = "-: CLEANED :-\n" + o.content;
	            o.content = strip_tags( o.content,'<b><u><i>' );
	            //alert(o.content);
        	},
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
			plugins : 'paste,imagemanager,safari,asciimath,asciisvg,table,inlinepopups',
		   
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
			paste_preprocess : function(pl, o) {
	            o.content = strip_tags( o.content,'<b><u><i>' );
        	},			
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
					
					//theme_advanced_buttons1 : "bold,italic,underline,separator,strikethrough,justifyleft,justifycenter,justifyright, justifyfull,bullist,numlist,undo,redo,link,unlink,image,media,anchor,formatselect",
					theme_advanced_buttons1 : "bold,italic,underline,separator,strikethrough,justifyleft,justifycenter,justifyright, justifyfull,bullist,numlist,undo,redo,link,unlink,anchor,code",
					theme_advanced_buttons2 : "",
					forced_root_block : false,
					force_br_newlines : true,
					//content_css : "css/content.css",
					force_p_newlines : false,
					theme_advanced_buttons3 : "",
					theme_advanced_toolbar_location : "top",
					theme_advanced_toolbar_align : "left",
					theme_advanced_statusbar_location : "bottom",
					plugins : "paste",
					theme_advanced_resizing : resize,
					theme_advanced_resizing_max_width : maxwidth,
					paste_preprocess : function(pl, o) {
			            o.content = strip_tags( o.content,'<b><u><i>' );
		        	},			
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
			plugins : "paste",
			theme_advanced_buttons1 : "bold,italic,underline,separator,strikethrough,justifyleft,justifycenter,justifyright, justifyfull,bullist,numlist,undo,redo,link,unlink,anchor,code",
			theme_advanced_buttons2 : "",
			theme_advanced_buttons3 : "",
			theme_advanced_fonts : "",
			theme_advanced_toolbar_location : "top",
			theme_advanced_toolbar_align : "left",
			theme_advanced_statusbar_location : "bottom",
			content_css : "css/content.css",
 			forced_root_block : false,
 			force_br_newlines : true,
			force_p_newlines : false,
			editor_selector : className,
			theme_advanced_resizing : resize,
			theme_advanced_font_sizes: "10px,12px,13px,14px,16px,18px,20px,22px,24px,26px",
			theme_advanced_resizing_max_width : maxwidth,
			paste_preprocess : function(pl, o) {
	            o.content = strip_tags( o.content,'<b><u><i>' );
        	},			
			readonly : readonly
		});	
	}	
}

function initShortEditorByClassTestOneQues(className,maxwidth,readonly,resize){
// Cái maxwidth cần xem lại để cố định 
	if(className){		
			tinyMCE.init({
			mode : "specific_textareas",
			theme : "advanced",
			plugins : "paste",
			theme_advanced_buttons1 : "bold,italic,underline,separator,strikethrough,justifyleft,justifycenter,justifyright, justifyfull,bullist,numlist,undo,redo,link,unlink,anchor,code",
			theme_advanced_buttons2 : "",
			theme_advanced_buttons3 : "",
			theme_advanced_fonts : "",
			theme_advanced_toolbar_location : "top",
			theme_advanced_toolbar_align : "left",
			theme_advanced_statusbar_location : "bottom",
			content_css : "css/content.css",
 			forced_root_block : false,
 			force_br_newlines : true,
			force_p_newlines : false,
			editor_selector : className,
			theme_advanced_resizing : resize,
			theme_advanced_font_sizes: "10px,12px,13px,14px,16px,18px,20px,22px,24px,26px",
			theme_advanced_resizing_max_width : maxwidth,
			paste_preprocess : function(pl, o) {
	            o.content = strip_tags( o.content,'<b><u><i>' );
        	},			
			readonly : readonly
		});	
	}	
}


/*
paste_preprocess : function(pl, o) {
  //example: keep bold,italic,underline and paragraphs
  //o.content = strip_tags( o.content,'<b><u><i><p>' );

  // remove all tags => plain text
  o.content = strip_tags( o.content,'' );
}
*/
// Strips HTML and PHP tags from a string 
// returns 1: 'Kevin <b>van</b> <i>Zonneveld</i>'
// example 2: strip_tags('<p>Kevin <img src="someimage.png" onmouseover="someFunction()">van <i>Zonneveld</i></p>', '<p>');
// returns 2: '<p>Kevin van Zonneveld</p>'
// example 3: strip_tags("<a href='http://kevin.vanzonneveld.net'>Kevin van Zonneveld</a>", "<a>");
// returns 3: '<a href='http://kevin.vanzonneveld.net'>Kevin van Zonneveld</a>'
// example 4: strip_tags('1 < 5 5 > 1');
// returns 4: '1 < 5 5 > 1'
function strip_tags (str, allowed_tags)
{

    var key = '', allowed = false;
    var matches = [];    var allowed_array = [];
    var allowed_tag = '';
    var i = 0;
    var k = '';
    var html = ''; 
    var replacer = function (search, replace, str) {
        return str.split(search).join(replace);
    };
    // Build allowes tags associative array
    if (allowed_tags) {
        allowed_array = allowed_tags.match(/([a-zA-Z0-9]+)/gi);
    }
    str += '';

    // Match tags
    matches = str.match(/(<\/?[\S][^>]*>)/gi);
    // Go through all HTML tags
    for (key in matches) {
        if (isNaN(key)) {
                // IE7 Hack
            continue;
        }

        // Save HTML tag
        html = matches[key].toString();
        // Is tag not in allowed list? Remove from str!
        allowed = false;

        // Go through all allowed tags
        for (k in allowed_array) {            // Init
            allowed_tag = allowed_array[k];
            i = -1;

            if (i != 0) { i = html.toLowerCase().indexOf('<'+allowed_tag+'>');}
            if (i != 0) { i = html.toLowerCase().indexOf('<'+allowed_tag+' ');}
            if (i != 0) { i = html.toLowerCase().indexOf('</'+allowed_tag)   ;}

            // Determine
            if (i == 0) {                allowed = true;
                break;
            }
        }
        if (!allowed) {
            str = replacer(html, "", str); // Custom replace. No regexing
        }
    }
    return str;
}
