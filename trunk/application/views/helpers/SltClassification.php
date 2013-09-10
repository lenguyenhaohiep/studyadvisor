<?php
class Zend_View_Helper_SltClassification
{
	public function SltClassification($name,$value)
	{
		 
		$value = trim($value);
		$asscci_code  = 'DCBA';
		$asscci_code  = array("0"=>"Kém","1"=>"Trung bình","2"=>"Khá tốt","3"=>"Rất tốt");
		if(0<=$value && $value<0.2)
			$tem = 1;
		else if(0.2<=$value && $value<0.3)
			$tem  = 2;
		else if(0.3<=$value && $value<0.4)
			$tem  = 3;
		else if(0.4<=$value)
			$tem  = 4;	
		
		$str="<select name='$name' id='$name' style='color:#666600; width:120px; height:23px; font-weight: bold;' >";
		$str .= "<option value='' >Lựa chọn</option>";		
			for($i=0;$i<4;$i++){
				$selected ="";
				if(($i+1) == $tem) $selected ="selected='selected'";
				$str.="<option value='".(($i+1)*0.1)."' $selected>".$asscci_code[$i]."</option>";
			}
		$str.="</select>";
		return $str;
	}
}