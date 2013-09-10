<?php
class Zend_View_Helper_SltTrueFalse{
	function sltTrueFalse($name,$value) {
		$str="<select name='$name'>";
		$selectTrue="";
		if($value==1) $selectTrue = "selected ='selected' ";
		$selectFalse="";
		if($value==0) $selectFalse = "selected ='selected' ";
		$str.="<option value='0' $selectFalse>Sai</option>";
		$str.="<option value='1' $selectTrue >Đúng</option>";
		$str.="</select>";
		return $str;
	}
		
}