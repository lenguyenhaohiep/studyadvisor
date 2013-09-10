<?php
require_once APPLICATION_PATH . '/models/Subject.php';
class Zend_View_Helper_SltSubject
{
	public function SltSubject($name,$value,$arraySubjectId = -1)
	{
		$obj 	= new Default_Models_Subject();
		$data = $obj->fetchAll();
		$str="<select name='$name' id='".$name."' style=' width:200px; height:23px; font-weight: bold;' >";
		$str .= "<option value='' >Chọn môn học</option>";
		if($arraySubjectId == -1 ){
			if($data)		
			foreach ($data as $rec)
			{
				$selected ="";
				if($rec->id == $value) $selected ="selected='selected'";
				$str.="<option value='".$rec->id."' $selected>".$rec->full_name."</option>";
			}
		}else{
			if($data)		
			foreach ($data as $rec)
			{
				$selected ="";
				if(in_array($rec->id,$arraySubjectId)){
					if($rec->id == $value) $selected ="selected='selected'";
					$str.="<option value='".$rec->id."' $selected>".$rec->full_name."</option>";
				}
			}
			
		}
		$str.="</select>";
		return $str;
	}
}