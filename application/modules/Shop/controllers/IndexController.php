<?php

class Shop_IndexController extends Zend_Controller_Action
{
	public function indexAction() // Page d'accueil
	{
		$this->view->title="Page d'Accueil";
		$this->_helper->layout->setLayout('categories');
		$TableProduit = new Shop_Model_Produit;
		$requete = $TableProduit->select()->where('actif=1')->order('date desc')->limit('6');
		$this->view->produits = $TableProduit->fetchAll($requete);
		$requete1 = $TableProduit->select()->setIntegrityCheck(false)->from(array('p'=>'Produit'))->where('actif=1')
		->joinLeft(array('cp'=>'CommandeProduit'),'p.id_produit = cp.id_produit',array('nbProduit' => 'count(cp.id_produit)'))
		->order('nbProduit desc')->group('id_produit')->limit('6');
		$this->view->produits1 = $TableProduit->fetchAll($requete1);
	}

	public function loginAction(){ // Page de connexion client
		$this->view->title = "Identifiant client";
		$this->_helper->layout->setLayout('default');
		$formLogin = new Shop_Form_Login;
		$Commande = new Zend_Session_Namespace('commande');
		if($this->getRequest()->isPost())
		{
			$data=$this->getRequest()->getPost();
			if($formLogin->isValid($data))
			{
				$auth = Zend_Auth::getInstance();
				$DbAdapter = new Zend_Auth_Adapter_DbTable(Zend_Registry::get('db'),'Client','login','password');
				$DbAdapter->setIdentity($data['login']);
				$DbAdapter->setCredential(md5($data['pass']));
				if($auth->authenticate($DbAdapter)->isValid()){
					$auth->getStorage()->write($DbAdapter->getResultRowObject(null,'password'));
					if($Commande->isLogin == "commande"){
						$Commande->isLogin = false; // a revoir
						$this->_redirector->gotoUrl('Shop/client/checkout-adresse');
					}
					else
						$this->_redirector->gotoUrl('compte');
				}
				else
				{
					$this->view->message = "<div id='message_nok'><label>Identifiant ou mot de passe invalide.</label></div>";
					$formLogin->populate($data);
				}
			}
			else
			{
				$formLogin->populate($data);
			}
		}
		$this->view->formLogin = $formLogin;
	}

	public function logoutAction(){ // Page de deconnexion

		Zend_Auth::getInstance()->clearIdentity();
		$this->_redirector->gotoUrl('accueil');

	}

	public function newAction(){ // Page de nouveautés
		$this->_helper->layout->setLayout('categories');
		$this->view->title = "Nouveautés";
		$TableProduit = new Shop_Model_Produit;
		$requete = $TableProduit->select()->where('actif=1')->order('date desc');

		if($this->getRequest()->getParam('orderBy'))
			$orderBy = $this->getRequest()->getParam('orderBy');
		else
			$orderBy = "Id_Asc";

		$this->view->produits = $TableProduit->fetchAll($requete);
		$paginator = Zend_Paginator::factory($TableProduit->fetchAll($requete));
		$paginator->setItemCountPerPage($this->view->nbElements);
		$paginator->setCurrentPageNumber($this->getRequest()->getParam('page'));
		$this->view->pagination = $this->view->paginationControl($paginator, 'Sliding', 'pagination.phtml',array("param"=>$this->getAllParams()));
		$this->view->paginator = $paginator;
	}

	public function topVentesAction(){ // Page de top-ventes
		$this->_helper->layout->setLayout('categories');
		
		$this->view->title = "Top Ventes";
		$TableProduit = new Shop_Model_Produit;
		$requete = $TableProduit->select()->setIntegrityCheck(false)->from(array('p'=>'Produit'))->where('actif=1')
		->joinLeft(array('cp'=>'CommandeProduit'),'p.id_produit = cp.id_produit',array('nbProduit' => 'count(cp.id_produit)'))
		->order('nbProduit desc')->group('id_produit');

		if($this->getRequest()->getParam('orderBy'))
			$orderBy = $this->getRequest()->getParam('orderBy');
		else
			$orderBy = "Id_Asc";

		$paginator = Zend_Paginator::factory($TableProduit->fetchAll($requete));
		$paginator->setItemCountPerPage($this->view->nbElements);
		$paginator->setCurrentPageNumber($this->getRequest()->getParam('page'));
		$this->view->pagination = $this->view->paginationControl($paginator, 'Sliding', 'pagination.phtml',array("param"=>$this->getAllParams()));
		$this->view->paginator = $paginator;
	}

	public function init()
	{
		//parent::init();
		//$models = $this->_request->getParam('module').'/models';
		
		//set_include_path(APPLICATION_PATH.'/modules/shop/models'. PATH_SEPARATOR . get_include_path());
		$this->view->messages = $this->_helper->FlashMessenger->getMessages();
		$this->_redirector = $this->_helper->getHelper('Redirector');
		$TableParametre = new Shop_Model_Parametre();
		$Parametre = $TableParametre->fetchRow();
		$this->view->nbProduits = $Parametre->nbProduits;
		$this->view->nbElements = $Parametre->nbElements;
	}
}

