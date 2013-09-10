<?php
class Zend_View_Helper_SltPerScore{
	
	function sltPerScore($name,$value) {
		$str="<select name='$name'>";
		for( $i = 100; $i >= -100; ){
			if ($i == -105)
				break ;
			$select = "selected ='selected' ";
				
			if($value == $i)
			{
				if($i == 0)
					$str.="<option value='$i' $select > None </option>";
				else
					$str.="<option value='$i' $select>$i %</option>";
			}
			elseif( $i == 0 )
				$str.="<option value='$i'> None </option>";
			else
				$str.="<option value='$i' >$i %</option>";
			$i -= 5;
		}
		$str.="</select>";
		return $str;
	}
}