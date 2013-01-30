<?php
class Aeroport_LibraryAcl extends Zend_Acl
{
	public function __construct()
	{
		
		$this->addRole(new Zend_Acl_Role('NOT_LOGGED'));
		$this->addRole(new Zend_Acl_Role('1'));
		$this->addRole(new Zend_Acl_Role('2'));
		$this->addRole(new Zend_Acl_Role('3'));
		$this->addRole(new Zend_Acl_Role('4'));
		$this->addRole(new Zend_Acl_Role('5'));
		$this->addRole(new Zend_Acl_Role('6'));
		$this->addRole(new Zend_Acl_Role('7'));
		$this->addRole(new Zend_Acl_Role('8'));
		
		$this->addRole(new Zend_Acl_Role(array('1','2','3','4','5','6','7','8')),'NOT_LOGGED');
		$this->addRole(new Zend_Acl_Role('LOGGED',array('1','2','3','4','5','6','7','8')));
		$this->addRole(new Zend_Acl_Role('ADMIN'),'LOGGED');
		$this->addRole(new Zend_Acl_Role('CLIENT'),'NOT_LOGGED');
		
		
		$this->addResource('index');
		$this->addResource('vol');
		$this->addResource('drh');
		$this->addResource('maintenance');
		$this->addResource('planning');
		$this->addResource('pilote');
		$this->addResource('logistique');
		$this->addResource('exploitation');
		
		// shop
		$this->addResource('commande');
		
		
		// index
		$this->allow(null, 'index');
		
		// strategique
		$this->allow('1', 'vol', array('index','ajouter-ligne', 'consulter-ligne', 'consulter-vol', 'fiche-vol', 'modifier-ligne', 'rechercher-adresse', 'rechercher-aeroport', 'supprimer-ligne'));
		
		// commercial
		$this->allow('2', 'commande', 'liste');
		
		// maintenance
		$this->allow('3', 'maintenance', array('index','consulter-avion','ajouter-avion','modifier-avion', 'fiche-avion-maintenance','fiche-avion','ajouter-maintenance','supprimer-avion','ajouter-modele-avion','modifier-modele-avion','supprimer-modele-avion','consulter-modele-avion','fiche-modele-avion','supprimer-maintenance','planning','planning-jour','consulter-maintenance'));
		
		// drh
		$this->allow('4', 'drh', array('index','consulter-employe', 'change-disponibilite', 'prolonge-brevet', 'consulter-service', 'ajouter-employe', 'modifier-employe', 'supprimer-employe', 'change-ville', 'get-brevet', 'ajouter-service', 'modifier-service', 'supprimer-service', 'get-typeavion'));

		// planning
		$this->allow('5', 'planning', array('index','planifier-vol','liste-vol','recherchepilote','planifier-astreinte','planning-liste','fiche-astreinte'));
		$this->allow('5', 'vol', array('fiche-vol'));
		
		//exploitation
		$this->allow('6', 'exploitation', array('index', 'fiche-vol', 'rechercher-aeroport', 'rechercher-ville'));
		
		//logistique
		$this->allow('7', 'logistique', array('index', 'ajouter-remarque', 'consulter-remarque', 'fiche-vol', 'modifier-remarque', 'supprimer-remarque', 'traiter-all', 'traiter-remarque', 'traiter-type'));
		
		//pilote
		$this->allow('8', 'pilote', array('index'));
		$this->allow('8', 'vol', array('fiche-vol'));
		$this->allow('8', 'planning', array('fiche-astreinte'));
		
		$this->allow(array('1','2','3','4','5','6','7'), 'vol', 'rechercher-aeroport');
		
	}
}
