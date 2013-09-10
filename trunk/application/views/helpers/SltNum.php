<?php
class Zend_View_Helper_SltNum
{
	public function sltNum($name,$value,$start,$end,$step)
	{
		$str = "<select name='$name'>";
		for($i=$start;$i<=$end;$i+=$step){
				if($i == $value)	
					$select = " selected='selected' ";
				else $select="";
			$str.="<option value='$i' $select>$i</option>";
		}
		$str.="</select>";
		return $str;
	}
}