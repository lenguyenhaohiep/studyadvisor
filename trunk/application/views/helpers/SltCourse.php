<?php
require_once APPLICATION_PATH . '/models/Course.php';
class Zend_View_Helper_SltCourse
{
	/* Hàm hiện ra select của các khóa học
	 * $name: tên thẻ
	 * $value: giá trị đưa vào
	 * $ShowOrHideCourseHidden: có hiện những khóa học đã bị đóng không  
	 * $$arrCourseIdStudentJoin: Ý nghĩa: Hiện những khóa học mà student chưa tham gia. 
	 * Input: đưa vào 1 màng các khóa học mà sinh viên đó tham gia
	 */ 
	public function SltCourse($name, $value, $ShowOrHideCourseHidden=0, $arrCourseIdStudentJoin=array())
	{
		$obj 	= new Default_Models_Course();
			if($ShowOrHideCourseHidden==1)
			{
				$where = "`hidden`='on'";
				$data = $obj->fetchAll($where);
			}else
				$data = $obj->fetchAll();
			$str="<select name='$name' id='$name' style='color:#666600; width:260px; height:23px; font-weight: bold;' >";
			$str .= "<option value='0' >Chọn khóa học</option>";		
			if($data)
			foreach ($data as $rec)
			{
				// Kiểm tra xem khóa học sinh viên đã tham gia chưa
				if(!in_array($rec->id,$arrCourseIdStudentJoin))
				{
					$selected ="";
					if($rec->id == $value) $selected ="selected='selected'";
					$str.="<option value='".$rec->id."' $selected>".$rec->full_name."</option>";
				}
			}
			$str.="</select>";
		return $str;
	}
}