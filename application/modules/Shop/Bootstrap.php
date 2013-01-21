<?php

class Shop_Bootstrap extends Zend_Application_Module_Bootstrap
{
	
	
	/*
	 protected function _initDataBase(){ // Initialisation de la base de données
	
		$config = new Zend_Config_Ini(APPLICATION_PATH.'/configs/application.ini','development');
		$db = Zend_Db::factory($config->database1);
		Zend_Db_Table_Abstract::setDefaultAdapter($db);
		Zend_Registry::set('db',$db);
		Zend_Registry::set('config',$config); // Mettre la config de application.ini dans le registre
	}
	*/

	protected function _initLibrairie(){ // Initialisation de la librarie personnelle
		$autoloader = Zend_Loader_Autoloader::getInstance();
		$autoloader->registerNamespace('Application_');
	}

	
	protected function _initBreadcrumb(){ // Initialisation de la navigation (fil d'ariane)

		$layout = $this->bootstrap('layout')->getResource('layout');
		$view = $layout->getView();
		$config = new Zend_Config(require APPLICATION_PATH.'/configs/navigationShop.php');
		$navigation = new Zend_Navigation();
		$view->navigation($navigation);
		$navigation->addPage($config);
		Zend_Registry::set('navigation',$navigation);
	}
/*
	protected function _initTranslate(){ // Initialisation De la traduction en Francais
		$translate = new Zend_Translate(array('adapter' => 'array', 'content' =>
				realpath(APPLICATION_PATH . '/../resources/languages'), 'locale' => 'fr', 'scan' =>
				Zend_Translate::LOCALE_DIRECTORY));
		Zend_Registry::set('Zend_Translate', $translate);
	}*/

	protected function _initRouter() { // Initialisation de la redirection des pages (URl Rewriting)
		$front = $this->bootstrap('FrontController')->getResource('FrontController');
		$router = $front->getRouter();
		$router->addRoute('Accueil', new Zend_Controller_Router_Route('accueil', array('module' => 'Shop', 'controller' => 'index', 'action' => 'index')));
		$router->addRoute('monCompte', new Zend_Controller_Router_Route('compte', array('module' => 'Shop', 'controller' => 'client', 'action' => 'tableau-bord')));
		$router->addRoute('connexionClient', new Zend_Controller_Router_Route('connexion', array('module' => 'Shop', 'controller' => 'index', 'action' => 'login')));
		$router->addRoute('deconnexion', new Zend_Controller_Router_Route('deconnexion', array('module' => 'Shop', 'controller' => 'index', 'action' => 'logout')));
		$router->addRoute('newCompte', new Zend_Controller_Router_Route('nouveau_compte', array('module' => 'Shop', 'controller' => 'client', 'action' => 'ajout')));
		$router->addRoute('panier', new Zend_Controller_Router_Route('panier/:rm', array('module' => 'Shop', 'controller' => 'client', 'action' => 'panier','rm'=>'')));
		$router->addRoute('menu', new Zend_Controller_Router_Route('menu', array('module' => 'Shop', 'controller' => 'administration', 'action' => 'login')));
		$router->addRoute('carnetAdresse', new Zend_Controller_Router_Route('carnet_adresses', array('module' => 'Shop', 'controller' => 'client', 'action' => 'carnet-adresse')));
		$router->addRoute('informationCompte', new Zend_Controller_Router_Route('information_compte', array('module' => 'Shop', 'controller' => 'client', 'action' => 'information-compte')));
		$router->addRoute('mesCommandes', new Zend_Controller_Router_Route('mes_commandes/:orderBy/:page', array('module' => 'Shop', 'controller' => 'client', 'action' => 'commande','orderBy'=>'Id_Desc', 'page'=>'')));
		$router->addRoute('ajoutAdresse', new Zend_Controller_Router_Route('ajouter_adresse', array('module' => 'Shop', 'controller' => 'client', 'action' => 'ajout-carnet-adresse')));
		$router->addRoute('modifierAdresse', new Zend_Controller_Router_Route('modifier_adresse/:id', array('module' => 'Shop', 'controller' => 'client', 'action' => 'ajout-carnet-adresse')));
		$router->addRoute('supprimerAdresse', new Zend_Controller_Router_Route('supprimer_adresse/:id', array('module' => 'Shop', 'controller' => 'client', 'action' => 'delete-carnet-adresse','id' => '')));
		$router->addRoute('mesFichesCommandes', new Zend_Controller_Router_Route('fiches_commandes/:id', array('module' => 'Shop', 'controller' => 'client', 'action' => 'fiche-commande','id' => '')));
		$router->addRoute('listeCommandes', new Zend_Controller_Router_Route('liste_commandes/:livre/:orderBy/:page', array('module' => 'Shop', 'controller' => 'commande', 'action' => 'liste', 'livre'=>'nonlivre', 'orderBy'=>'Id_Asc', 'page'=>'')));
		$router->addRoute('fichesCommandes', new Zend_Controller_Router_Route('fiche_commande/:id', array('module' => 'Shop', 'controller' => 'commande', 'action' => 'fiche')));
		$router->addRoute('livreCommandes', new Zend_Controller_Router_Route('livre_commande/:id/:islivre', array('module' => 'Shop', 'controller' => 'commande', 'action' => 'envoye')));
		$router->addRoute('ficheProduitAdmin', new Zend_Controller_Router_Route('fiche_produit_admin/:id', array('module' => 'Shop', 'controller' => 'administration', 'action' => 'fiche-produit')));
		$router->addRoute('listeProduit', new Zend_Controller_Router_Route('liste_produit/:orderBy/:page', array('module' => 'Shop', 'controller' => 'produit', 'action' => 'liste','orderBy'=>'Id_Desc', 'page'=>'')));
		$router->addRoute('ajoutProduit', new Zend_Controller_Router_Route('ajout_produit', array('module' => 'Shop', 'controller' => 'produit', 'action' => 'ajout')));
		$router->addRoute('listeClient', new Zend_Controller_Router_Route('liste_client/:orderBy/:page', array('module' => 'Shop', 'controller' => 'client', 'action' => 'liste','orderBy'=>'', 'page'=>'')));
		$router->addRoute('ajoutClient', new Zend_Controller_Router_Route('ajout_client', array('module' => 'Shop', 'controller' => 'client', 'action' => 'ajout-admin')));
		$router->addRoute('statutProduit', new Zend_Controller_Router_Route('statut_produit/:id', array('module' => 'Shop', 'controller' => 'produit', 'action' => 'statut')));
		$router->addRoute('supprimerProduit', new Zend_Controller_Router_Route('supprimer_produit/:id', array('module' => 'Shop', 'controller' => 'produit', 'action' => 'delete')));
		$router->addRoute('modifierProduit', new Zend_Controller_Router_Route('modifier_produit/:id', array('module' => 'Shop', 'controller' => 'produit', 'action' => 'update')));
		$router->addRoute('ficheClient', new Zend_Controller_Router_Route('fiche_client/:id', array('module' => 'Shop', 'controller' => 'administration', 'action' => 'fiche-client')));
		$router->addRoute('supprimerClient', new Zend_Controller_Router_Route('supprimer_client/:id', array('module' => 'Shop', 'controller' => 'client', 'action' => 'delete')));
		$router->addRoute('listeCategorie', new Zend_Controller_Router_Route('liste_categorie/:id/:orderBy', array('module' => 'Shop', 'controller' => 'categorie', 'action' => 'liste','id'=>'','orderBy'=>'Id_Asc')));
		$router->addRoute('ajoutCategorie', new Zend_Controller_Router_Route('ajout_categorie', array('module' => 'Shop', 'controller' => 'categorie', 'action' => 'ajout')));
		$router->addRoute('supprimerCategorie', new Zend_Controller_Router_Route('modifier_categorie', array('module' => 'Shop', 'controller' => 'categorie', 'action' => 'update')));
		$router->addRoute('parametre', new Zend_Controller_Router_Route('parametres', array('module' => 'Shop', 'controller' => 'administration', 'action' => 'parametre')));
		$router->addRoute('catalogue', new Zend_Controller_Router_Route('catalogue/:orderBy/:page', array('module' => 'Shop', 'controller' => 'produit', 'action' => 'catalogue', 'page'=>'', 'orderBy'=>'Date_Desc')));
		$router->addRoute('recherche', new Zend_Controller_Router_Route('recherche/:mot/:orderBy/:page', array('module' => 'Shop', 'controller' => 'produit', 'action' => 'catalogue', 'mot'=>'', 'page'=>'', 'orderBy'=>'Date_Desc')));
		$router->addRoute('new', new Zend_Controller_Router_Route('new/:orderBy/:page', array('module' => 'Shop', 'controller' => 'index', 'action' => 'new', 'page'=>'', 'orderBy'=>'Date_Desc')));
		$router->addRoute('topVentes', new Zend_Controller_Router_Route('top_ventes/:orderBy/:page', array('module' => 'Shop', 'controller' => 'index', 'action' => 'top-ventes', 'page'=>'', 'orderBy'=>'Date_Desc')));
		
	}

	/*
	protected function _initSession(){ // Initialisation de la session Panier
		$sessionConfig = Zend_registry::get('config')->session;
		$session = $sessionConfig->toArray();
		$panier = new Zend_Session_Namespace('panier');
		$panier->setExpirationSeconds($session['time_panier']);
	}
*/
	protected function _initAccessControlList(){ // Initialisation des ACL
		$SessionRole = new Zend_Session_Namespace('Role');
		if( (Zend_Auth::getInstance()->getIdentity()) && (isset(Zend_Auth::getInstance()->getIdentity()->id_client)) )
			$SessionRole->Role = "Member"; // Membre
		else if( (Zend_Auth::getInstance()->getIdentity()) && (isset(Zend_Auth::getInstance()->getIdentity()->id_admin)) )
			$SessionRole->Role = "Admin"; // Administrateur
		else
			$SessionRole->Role = "Visitor"; // Visiteur
	}
}