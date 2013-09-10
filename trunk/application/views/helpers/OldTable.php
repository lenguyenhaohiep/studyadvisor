<?php
// application/views/helpers/Table.php

class Zend_View_Helper_Table {
	function table($rows, $controller, $view = "") {
		$html = '<table class="data-table display">';

		if (is_null($rows) || !is_array($rows) || count($rows) == 0) {
			return '<p  class="ui-state-default" style="font-size:10pt;text-align:center;margin-top:40px;">Không thấy dữ liệu trong cơ sở dữ liệu</p>';
		}
		$keys = array_keys($rows[0]);
		// thead
		$html .= '<thead><tr>';
		foreach ($keys as $key) {
			if ($key != 'id')
			$html .= '<th>'. $key .'</th>';
		}
		$html .= '<td class="ui-state-default thead-action">Thao tác</td>';
		$html .= '<td class="ui-state-default"><input type="checkbox" id="check-all"/></td>';
		$html .= '</tr></thead>';
		// tbody
		$html .= '<tbody>';
		foreach ($rows as $row) {
			$html .= '<tr id="row'.$row['id'].'">';
			foreach($keys as $key) {
				if ($key != 'id')
				$html .= '<td>'. $row[$key] .'</td>';
			}
			$html .= '<td class="action-row">';
			if(!empty($view)){
				$html .= '<a class="view-icon" name="'.$view.'" href="'. BASE_URL .'/'. $controller .'/view/id/'. $row['id'] .'">
                	<img class="fugue fugue-eye" alt="" src="'. BASE_URL .'/img/icons/space.gif"/></a>';
			}

			$html .= '<a class="edit-icon" href="'. BASE_URL .'/'. $controller .'/edit/id/'. $row['id'] .'">
                <img class="fugue fugue-pencil" alt="" src="'. BASE_URL .'/img/icons/space.gif"/></a>';
			$html .= '<a class="remove-icon" href="'.BASE_URL.'/'.$controller.'/remove" name="delete_row_'.$row['id'].'">
                <img class="fugue fugue-cross" alt="" src="'. BASE_URL .'/img/icons/space.gif"/></a>';
			$html .='</td>';
			$html .= '<td><input type="checkbox" name="items'. $row['id'] .'" value="'. $row['id'] .'"/></td>';
			$html .= '</tr>';
		}
		$html .= '</tbody>';

		// tfoot
		$html .= '<tfoot>';
		$html .= '<tr class="search-on-table">';
		foreach($keys as $key) {
			if ($key != 'id')
			$html .= '<th><input class="inline-search" type="text"
                        name="search-'. strtolower($key) .'" value="" /></th>';
		}
		$html .= '<td><img class="fugue fugue-magnifier" alt="" src="'. BASE_URL .'/img/icons/space.gif"/></td><td></td>';

		$html .= '</tfoot>';

		$html .= '</table>';

		return $html;
	}
}