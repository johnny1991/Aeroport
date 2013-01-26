<?php

class IndexController extends Zend_Controller_Action
{
	public function init()
	{
		$this->_redirector = $this->_helper->getHelper('Redirector');
		
		$acl = new Aeroport_LibraryAcl();
		$SRole = new Zend_Session_Namespace('Role');
		if(!$acl->isAllowed($SRole->id_service, $this->getRequest()->getControllerName(), $this->getRequest()->getActionName()))
		{
			//echo $SRole->id_service . '<br />';
			//echo $this->getRequest()->getControllerName(). '<br />';
			//echo $this->getRequest()->getActionName();
			$this->_redirector->gotoUrl('/');
		}
	}

	public function indexAction()
	{
		$this->_helper->layout()->disableLayout();
		
		$auth = Zend_Auth::getInstance(); // on creer l'authentification
		$form = new Login(); //nouveau formulaire de log
		$this->view->formLogin = $form;
		if ($this->_request->isPost()) {
			$formData = $this->_request->getPost();
			if ($form->isValid($formData)) { //si le formulaire est valide
				$login = $form->getValue('login'); // on récupère les valeur entrées
				$password = $form->getValue('password');
				$authAdapter = new Zend_Auth_Adapter_DbTable(Zend_Db_Table::getDefaultAdapter());
				$authAdapter->setTableName('utilisateur')
				->setIdentityColumn('login') // on les compare
				->setCredentialColumn('password')
				->setIdentity($login)
				->setCredential(md5($password));
				$authAuthenticate = $authAdapter->authenticate();
		
				if ($authAuthenticate->isValid()) { // si l'authentification est bonne
					$storage = Zend_Auth::getInstance()->getStorage(); // et si la comparaison est bonne
					$storage->write($authAdapter->getResultRowObject(null, 'password'));
					
					if($auth->getIdentity()->id_service == "1"){// direction stratégique
						$this->_helper->redirector('consulter-ligne','vol');
					}
					elseif($auth->getIdentity()->id_service == "2"){// service commercial
						$this->_helper->redirector('liste','commande','Shop');
					}
					elseif($auth->getIdentity()->id_service == "3"){// service maintenance
						$this->_helper->redirector('consulter-maintenance','maintenance');
					}
					elseif($auth->getIdentity()->id_service == "4"){// drh
						$this->_helper->redirector('index','drh');
					}
					elseif($auth->getIdentity()->id_service == "5"){// service planning
						$this->_helper->redirector('index','planning');
					}
					elseif($auth->getIdentity()->id_service == "6"){// service exploitation
						$this->_helper->redirector('index','vol');
					}
					elseif($auth->getIdentity()->id_service == "7"){// logistique commerciale
						$this->_helper->redirector('index','vol');
					}
					
					//Zend_Debug::dump($auth->getIdentity()->id_service);exit();
					
					//$this->_helper->redirector('index','vol'); // on redirige vers l'index
				}else { // sinon on affiche le message suivant
					$form->setDescription('Le couple email / mot de passe n\'est pas valide');
				}
			}
		}
	}
	
	public function deconnexionAction(){
			Zend_Auth::getInstance()->clearIdentity(); //on détruit l'authentification
			$this->_redirector->gotoUrl('/');
	}
}

