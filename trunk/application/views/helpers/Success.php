<?php

class Zend_View_Helper_Success {
	function Success() {
		$html = '<div class="success">';
		$html .= '<ul>';
			$html .= '<li > Lưu lại thành công </li>';
		$html .= '</ul></div>';
		return $html;
	}
}

