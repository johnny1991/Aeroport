<?php

class Shop_IndexController extends Zend_Controller_Action
{
	public function indexAction() // Page d'accueil
	{
		$this->view->title="Page d'Accueil";
		$this->_helper->layout->setLayout('categories');
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
				$DbAdapter = new Zend_Auth_Adapter_DbTable(Zend_Registry::get('db'),'client','login','password');
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

	public function init()
	{
		$this->view->messages = $this->_helper->FlashMessenger->getMessages();
		$this->_redirector = $this->_helper->getHelper('Redirector');
		$TableParametre = new Shop_Model_Parametre();
		$Parametre = $TableParametre->fetchRow();
		$this->view->nbProduits = $Parametre->nbProduits;
		$this->view->nbElements = $Parametre->nbElements;
	}
}

