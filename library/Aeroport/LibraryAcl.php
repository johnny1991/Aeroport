<?php
class Aeroport_LibraryAcl extends Zend_Acl
{
	public function __construct()
	{
		
		$this->addRole(new Zend_Acl_Role('LOGGED'));
		$this->addRole(new Zend_Acl_Role('NOT_LOGGED'));
		
		$this->addRole(new Zend_Acl_Role('1'),'LOGGED');
		$this->addRole(new Zend_Acl_Role('2'),'LOGGED');
		$this->addRole(new Zend_Acl_Role('3'),'LOGGED');
		$this->addRole(new Zend_Acl_Role('4'),'LOGGED');
		$this->addRole(new Zend_Acl_Role('5'),'LOGGED');
		$this->addRole(new Zend_Acl_Role('6'),'LOGGED');
		$this->addRole(new Zend_Acl_Role('7'),'LOGGED');
		$this->addRole(new Zend_Acl_Role('8'),'LOGGED');
				
		$this->addRole(new Zend_Acl_Role('ADMIN'),'LOGGED');
		$this->addRole(new Zend_Acl_Role('MEMBER'),'NOT_LOGGED');
		
		
		$this->addResource('index');
		$this->addResource('vol');
		$this->addResource('drh');
		$this->addResource('maintenance');
		$this->addResource('planning');
		$this->addResource('pilote');
		$this->addResource('logistique');
		$this->addResource('exploitation');
		
		/* MAGASIN WEB */
		$this->addResource('Shop/administration');
		$this->addResource('Shop/client');
		$this->addResource('Shop/commande');
		$this->addResource('Shop/index');
		$this->addResource('Shop/produit');
		$this->addResource('Shop/sidedar');
		/* FIN MAGASIN WEB */
		
		// shop
		//$this->addResource('commande');
		
		
		// index
		$this->allow(null, 'index');
		
		// strategique
		$this->allow('1', 'vol', array('index','ajouter-ligne', 'consulter-ligne', 'consulter-vol', 'fiche-vol', 'modifier-ligne', 'rechercher-adresse', 'rechercher-aeroport', 'supprimer-ligne'));
		
		// commercial
		//$this->allow('2', 'commande', 'liste');
		
		// maintenance
		$this->allow('3', 'maintenance', array('intervention','index','consulter-avion','ajouter-avion','modifier-avion', 'fiche-avion-maintenance','fiche-avion','ajouter-maintenance','supprimer-avion','ajouter-modele-avion','modifier-modele-avion','supprimer-modele-avion','consulter-modele-avion','fiche-modele-avion','supprimer-maintenance','planning','planning-jour','consulter-maintenance'));
		// drh
		$this->allow('4', 'drh', array('index','consulter-employe', 'change-disponibilite', 'prolonge-brevet', 'consulter-service', 'ajouter-employe', 'modifier-employe', 'supprimer-employe', 'change-ville', 'get-brevet', 'ajouter-service', 'modifier-service', 'supprimer-service', 'get-typeavion'));

		// planning
		$this->allow('5', 'planning', array('index','planifier-vol','liste-vol','recherchepilote','planifier-astreinte','planning-liste','fiche-astreinte'));
		$this->allow('LOGGED', 'vol', array('fiche-vol'));
		echo $this->isAllowed('2', 'vol','fiche-vol') ? 'autorisé' : 'refusé';
		
		//exploitation
		$this->allow('6', 'exploitation', array('index', 'fiche-vol', 'rechercher-aeroport', 'rechercher-ville'));
		
		//logistique
		$this->allow('7', 'logistique', array('index', 'ajouter-remarque', 'consulter-remarque', 'fiche-vol', 'modifier-remarque', 'supprimer-remarque', 'traiter-all', 'traiter-remarque', 'traiter-type'));
		
		//pilote
		$this->allow('8', 'pilote', array('index'));
		//$this->allow('8', 'vol', array('fiche-vol'));
		$this->allow('8', 'planning', array('fiche-astreinte'));
		
		$this->allow(NULL, 'vol', 'rechercher-aeroport');
		
		
		
		
		// Administration
		$this->allow('2','Shop/administration'); // Autorise accès au controller administration pour l'administration
		//$this->allow('NOT_LOGGED','Shop/administration','login'); // Autorise accès à la page login de l'administration pour Visiteurs et Membres
		$this->deny('NOT_LOGGED','Shop/administration',null); // Aucun accès aux pages administration pour Visiteurs et Membres
			
		//Client
		$this->allow('MEMBER','Shop/client'); // Autorise accès au controller administration pour l'administrateur
		$this->deny('MEMBER','Shop/client',array('liste','ajout-admin')); // Aucun accès aux pages clients pour Visiteurs et Admin
		$this->allow('NOT_LOGGED','Shop/client',array('tableau-bord','ajout','panier')); // Autorise accès à la page login du client pour Visiteurs et Membres
		$this->allow('2','Shop/client',array('liste','ajout-admin','delete')); // Aucun accès aux pages clients pour Visiteurs et Admin
		
		
		//Commande
		$this->allow('2','Shop/commande');// Autorise accès au controller conmmande pour l'administrateur
		$this->deny('NOT_LOGGED','Shop/commande'); // Aucun accès aux pages commandes pour Visiteurs et Membres
		
		//index
		$this->allow('NOT_LOGGED','Shop/index'); // Tous les droits à tout le monde
		
		//produit
		$this->deny('NOT_LOGGED','Shop/produit', array('ajout','delete','update','liste','affichage-sous-categorie'));
		$this->allow('NOT_LOGGED','Shop/produit');// Autorise accès au controller produit pour tout le monde
		$this->allow('2', 'Shop/produit',array('fiche','catalogue','ajout','delete','update','liste','affichage-sous-categorie'));// Autorise accès au controller produit pour tout le monde
		
		
		
	}
}
