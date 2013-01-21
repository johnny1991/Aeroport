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



/*
	protected function _initApplication()
	{
		$this->bootstrap('frontcontroller');
		$front = $this->getResource('frontcontroller');
		$front->addModuleDirectory(dirname(__FILE__) . '/modules');
	}*/
/*
protected function _initView(){

	$this->bootstrap('View');
	$view = $this->getResource('View');
	$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper( 'ViewRenderer');
	$viewRenderer->setView($view);
	return $view;
}*/


/*
protected function _initrouter()
{
	$frontController = Zend_Controller_Front::getInstance();
	$frontController->setControllerDirectory(array(
			'default' => APPLICATION_PATH.'/modules/controllers',
			'shop'    => APPLICATION_PATH.'/modules/controllers'
	));
	$frontController->addModuleDirectory(APPLICATION_PATH.'/modules');
 
}*/
/*
protected function _initAutoload()

{

	$defaultloader = new Zend_Application_Module_Autoloader(array(

			'namespace' => '',

			'basePath' => APPLICATION_PATH . '/modules/default/'));

		  $defaultloader->addResourceType('form', 'forms/', 'Form')
               ->addResourceType('model', 'models/', 'Model');

	$mbloader = new Zend_Application_Module_Autoloader(array(

			'namespace' => '',

			'basePath' => APPLICATION_PATH . '/modules/shop/'));

		  $mbloader->addResourceType('form', 'forms/', 'Form')
               ->addResourceType('model', 'models/', 'Model');

}*/


	protected function _initTranslate(){
		$translate=new Zend_Translate(array('adapter' => 'array', 'content' =>
				realpath(APPLICATION_PATH . '/../resources/languages'), 'locale' => 'fr', 'scan' =>
				Zend_Translate::LOCALE_DIRECTORY));
		Zend_Registry::set('Zend_Translate', $translate);
	}
	
}

