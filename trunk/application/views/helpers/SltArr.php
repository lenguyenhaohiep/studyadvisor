<?php
class Zend_View_Helper_SltArr {
	function sltArr($name,$value,$arr) {
		$html = "<select name='$name' id='$name'>";
		foreach($arr as $key => $item)
		{
			if($key==$value) $selected = " selected='selected' ";
			else $selected = "";
			$html .= "<option value='$key' $selected>$item</option>";

		}
		$html.="</select>";
		return $html;
	}
}