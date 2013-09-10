<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	protected function _initHeader()
	{
		$this->bootstrap('view');
		$view = $this->getResource('view');
		$view->doctype('XHTML1_STRICT');
		$view->headMeta()->appendHttpEquiv('Content-Type', 'text/html;charset=utf-8');
		$view->headTitle()->setSeparator(' - ');
		$view->headTitle('Study Advisor - Website hỗ trợ học tập CSDL');
		$view->headLink()->appendStylesheet(BASE_URL . '/css/globals.css')
		->appendStylesheet(BASE_URL . '/css/fugue-sprite/fugue-sprite.css')
		//->appendStylesheet('http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.0/themes/smoothness/jquery-ui.css')
		->appendStylesheet(BASE_URL. '/js/jquery-ui-1.8.9.custom/css/cupertino/jquery-ui-1.8.9.custom.css');
		
		$view->addHelperPath("ZendX/JQuery/View/Helper", "ZendX_JQuery_View_Helper");
		$view->jQuery()->enable();
		//$view->jQuery()->uiEnable();
		//$view->jQuery()->setVersion('1.4.4');

		$view->jQuery()->addJavascriptFile(BASE_URL . '/js/jquery-1.4.4.min.js')
		->addJavascriptFile(BASE_URL. '/js/jquery-ui-1.8.9.custom/js/jquery-ui-1.8.9.custom.min.js');
		
		$view->headLink()->appendStylesheet(BASE_URL . '/js/jquery.countdown.package-1.5.8/jquery.countdown.css');
		$view->jQuery()->addJavascriptFile(BASE_URL . '/js/jquery.countdown.package-1.5.8/jquery.countdown.min.js');
		
		$view->jQuery()->addJavascriptFile(BASE_URL . '/js/jquery.blockUI.js');

		$view->jQuery()->addJavascriptFile(BASE_URL . '/js/main.js');
		
		/*------------------------------------------------------------*/		
		$view->headLink()->appendStylesheet(BASE_URL . '/js/jdatatable/media/css/table.css');
		$view->jQuery()->addJavascriptFile(BASE_URL  . '/js/jdatatable/media/js/jquery.dataTables.min.js');
		// plugin jqueryDatatable ColVis
		$view->headLink()->appendStylesheet(BASE_URL . '/js/jdatatable/extras/ColVis/media/css/ColVis.css');
		$view->jQuery()->addJavascriptFile(BASE_URL  . '/js/jdatatable/extras/ColVis/media/js/ColVis.min.js');
		/*------------------------------------------------------------*/
		// plugin TableTool
		$view->headLink()->appendStylesheet(BASE_URL . '/js/jdatatable/extras/TableTools/media/css/TableTools.css');
		$view->jQuery()->addJavascriptFile(BASE_URL  . '/js/jdatatable/extras/TableTools/media/js/TableTools.min.js');
		$view->jQuery()->addJavascriptFile(BASE_URL  . '/js/jdatatable/extras/TableTools/media/ZeroClipboard/ZeroClipboard.js');
		/*------------------------------------------------------------*/				

		//JQUERY UPLOADIFY
		$view->headLink()->appendStylesheet(BASE_URL . '/js/jquery.uploadify-v2.1.0/uploadify.css');
		$view->jQuery()->addJavascriptFile(BASE_URL . '/js/jquery.uploadify-v2.1.0/swfobject.js');
		$view->jQuery()->addJavascriptFile(BASE_URL . '/js/jquery.uploadify-v2.1.0/jquery.uploadify.v2.1.0.min.js');
		

		//PLUGIN ASCIIMathML+EDITOR TINYMCE
		
		$view->jQuery()->addJavascriptFile(BASE_URL . '/js/tinyMCE/jscripts/tiny_mce/plugins/asciimath/js/ASCIIMathMLwFallback.js');
		$view->jQuery()->addJavascriptFile(BASE_URL . '/js/tinyMCE/jscripts/tiny_mce/plugins/asciisvg/setVariable.js');
		$view->jQuery()->addJavascriptFile(BASE_URL . '/js/tinyMCE/jscripts/tiny_mce/tiny_mce.js');
		$view->jQuery()->addJavascriptFile(BASE_URL . '/js/tinyMCE/jscripts/tinyMCEfunc.js');
		$view->jQuery()->addJavascriptFile(BASE_URL . '/js/tinyMCE/jscripts/tiny_mce/plugins/imagemanager/js/mcimagemanager.js');
		
		//-----------------------------------------------------------		
		/* jquery tooltip
		$view->headLink()->appendStylesheet(BASE_URL. '/js/jquery-tooltip/jquery.tooltip.css');
		
		$view->jQuery()->addJavascriptFile(BASE_URL . '/js/jquery-tooltip/jquery.bgiframe.js')
		->addJavascriptFile(BASE_URL . '/js/jquery-tooltip/jquery.dimensions.js')
		->addJavascriptFile(BASE_URL . '/js/jquery-tooltip/jquery.tooltip.js');
		*/
		/* General token for IE Browser*/
		$view->jQuery()->addJavascriptFile(BASE_URL . '/js/gen_token.js');
	}

	protected function _initRegistry() {
		$db = $this->getPluginResource('db')->getDbAdapter();
		Zend_Registry::set('db', $db);
	}

	protected function _initAutoload()
	{
		$autoloader = new Zend_Application_Module_Autoloader(array(
            'namespace' => 'Default_',
            'basePath'  => dirname(__FILE__),
		));
		return $autoloader;		
	}

}