<?php
require_once APPLICATION_PATH . '/models/Classs.php';
class Zend_View_Helper_SltClass
{
	public function SltClass($name,$value, $courseId="default")
	{
		$obj 	= new Default_Models_Classs();
	
		if($courseId=="default")
		{
			$data = $obj->fetchAll();
		}
		else
		{
			$where = "`course_id`='".$courseId."'";
			$data = $obj->fetchAll($where);
		}		
			
		$str="<select name='$name' id='$name' style='color:#666600; width:200px; height:23px; font-weight: bold;' >";
		$str .= "<option value='' >Chọn lớp học</option>";		
		if($data)
		foreach ($data as $rec)
		{
			$selected ="";
			if($rec->id == $value) $selected ="selected='selected'";
			$str.="<option value='".$rec->id."' $selected>".$rec->full_name."</option>";
		}
		$str.="</select>";
		return $str;
	}
}