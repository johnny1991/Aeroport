<?php
class Application_Acl_Acl extends Zend_Acl
{
	public function __construct()
	{
		/*
		$this->addRole(new Zend_Acl_Role('1'));
		$this->addRole(new Zend_Acl_Role('2'));
		$this->addRole(new Zend_Acl_Role('3'));
		$this->addRole(new Zend_Acl_Role('4'));
		$this->addRole(new Zend_Acl_Role('5'));
		$this->addRole(new Zend_Acl_Role('6'));
		$this->addRole(new Zend_Acl_Role('7'));
				
		$this->addRole(new Zend_Acl_Role('Visitor'));
		$this->addRole(new Zend_Acl_Role('Member'), 'Visitor');
		$this->addRole(new Zend_Acl_Role(array('1','2','3','4','5','6','7')), 'Visitor');
		$this->addRole(new Zend_Acl_Role('Admin', array('1','2','3','4','5','6','7')));
		
		
		*/
		
		$this->addRole(new Zend_Acl_Role('NOT_LOGGED'));
		$this->addRole(new Zend_Acl_Role('1'));
		$this->addRole(new Zend_Acl_Role('2'));
		$this->addRole(new Zend_Acl_Role('3'));
		$this->addRole(new Zend_Acl_Role('4'));
		$this->addRole(new Zend_Acl_Role('5'));
		$this->addRole(new Zend_Acl_Role('6'));
		$this->addRole(new Zend_Acl_Role('7'));
		$this->addRole(new Zend_Acl_Role('Member'), 'NOT_LOGGED');
		
		$this->addRole(new Zend_Acl_Role(array('1','2','3','4','5','6','7')),'NOT_LOGGED');
		$this->addRole(new Zend_Acl_Role('LOGGED',array('1','2','3','4','5','6','7')));
		
		$this->addResource('administration');
		$this->addResource('categorie');
		$this->addResource('client');
		$this->addResource('commande');
		$this->addResource('index');
		$this->addResource('produit');
		$this->addResource('sidedar');

		// Administration
		$this->allow('LOGGED','administration'); // Autorise accès au controller administration pour l'administration
		$this->allow('NOT_LOGGED','administration','login'); // Autorise accès à la page login de l'administration pour Visiteurs et Membres
		$this->deny('NOT_LOGGED','administration',null); // Aucun accès aux pages administration pour Visiteurs et Membres
			
		//Client
		$this->allow('Member','client'); // Autorise accès au controller administration pour l'administrateur
		$this->deny('Member','client',array('liste','ajout-admin')); // Aucun accès aux pages clients pour Visiteurs et Admin
		$this->allow('NOT_LOGGED','client',array('tableau-bord','ajout','panier','checkout-adresse')); // Autorise accès à la page login du client pour Visiteurs et Membres
		$this->allow('LOGGED','client',array('liste','ajout-admin','delete')); // Aucun accès aux pages clients pour Visiteurs et Admin
		
		
		//Commande
		$this->allow('LOGGED','commande');// Autorise accès au controller conmmande pour l'administrateur
		$this->deny('NOT_LOGGED','commande'); // Aucun accès aux pages commandes pour Visiteurs et Membres
		
		//index
		$this->allow('NOT_LOGGED','index'); // Tous les droits à tout le monde
		
		//produit
		$this->deny('NOT_LOGGED','produit', array('ajout','delete','update','liste','affichage-sous-categorie'));
		$this->allow('NOT_LOGGED','produit');// Autorise accès au controller produit pour tout le monde
		$this->allow('LOGGED', 'produit',array('ajout','delete','update','liste','affichage-sous-categorie'));// Autorise accès au controller produit pour tout le monde
	}
}







