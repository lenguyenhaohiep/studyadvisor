<?php
// application/views/helpers/Table.php

class Zend_View_Helper_Table {
	
	function table($name,$cols_view_title,$controller, $cols_view = array(),$cols_view_select_search = array()) {
				$html ='<div class="action-table">';
				$html.='<a href="'.BASE_URL.'/'.$controller.'/add" class="btn_add"> <img class="fugue fugue-plus-circle" alt=""	src="'.BASE_URL.'/img/icons/space.gif" /> Thêm mới </a>';
				$html.='<a href="'.BASE_URL.'/'.$controller.'/delete" class="btn_delete_select"> <img class="fugue fugue-cross-circle" alt=""	src="'.BASE_URL.'/img/icons/space.gif" /> Xóa chọn </a>';
				$html.='</div>';
				$html.='<table id="'.$name.'">';
					$html.='<thead>';
						$html.='<tr>';
						$html.='<th id="th-action" style="width:115px;">Thao tác</th>';
						foreach ($cols_view_title as $cols_view_titleItem)
							if($cols_view_titleItem != "id")
								$html.='<th>'.$cols_view_titleItem.'</th>';								
						$html.='<th id="th-checkall" style="width:10px;"><input type="checkbox" class="checkall"/></th>';
						$html.='</tr>';
					$html.='</thead>';
					$html.='<tbody>';
					$html.='</tbody>';
					$html .= '<tfoot>';
					$html .= '<tr class="search-on-table">';
					$html.='<th style="border:none;">Tìm kiếm<img class="fugue fugue-magnifier" alt="" src="'. BASE_URL .'/img/icons/space.gif"/></th>';
					foreach($cols_view_title as $key=>$cols_view_titleItem){
						if($cols_view_titleItem != "id"){
							$ok = false;
							if(!empty($cols_view[$key]))
								if(key_exists($cols_view[$key],$cols_view_select_search)){// nếu có thì là selection đây
									$html .= '<th>'.$cols_view_select_search[$cols_view[$key]].'</th>';
									$ok= true;
								}
							if($ok==false)
								$html .= '<th><input class="inline-search" type="text" name="inline-search-'.$key.'" value=""/></th>';
						}
					}					
					$html.='<th style="border:none;"></th>';
					$html.='</tr>';
					$html .= '</tfoot>';
				$html.='</table>';
				$html.='<div class="progress-action">';
				$html.='<h3>Đang xử lý </h3>';
				$html.='<div class="progress-action-processBar ui-state-highlight"></div>';
				$html.='</div>';
		return $html;
	}
}