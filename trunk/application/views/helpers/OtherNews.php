<?php
require_once APPLICATION_PATH . '/models/News.php';
require_once APPLICATION_PATH . '/models/Category.php';
require_once LIBRARY_PATH.		'/FormatDate.php';
class Zend_View_Helper_OtherNews
{
	public function otherNews($category_id)
	{
		$html = '';
		$model_news = new Default_Models_News();
		$arrObjNews = $model_news->fetchAll("`category_id`='".$category_id."'");
		$str = '';
		if(count($arrObjNews)){		
		$str.='<div class="tinkhac">';
				$str.='<p><b>Các tin khác</b></p>';		
						$str.='<ul>';					
						if(count($arrObjNews)){
							foreach($arrObjNews as $item){
								if($item->getPublish()==1){
								$str.='<li>';
									$str.='<span>';
										//$str.='<img src="'.BASE_URL.'/media/images/icongiaovien.gif"/>';
									$str.='</span>';
									$str.='<a title="'.$item->getTitle().'"  href="'.BASE_URL.'/pagestudent/viewnews/id/'.$item->getId().'">'.$item->getTitle().' (';
									$str.= Zend_View_Helper_FormatDate::convertSecondToDateTimeHasHour($item->getCreated()) ;
									$str.=' )</a>';
								$str.='</li>';		
								}						
							}
						}
						$str.='</ul>';
					$str.='</div>';
		}			
		return $str;
	}
}