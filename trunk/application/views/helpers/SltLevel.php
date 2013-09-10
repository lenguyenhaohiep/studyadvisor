<?php
class Zend_View_Helper_SltLevel
{
	public function SltLevel($name,$value)
	{
		$str="<select name='$name' id='$name' style='color:#666600; width:120px; height:23px; font-weight: bold;' >";
		$array = array("0"=>1,"1"=>0.9,"2"=>0.8,"3"=>0.7,"4"=>0.6,"5"=>0.5,"6"=>0.4,"7"=>0.3,"8"=>0.2,"9"=>0.1);
			if(empty($value))
                            $value=-1;
//                        $tem=0;
				
//                        if(0<=$value && $value<=0.1)
//                                $tem = 10;
//                        if(0.1<=$value && $value<0.2)
//                                $tem = 9;
//                        if(0.2<=$value && $value<0.3)
//                                $tem = 8;
//                        if(0.3<=$value && $value<0.4)
//                                $tem = 7;
//                        if(0.4<=$value && $value<0.5)
//                                $tem = 6;
//                        if(0.5<=$value && $value<0.6)
//                                $tem = 5;
//                        if(0.6<=$value && $value<0.7)
//                                $tem = 4;
//                        if(0.7<=$value && $value<0.8)
//                                $tem = 3;
//                        if(0.8<=$value && $value<0.9)
//                                $tem = 2;
//                        if(0.9<=$value && $value<1)
//                                $tem = 1;
				
		$str .= "<option value='' >Lựa chọn</option>";		
			for($i=0;$i<10;$i++){
				$selected ="";
				if($array[$i] == $value) $selected ="selected='selected'";
				$str.="<option value='".$array[$i]."' $selected>".($i+1)."</option>";
				
			}
		$str.="</select>";
		return $str;
	}
}