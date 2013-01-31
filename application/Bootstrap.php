<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

	protected function _initDataBase(){
		$config = new Zend_Config_Ini(APPLICATION_PATH.'/../../application.ini','development');
		$db = Zend_Db::factory($config->database);
		Zend_Db_Table_Abstract::setDefaultAdapter($db);
		Zend_Registry::set('db',$db);
		Zend_Registry::set('config',$config); // Mettre la config de application.ini dans le registre
	}

	protected function _initTranslate(){
		$translate=new Zend_Translate(array('adapter' => 'array', 'content' =>
				realpath(APPLICATION_PATH . '/../resources/languages'), 'locale' => 'fr', 'scan' =>
				Zend_Translate::LOCALE_DIRECTORY));
		Zend_Registry::set('Zend_Translate', $translate);
	}
	
}

