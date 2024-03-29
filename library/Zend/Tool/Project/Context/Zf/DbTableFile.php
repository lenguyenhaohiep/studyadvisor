<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Tool
 * @subpackage Framework
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: DbTableFile.php 18951 2009-11-12 16:26:19Z alexander $
 */

/**
 * @see Zend_Tool_Project_Context_Filesystem_File
 */
require_once 'Zend/Tool/Project/Context/Filesystem/File.php';

/**
 * This class is the front most class for utilizing Zend_Tool_Project
 *
 * A profile is a hierarchical set of resources that keep track of
 * items within a specific project.
 *
 * @category   Zend
 * @package    Zend_Tool
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Tool_Project_Context_Zf_DbTableFile extends Zend_Tool_Project_Context_Filesystem_File
{

	/**
	 * getName()
	 *
	 * @return string
	 */
	public function getName()
	{
		return 'DbTableFile';
	}

	/*
	 protected $_dbTableName;

	 public function getPersistentAttributes()
	 {
	 return array('dbTableName' => $this->_dbTableName);
	 }

	 public function setDbTableName($dbTableName)
	 {
	 $this->_dbTableName = $dbTableName;
	 $this->_filesystemName = $dbTableName . '.php';
	 }

	 public function getDbTableName()
	 {
	 return $this->_dbTableName;
	 }
	 */

}
