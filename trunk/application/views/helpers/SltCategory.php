<?php
require_once APPLICATION_PATH . '/models/Category.php';
class Zend_View_Helper_SltCategory
{
	public $str="";
	
	public function haschild($id,$arrobj)
	{
		foreach($arrobj as $obj)
		{
			$temp_parent_id = $obj->getParent_id();
			if($temp_parent_id==$id) return 1;			
		}
		return 0;
	}
	
	public function findchild($id,$arrobj,$selected_id,$muc)
	{
		foreach($arrobj as $obj)
		{
			$temp_id 		= $obj->getId();
			$temp_parent_id = $obj->getParent_id();
			if($temp_parent_id==$id)
			{
				$name = $obj->getName();
				$temp_str="";
				for($i=0;$i<$muc;$i++)
					 $temp_str.="---------";
				if($muc!=0)	$temp_str.=">";
				if($selected_id==$temp_id)
					$sel = " selected ='selected' ";
				else $sel="";
				if($muc==0)
					$style =" style='color:#0002A8;font-weight:bold;' ";
				elseif($muc==1)
					$style =" style='color:#0004FF;' ";	
				else
					$style =" style='color:#5154FF;' ";
				$this->str.="<option value='$temp_id' $sel $style>";
				$this->str.=$temp_str.$name;
				$this->str.="</option>";
				$this->findchild($temp_id,$arrobj,$selected_id,$muc+1);
			}
		}
	}
	
	public function sltCategory($name,$value,$flag=false)
	{

		$model_category = new Default_Models_Category();
		$arrObjCategory =  $model_category->fetchAll();
		//$arrObjCategory = $model_category->findByID(0,true);	

		$strSlt="<select name='$name' id='$name'>";
		$strSlt.="<option value='0'>-- Mục gốc --</option>";
		if($arrObjCategory!= NULL)
		{
			$this->str="";
			$this->findchild(0,$arrObjCategory,$value,0);
			$strSlt.=$this->str;
		}			
		$strSlt.="</select>";
		return $strSlt;
	}
} 