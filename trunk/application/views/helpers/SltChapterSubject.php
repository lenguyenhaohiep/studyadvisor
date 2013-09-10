<?php
require_once APPLICATION_PATH . '/models/Chaptersubject.php';
class Zend_View_Helper_SltChapterSubject
{
	public function SltChapterSubject($name,$value,$subject_id=null)
	{
		$obj 	= new Default_Models_ChapterSubject();
		if($subject_id!=null)
			$data = $obj->fetchAll('`subject_id`='.$subject_id);							
		else 
			$data = null;			
		$str="<select name='$name' onchange='getChapterObj(); return false'  id='".$name."' style='width:200px; height:23px; font-weight: bold;' >";
		$str .= "<option value='' >Chọn chủ đề</option>";
		if($data)		
			foreach ($data as $rec)
			{
				$selected ="";
				if($rec->id == $value) $selected ="selected='selected'";
				$str.="<option value='".$rec->id."' $selected>".$rec->name."</option>";
			}
		$str.="</select>";
		return $str;
	}
}