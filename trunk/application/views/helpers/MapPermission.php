<?php
class Zend_View_Helper_MapPermission {
	// arrSelect = array("user"=>array("add"=>1,"edit"=>-1,"delete"=>0)
	// $arrShowMenu = array("user"=>array("add"=>1,"edit"=>-1,"delete"=>0)
	function mapPermission($name,$arrSelect=array(), $arrShowMenu) {
		$html  = '';
		$ZSN   = new Zend_Session_Namespace(NAMESPACE_MAP_APP);
		$MapPermissionApp = $ZSN->__get(NAMESPACE_MAP_APP);
		$MapApp   		  = $MapPermissionApp->getMapApp();
		if(count($MapApp)>0)
			foreach($MapApp as $MapAppItem){
				$html.='<table class="datatable table_'.$name.'" style="width:800px;">';
				$html.='<caption>'.$MapAppItem["detail"].'</caption>';
				$html.='<tr>';
				$html.='<th>Quyền</th>';
				$html.='<th style="width:100px;">Chưa xét</th>';
				$html.='<th style="width:100px;">Không cho phép</th>';
				$html.='<th style="width:100px;">Cho phép</th>';
				$html.='<th style="width:100px;">Hiển thị menu</th>';
				$html.='</tr>';
				if(count($MapAppItem["actions"])>0)
					foreach($MapAppItem["actions"] as $actionArr){
						// kiem tra xem trong arrSelect co hay ko?
						$value=0;
						if(array_key_exists($MapAppItem["name"],$arrSelect)){
							if(array_key_exists($actionArr["name"],$arrSelect[$MapAppItem["name"]])){
								$value = $arrSelect[$MapAppItem["name"]] [$actionArr["name"]];
							}
						}
						$checked1 = "";$checked2 = "";$checked3 = "";
						$classHightLight ="";
						if($value==0){ $checked1  = ' checked ="checked" '; $classHightLight="class='focus-tick-group-user'"; }
						if($value==-1){ $checked2  = ' checked ="checked" ';$classHightLight="class='focus-tick-group-user'"; }
						if($value==1){ $checked3  = ' checked ="checked" ';$classHightLight="class='focus-tick-group-user'"; }
						$dependence   = $actionArr['dependence'];
						if(!empty($dependence))
							$classDependence  = 'class="dependence-'.$dependence.' hidden"'; 
						else
							$classDependence = '';
						$html.='<tr '.$classDependence.'>';						
						$html.='<td>'.$actionArr["detail"].'<input type="hidden" name="'.$name.'[]" id="'.$name.'" value="'.$MapAppItem["name"].'-'.$actionArr["name"].'"/></td>';
						if($value==0){
							$html.='<td '.$classHightLight.'><input  type="radio" name="'.$MapAppItem["name"].'-'.$actionArr["name"].'" value="0" '.$checked1.'  onchange="UpdateChildren(0,\''.$actionArr["name"].'\');"/></td>';
							$html.='<td><input type="radio" name="'.$MapAppItem["name"].'-'.$actionArr["name"].'" value="-1" '.$checked2.' onchange="UpdateChildren(1,\''.$actionArr["name"].'\');"/></td>';
							$html.='<td><input type="radio" name="'.$MapAppItem["name"].'-'.$actionArr["name"].'" value="1"  '.$checked3.' onchange="UpdateChildren(2,\''.$actionArr["name"].'\');"/></td>';						
						}
						else if($value==-1){
							$html.='<td><input  type="radio" name="'.$MapAppItem["name"].'-'.$actionArr["name"].'" value="0" '.$checked1.'  onchange="UpdateChildren(0,\''.$actionArr["name"].'\');"/></td>';
							$html.='<td '.$classHightLight.'><input type="radio" name="'.$MapAppItem["name"].'-'.$actionArr["name"].'" value="-1" '.$checked2.' onchange="UpdateChildren(1,\''.$actionArr["name"].'\');"/></td>';
							$html.='<td><input type="radio" name="'.$MapAppItem["name"].'-'.$actionArr["name"].'" value="1"  '.$checked3.' onchange="UpdateChildren(2,\''.$actionArr["name"].'\');"/></td>';						
						}
						else if($value==1){
							$html.='<td><input  type="radio" name="'.$MapAppItem["name"].'-'.$actionArr["name"].'" value="0" '.$checked1.'  onchange="UpdateChildren(0,\''.$actionArr["name"].'\');"/></td>';
							$html.='<td><input type="radio" name="'.$MapAppItem["name"].'-'.$actionArr["name"].'" value="-1" '.$checked2.' onchange="UpdateChildren(1,\''.$actionArr["name"].'\');"/></td>';
							$html.='<td '.$classHightLight.'><input type="radio" name="'.$MapAppItem["name"].'-'.$actionArr["name"].'" value="1"  '.$checked3.' onchange="UpdateChildren(2,\''.$actionArr["name"].'\');"/></td>';						
						}
						
						// Biến xác định có cho action này hiện trên menu hay ko
						$flagshowMenu = empty($arrShowMenu[$MapAppItem["name"]] [$actionArr["name"]])?0:1;
						if($flagshowMenu==1) $selected1 = " selected='selected' ";
						else $selected1= "";
						if($flagshowMenu==0) $selected0 = " selected='selected' ";
						else $selected0= "";
						
						$html.='<td>';
					    	$html.='<select name="'.$MapAppItem["name"].'-'.$actionArr["name"].'_show_menu">';
					    		$html.='<option value="0" '.$selected0.'>Không hiển thị</option>';
					    		$html.='<option value="1" '.$selected1.'>Hiển thị</option>';
					    	$html.='</select>';
						$html.='</td>';
						
						$html.='</tr>';						
					}
				$html.='</table>';				
			}		
		return $html;
	}
	
}