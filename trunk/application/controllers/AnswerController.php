<?php

require_once APPLICATION_PATH . '/models/Answer.php';
require_once APPLICATION_PATH . '/models/ProcessXML/questionXML.php';
require_once LIBRARY_PATH.		'/lib_xml.php';
class AnswerController extends Zend_Controller_Action
{
	private $_answer;
	
	public function init()
	{
		$this->_answer 	 = new Default_Models_Answer();
		$this->view->jQuery()->addJavascriptFile(BASE_URL . '/js/jquery.datatables.js');
		$this->view->headLink()->appendStylesheet(BASE_URL . '/css/table/table.css');
	}
	
	public function indexAction()
	{
		try {
			$request = $this->getRequest();
			if ($request->isPost()) {
				$data = $request->getParams();
			}elseif($request->isGet()) {
				$subject_filter = $request->getParam("subject_filter",0);
				$type_filter    = $request->getParam("type_filter",0);
				$cbSearch       = $request->getParam("cbSearch",0);
				
				if(!empty($cbSearch))
				$cbSearch=1;
				$this->view->cbSearch = $cbSearch;

				$this->view->subject_filter = $subject_filter;
				$this->view->type_filter    = $type_filter;
				$where="";
				$where .= (!empty($type_filter))?" `type`='$type_filter' AND ":"";
				
				$where .="1";
				
				$rows = $this->_question->fetchAll($where);
			
				$data = array();
				if(count($rows)>0)
				foreach ($rows as $row) {
					$part = array();
					$part['Nội dung']  			= $row->getQuestiontext();
					$part['id']  				= $row->getId();
					$part['Phan hoi chung']   	= $row->getGeneralfeedback();
					$part['Subject ID']   		= $row->getSubject_id();
					$part['Cha']  		   		= $row->getContent();
				
					$data[] = $part;				
				}				
				$this->view->data = $data;
				die();
			}
		}catch(Exception $ex){
			echo $ex->getMessage();  

		}
	}
	
	
}