<?php 
class My_String {
	
    public static function _Lower($str){
    	$lower = 'a|b|c|d|e|f|g|h|i|j|k|l|m|n|o|p|q|r|s|t|u|v|w|x|y|z|á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ|đ|é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ|í|ì|ỉ|ĩ|ị|ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ|ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự|ý|ỳ|ỷ|ỹ|ỵ';
    	$upper = 'A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z|Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ|Đ|É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ|Í|Ì|Ỉ|Ĩ|Ị|Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ|Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự|Ý|Ỳ|Ỷ|Ỹ|Ỵ';
        $arrayUpper = explode('|', preg_replace("/\n|\t|\r/","", $upper));
        $arrayLower = explode('|', preg_replace("/\n|\t|\r/","", $lower));
    	return str_replace($arrayUpper,$arrayLower,$str);
    }
    
    public static function _Upper($str){
    	$lower = 'a|b|c|d|e|f|g|h|i|j|k|l|m|n|o|p|q|r|s|t|u|v|w|x|y|z|á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ|đ|é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ|í|ì|ỉ|ĩ|ị|ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ|ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự|ý|ỳ|ỷ|ỹ|ỵ';
    	$upper = 'A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z|Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ|Đ|É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ|Í|Ì|Ỉ|Ĩ|Ị|Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ|Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự|Ý|Ỳ|Ỷ|Ỹ|Ỵ';
        $arrayUpper = explode('|', preg_replace("/\n|\t|\r/","", $upper));
        $arrayLower = explode('|', preg_replace("/\n|\t|\r/","", $lower));
    	return str_replace($arrayLower,$arrayUpper,$str);
	}
	
	public static function _trim_space($str){
		// Cắt hết khoảng trắng chuỗi và mỗi từ chỉ cách nhau 1 khoảng trắng
		$str = trim($str);
		while(strpos($str,"  ")!== false){			
			$pos  = strpos($str,"  ");			
			$str = substr($str,0,$pos) . substr($str,$pos+1);
		}
		return $str;
	}
	
	public static  function  _compare_string($str1,$str2){
		// So sánh chuỗi sau khi đã cắt khoảng trắng và chuyển thành chữ hoa
		
		$str1= My_String::_trim_space($str1);
		$str1= My_String::_Upper($str1); 
		$str2= My_String::_trim_space($str2);
		$str2= My_String::_Upper($str2);
		if($str1 == $str2)
		 return 1;
		else return 0;
	}
	
	public static function _tinyMce_get_absolute_path_image($_questionHtml){
		return str_replace("","",$_questionHtml);		
	}
} 
