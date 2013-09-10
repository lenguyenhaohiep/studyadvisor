<?php
class Zend_View_Helper_SltOneOrMoreTrue{
	
	function sltOneOrMoreTrue($name,$value) {
		$str="<select name='$name'>";
		$selectOneTrue="";
		if($value=='0') $selectOneTrue = "selected ='selected' ";
		$selectMoreTrue="";
		if($value=='1') $selectMoreTrue = "selected ='selected' ";
		$str.="<option value='0' $selectOneTrue>Chỉ có một phương án đúng</option>";
		$str.="<option value='1' $selectMoreTrue>Cho phép nhiều phương án trả lời</option>";
		$str.="</select>";
		return $str;
	}
		
}