<?php
class Zend_View_Helper_SltTestPagination
{
	public function SltTestPagination($name,$value)
	{
		$str="<select name='$name' id='$name' style='width:130px; height:20px; font-weight: bold;' >";
		$str .= "<option value='0' >Không giới hạn</option>";
		for($i=1 ; $i< 51; $i ++)
		{
			$selected ="";
			if($i == $value) $selected ="selected='selected'";
			$str.="<option value='$i' $selected>".$i."</option>";
		}
		$str.="</select>";
		return $str;
	}
}