<?php
class Application_Acl_Acl extends Zend_Acl
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
		$this->addRole(new Zend_Acl_Role('MEMBER'), 'NOT_LOGGED');
		$this->addRole(new Zend_Acl_Role(array('1','2','3','4','5','6','7')),'NOT_LOGGED');
		
		$this->addResource('Shop/administration');
		$this->addResource('Shop/client');
		$this->addResource('Shop/commande');
		$this->addResource('Shop/index');
		$this->addResource('Shop/produit');
		$this->addResource('Shop/sidedar');

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







