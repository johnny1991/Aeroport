<?php
class Application_Acl_Acl extends Zend_Acl
{
	public function __construct()
	{
		$this->addRole(new Zend_Acl_Role('Visitor'));
		$this->addRole(new Zend_Acl_Role('Member'),'Visitor');
		$this->addRole(new Zend_Acl_Role('Admin'),'Visitor');

		$this->addResource('administration');
		$this->addResource('categorie');
		$this->addResource('client');
		$this->addResource('commande');
		$this->addResource('index');
		$this->addResource('produit');
		$this->addResource('sidedar');

		// Administration
		$this->allow('Admin','administration'); // Autorise accès au controller administration pour l'administration
		$this->allow('Visitor','administration','login'); // Autorise accès à la page login de l'administration pour Visiteurs et Membres
		$this->deny('Visitor','administration',null); // Aucun accès aux pages administration pour Visiteurs et Membres
		
		//Categorie
		$this->allow('Admin','categorie',null); // Aucun accès aux pages categories pour Visiteurs et Membres
		
		//Client
		$this->allow('Member','client'); // Autorise accès au controller administration pour l'administrateur
		$this->deny('Member','client',array('liste','ajout-admin')); // Aucun accès aux pages clients pour Visiteurs et Admin
		$this->allow('Visitor','client',array('tableau-bord','ajout','panier','checkout-adresse')); // Autorise accès à la page login du client pour Visiteurs et Membres
		$this->allow('Admin','client',array('liste','ajout-admin','delete')); // Aucun accès aux pages clients pour Visiteurs et Admin
		
		
		//Commande
		$this->allow('Admin','commande');// Autorise accès au controller conmmande pour l'administrateur
		$this->deny('Visitor','commande'); // Aucun accès aux pages commandes pour Visiteurs et Membres
		
		//index
		$this->allow('Visitor','index'); // Tous les droits à tout le monde
		
		//produit
		$this->deny('Visitor','produit', array('ajout','delete','update','liste','affichage-sous-categorie'));
		$this->allow('Visitor', 'produit');// Autorise accès au controller produit pour tout le monde
		$this->allow('Admin', 'produit',array('ajout','delete','update','liste','affichage-sous-categorie'));// Autorise accès au controller produit pour tout le monde
	}
}







