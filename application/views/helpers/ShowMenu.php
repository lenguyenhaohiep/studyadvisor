<?php
require_once APPLICATION_PATH . '/models/News.php';
require_once APPLICATION_PATH . '/models/Category.php';
require_once LIBRARY_PATH.		'/FormatDate.php';

class Zend_View_Helper_ShowMenu
{
	public $str="";	
	public function haschild($id,$arrobj)
	{
		foreach($arrobj as $obj)
			if($obj->getParent_id() == $id) return 1;			
		return 0;
	}
	
	public function findchild($id,$arrobj,$selected_id)
	{
		foreach($arrobj as $obj)
		{
			$temp_id 		= $obj->getId();
			$temp_parent_id = $obj->getParent_id();
			if($temp_parent_id!=0){
				if($temp_parent_id==$id)
				{
						if($obj->getHidden()!= 1 ){
							$name = $obj->getName();
							if(!$this->haschild($temp_id,$arrobj)==1)
								$this->str.="<li><a href='".BASE_URL."/index/viewnewsgroup/id/".$obj->getId()."'>$obj->getName()</a>";
							else
								$this->str.="<li><a href='#'>$obj->getName()</a>";
							if($this->haschild($temp_id,$arrobj)==1)
							{
								$this->str.="<ul>";
								$this->findchild($temp_id,$arrobj,$selected_id);
								$this->str.="</ul>";
							}
							$this->str.="</li>";				
					}
				}
			}else
			{
				if($temp_parent_id==$id)
				{
						if($obj->getHidden()!= 1 ){
							$name = $obj->getName();
							if(!$this->haschild($temp_id,$arrobj)==1)
								$this->str.='<h3><a href="#">Thông báo</a></h3>';
								//$this->str.="<li><a href='".BASE_URL."/index/viewnewsgroup/id/".$obj->getId()."'>$obj->getName()</a>";
							else
								//$this->str.="<li><a href='#'>$obj->getName()</a>";
								$this->str.='<h3><a href="#">Thông báo</a></h3>';
							if($this->haschild($temp_id,$arrobj)==1)
							{
								$this->str.="<ul>";
								$this->findchild($temp_id,$arrobj,$selected_id);
								$this->str.="</ul>";
							}
							//$this->str.="</li>";				
					}
				}
			}
		}
	}
	
	public function showMenu()
	{
		$html = '';
		$model_category = new Default_Models_Category();
		$arrObjCategory = $model_category->fetchAll(null,"order");
		if(count($arrObjCategory)>0){
			foreach($arrObjCategory as $key=>$arrObjCategoryItem){
				//$arrObjCategoryItem = new Default_Models_Category();
				if($arrObjCategoryItem->getHidden()!=0){
					$html .= '<h3><a href="#" class="flag-to-focus-menu">'.$arrObjCategoryItem->getName().'</a></h3>';
					$html .= '<div style="margin: 0px;padding: 0px;">';
						$html .= '<ul class="student_NavSubItem">';
						$modelNews = new Default_Models_News();
						$resultNews = $modelNews->fetchAll("`category_id`='".$arrObjCategoryItem->getId()."' ");
						if(count($resultNews)>0)
							foreach($resultNews as $key=>$resultNewsItem){
								if($resultNewsItem->getPublish()==1){
									if($key==5){ 
										$html .= '<li><a class="subMenuLeft" title="Xem thêm"  href="'.BASE_URL.'/pagestudent/viewnewsall/">Xem thêm...</a></li>';
									break;}
									$string = $resultNewsItem->getTitle();
									$token = strtok($string, " ");
									$i = 0;
									while ($i != 3)
									 {
										  $token .=" ".strtok(" ");
										  $i++;
									 }
									$html .= '<li><a class="subMenuLeft" title="'.$resultNewsItem->getTitle().'"  href="'.BASE_URL.'/pagestudent/viewnews/id/'.$resultNewsItem->getId().' ">'.$token.'...</a></li>';
								}
							}
						$html .= '</ul>';
					$html .= '</div>';
				}
			}
		}
		return $html;
	}	
}