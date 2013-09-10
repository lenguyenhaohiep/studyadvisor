<?php
// application/views/helpers/Error.php

class Zend_View_Helper_Error {
	function error($errors = null) {
		if (is_null($errors) || !is_array($errors))
		return '';
		$html = '<div class="error"><b>Thông báo lỗi:</b>';
		$html .= '<ul>';
		foreach ($errors as $error) {
			$html .= '<li>' . $error .'</li>';
		}
		$html .= '</ul></div>';
		return $html;
	}
}

