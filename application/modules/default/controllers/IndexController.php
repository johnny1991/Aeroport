<?php

class IndexController extends Zend_Controller_Action
{
	public function init()
	{
		/* Initialize action controller here */
	}

	public function indexAction()
	{

		$this->_helper->layout->setLayout('home');
		$this->view->title="INSSET Airlines - Connexion";
	}

	public function loginAction()
	{

	}
}
  

