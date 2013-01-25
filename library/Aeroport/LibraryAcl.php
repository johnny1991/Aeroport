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
		
		$this->addRole(new Zend_Acl_Role(array('1','2','3','4','5','6','7')),'NOT_LOGGED');
		$this->addRole(new Zend_Acl_Role('LOGGED',array('1','2','3','4','5','6','7')));
		$this->addRole(new Zend_Acl_Role('admin'),'LOGGED');
		
		
		$this->addResource('index');
		$this->addResource('vol');
		$this->addResource('drh');
		$this->addResource('maintenance');
		$this->addResource('planning');
		
		
		
		// index
		$this->allow(null, 'index');
		// ajouter-employe
		// drh
		$this->allow('4', 'drh', array('index','consulter-employe', 'change-disponibilite', 'prolonge-brevet', 'consulter-service', 'ajouter-employe', 'modifier-employe', 'supprimer-employe', 'change-ville', 'get-brevet', 'ajouter-service', 'modifier-service', 'supprimer-service', 'get-typeavion'));
		
		// maintenance
		$this->allow('3', 'maintenance', array('index','consulter-avion','ajouter-avion','modifier-avion', 'fiche-avion-maintenance','fiche-avion','ajouter-maintenance','supprimer-avion','ajouter-modele-avion','modifier-modele-avion','supprimer-modele-avion','consulter-modele-avion','fiche-modele-avion','supprimer-maintenance','planning','planning-jour','consulter-maintenance'));
		
		// strategique
		
		$this->allow('1', 'vol', array('index','ajouter-ligne', 'consulter-ligne', 'consulter-vol', 'fiche-vol', 'modifier-ligne', 'rechercher-adresse', 'rechercher-aeroport', 'supprimer-ligne'));
		
		$this->allow('5', 'planning', array('index','planifier-vol','liste-vol','recherchepilote','planifier-astreinte','planning-liste','fiche-astreinte'));

		
		
		
		
	}
}