<?php
class Zend_View_Helper_SltAnswerNumbering{
	
	function sltAnswerNumbering($name,$value) {
		$str="<select  name='$name'>";
		$selectabc="";
		if($value=='abc') $selectabc = "selected ='selected' ";
		$selectABC="";
		if($value=='ABC') $selectABC = "selected ='selected' ";
		$select123="";
		if($value=='123') $select123 = "selected ='selected' ";
		$selectnone="";
		if($value=='none') $selectnone = "selected ='selected' ";
		
		$str.="<option value='abc' $selectabc> a, b, c,...</option>";
		$str.="<option value='ABC' $selectABC> A, B, C,...</option>";
		$str.="<option value='123' $select123> 1, 2, 3,...</option>";
		$str.="<option value='none' $selectnone>Không đánh số</option>";
		$str.="</select>";
		return $str;
	}
		
}