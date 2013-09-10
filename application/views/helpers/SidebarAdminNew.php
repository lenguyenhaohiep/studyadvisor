<?php
// application/views/helpers/Sidebar.php

class Zend_View_Helper_SidebarAdmin {

	function sidebarAdmin($name, $selected) {
		$name ='CÁC CHỨC NĂNG QUẢN LÝ ';
		$html = '
           <div class="sidebar ui-state-default">
              <p class="ui-state-focus" style="font-size:17px;text-align:center;">'.$name.'</p>
              <ul>';
		$flag = false;
		// Home
		if ($selected == 'home admin')
		$flag = true;
		else
		$flag = false;
		$html .= $this->_element('Trang chủ', '/', 'home', $flag);
		$auth= Zend_Auth::getInstance();
		$userhaslogin = $auth->getStorage()->read();
	    $role = $userhaslogin->group_id;
	    if($role==5 ){
			// question managerment
			if ($selected == 'question')
			$flag = true;
			else
			$flag = false;
			$html .= $this->_element('Ngân hàng câu hỏi', '/question', 'spell-check', $flag);
	    }
	    
	    if($role==5){
			// test managerment
			if ($selected == 'test')
			$flag = true;
			else
			$flag = false;
			$html .= $this->_element('Đề thi', '/test', 'document--pencil', $flag);
	    }
	    if($role==5){
			// test managerment
			if ($selected == 'autogeneratetest')
			$flag = true;
			else
			$flag = false;
			$html .= $this->_element('Phát sinh đề thi', '/test/autogeneratetest', 'property-blue', $flag);
	    }
	    
	    if($role==5){
			// Test managerment
			if ($selected == 'exam')
			$flag = true;
			else
			$flag = false;
			$html .= $this->_element('Kỳ thi', '/exam', 'address-book-open', $flag);
	    }
	    if($role==5){
			// sheduleexam managerment
			if ($selected == 'sheduleexam')
			$flag = true;
			else
			$flag = false;
			$html .= $this->_element('Lịch thi', '/sheduleexam', 'address-book-open', $flag);
	    }
		if($role==5){ // là người quản lý mới có quyền quản lý thành viên
			// User managerment
			if ($selected == 'user')
			$flag = true;
			else
			$flag = false;
			$html .= $this->_element('Sinh viên', '/user', 'user', $flag);
	    }
	    if($role==5){
			// subject managerment
			if ($selected == 'subject')
			$flag = true;
			else
			$flag = false;
			$html .= $this->_element('Môn học', '/subject', 'address-book--pencil', $flag);
	    }
	    
	    if($role==5){
			// chaptersubject managerment
			if ($selected == 'chaptersubject')
			$flag = true;
			else
			$flag = false;
			$html .= $this->_element('Chủ đề môn học', '/chaptersubject', 'address-book--pencil', $flag);
	    }
	    if($role==5){
			// classhasstudent managerment
			if ($selected == 'classhasstudent')
			$flag = true;
			else
			$flag = false;
			$html .= $this->_element('Sinh viên trong lớp', '/classhasstudent', 'user--arrow', $flag);
	    }
	    if($role==5){
			// class managerment
			if ($selected == 'class')
			$flag = true;
			else
			$flag = false;
			$html .= $this->_element('Lớp học', '/classs', 'door-open-in', $flag);
	    }
	    if($role==5){
			// course managerment
			if ($selected == 'course')
			$flag = true;
			else
			$flag = false;
			$html .= $this->_element('Khóa học', '/course', 'door-open-in', $flag);
	    }
	    if($role==5){
			// groupuser managerment
			if ($selected == 'groupuser')
			$flag = true;
			else
			$flag = false;
			$html .= $this->_element('Nhóm người dùng', '/groupuser', 'users--arrow', $flag);
	    }
		$html .=  '</ul></div>';
		return $html;
	}

	private function _element($name, $url, $image, $selected) {
		$return = '<li><a href="' . BASE_URL . $url . '"' ;
		if ($selected)
		$return .= ' class="selected"';
		$return .= '>';
		$return .= '<img src="' . BASE_URL . '/img/icons/space.gif" alt=""';
		$return .= ' class="fugue fugue-' . $image . '"/>';
		$return .= $name;
		if ($selected)
		$return .= '<img class="tab" alt="" src="'. BASE_URL . '/img/tab.png"/>';
		$return .= '</a></li>';

		return $return;
	}
}

