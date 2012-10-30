<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	protected function _initDataBase(){
		$config=new Zend_Config_Ini(APPLICATION_PATH.'/../../application.ini','development');
		$db = Zend_Db::factory($config->database);
		Zend_Db_Table_Abstract::setDefaultAdapter($db);
		Zend_Registry::set('db',$db);
	}

	protected function _initLibrairie(){
		$autoloader= Zend_Loader_Autoloader::getInstance();
		$autoloader->registerNamespace('Aeroport_');
	}

	protected function _initRouter() {
		$front = $this->bootstrap('FrontController')->getResource('FrontController');
		$router = $front->getRouter();
		$route = new Zend_Controller_Router_Route('Nouvelle_Ligne', array('controller' => 'vol', 'action' => 'ajouter-ligne'));
		$router->addRoute('Nouvelle_Ligne', $route);
	}
}

