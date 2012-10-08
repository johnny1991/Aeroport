<?php

class IndexController extends Zend_Controller_Action
{

	public function init()
	{
		/* Initialize action controller here */
	}

	public function indexAction()
	{
		echo phpinfo();
		//Test de connexion a la bdd et aux modeles
		//$tableAeroport=new Aeroport;
		//$aeroport=$tableAeroport->fetchrow();
	}


}

