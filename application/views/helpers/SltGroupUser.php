<?php
require_once APPLICATION_PATH . '/models/Groupuser.php';
class Zend_View_Helper_SltGroupUser
{
	public function SltGroupUser($name,$value)
	{
		$obj 	= new Default_Models_GroupUser();
		$data = $obj->fetchAll();
		$str="<select name='$name' id='".$name."' style='color:#666600; width:200px; height:23px; font-weight: bold;' >";
		$str .= "<option value='' >Chọn nhóm người dùng</option>";
		if($data)		
		foreach ($data as $rec)
		{
			$selected ="";
			if($rec->id == $value) $selected ="selected='selected'";
			$str.="<option value='".$rec->id."' $selected>".$rec->group_name."</option>";
		}
		$str.="</select>";
		return $str;
	}
}