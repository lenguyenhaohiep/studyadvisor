<?php
class Zend_View_Helper_SltTypeQuestion
{
	public function SltTypeQuestion($name,$value)
	{
		$arrTypeQuestion = array(
								1=>"Đúng sai",
								2=>"Nhiều lựa chọn",
								3=>"Ghép cặp đôi",
								4=>"Điền khuyết",
								5=>"Tự luận",
								6=>"Trả lời ngắn",
								);
		$str="<select name='$name' id='$name' style='color:#666600; width:200px; height:23px; font-weight: bold;' >";
		$str .= "<option value='' >Chọn loại câu hỏi</option>";		
		foreach ($arrTypeQuestion as $key=>$rec)
		{
				$selected ="";
				if($key == $value) $selected ="selected='selected'";
				$str.="<option value='".$key."' $selected>".$rec."</option>";
		}
		$str.="</select>";
		return $str;
	}
}