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
		/** Test de tout les modèles */
		/*
		$tableAeroport = new Aeroport;
		$tableAstreinte = new Astreinte;
		$tableAvion = new Avion;
		$tableEtreBreveter = new EtreBreveter;
		$tableIntervention = new Intervention;
		$tableJourSemaine = new JourSemaine;
		$tablePays = new Pays;
		$tablePeriodiciteJourSemaine = new PeriodiciteJourSemaine;
		$tablePeriodicite = new Periodicite;
		$tablePilote = new Pilote;
		$tableRemarque = new Remarque;
		$tableService = new Service;
		$tableTypeAvion = new TypeAvion;
		$tableUtilisateur = new Utilisateur;
		$tableVille = new Ville;
		$tableVol = new Vol;
		*/
	}
	
	public function loginAction()
	{
		
	}
}


