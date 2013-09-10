<?php
class Zend_View_Helper_SltAttemptAllow
{
	public function SltAttemptAllow($name,$value)
	{
		$str="<select name='$name' id='SltAttemptAllow' style='color:#666600; width:130px; height:20px; font-weight: bold;' >";
		$str .= "<option value='0' >Không giới hạn</option>";
		for($i=1 ; $i< 11; $i ++)
		{
			$selected ="";
			if($i == $value) $selected ="selected='selected'";
			$str.="<option value='$i' $selected>".$i."</option>";
		}
		$str.="</select>";
		return $str;
	}
}