<?php
class Shop_AdministrationController extends Zend_Controller_Action
{

	public function loginAction(){ // Page de connexion Administrateur

		if( (Zend_Auth::getInstance()->getIdentity()) && (isset(Zend_Auth::getInstance()->getIdentity()->id_admin)) )
			$this->_redirector->gotoUrl('liste_commandes'); // Si l'administrateur est loggé, on le redirige vers la page commande
		else{
			//  // Si l'administrateur n'est pas loggé, on affiche le formulaire de connexion
			$this->view->title = "Connexion Administration"; // Attribution du titre de la page
			$this->_helper->layout->setLayout('categories'); // Attribution du layout Categories (Page avec une colonne à droite)
			$form = new Shop_Form_Login; // Nouveau formulaire Login (utilisé aussi pour la connexion client)

			$captcha = new Zend_Captcha_Image(); // Nouveau élément Captcha pour la connexion admin
			$cheminPublic = 'public/shop';
			$captcha->setDotNoiseLevel(25)->setLineNoiseLevel(2)->setWordLen(4)->setHeight(75)->setWidth(200)
			->setFont(APPLICATION_PATH.'/../'.$cheminPublic.'/font/Arial.ttf')->setFontSize(28)->setSuffix(".png")
			->setImgDir(APPLICATION_PATH.'/../'.$cheminPublic.'/captcha/')->setImgUrl('/captcha/')
			->setMessage("La valeur du captcha est fausse", 'badCaptcha');
			// Captcha avec mot de 4 lettres, 2 lignes barrées avec une hauteur de 75px et une largeur de 200px

			$Captcha = new Zend_Form_Element_Captcha('captcha', array('label'   => "Captcha *",'captcha' => $captcha));
			$Captcha->setRequired(true); // Captcha obligatoire
			$form->addElement($Captcha);
			$data = $this->getRequest()->getPost(); // Récupération des données en POST
			if(($this->getRequest()->isPost()) && ($form->isValid($data) && ($captcha->isValid($this->getRequest()->getParam('captcha')))))
			{
				// Si les données sont valident (pour le formulaire)
				$auth = Zend_Auth::getInstance(); // Création d'un zend_auth pour authentifier l'administateur
				$DbAdapter = new Zend_Auth_Adapter_DbTable(Zend_Registry::get('db'),'AdministrateurUser','login','password');
				$DbAdapter->setIdentity($data['login']);
				$DbAdapter->setCredential(md5($data['pass']));
				if($auth->authenticate($DbAdapter)->isValid()){
					// Si l'administrateur existe dans la bdd
					$auth->getStorage()->write($DbAdapter->getResultRowObject(null,'password')); // On met en "mémoire" l'administrateur
					$this->_redirector->gotoUrl('liste_commandes'); // On le redirige vers la page commande
				}
				else
					$this->view->message = "<div id='message_nok'><label>Identifiant ou mot de passe invalide.</label></div>"; // Si l'administrateur n'existe pas dans la bdd, on affiche un message d'erreur
			}				
				
			$form->populate($data); // On remplit le formulaire avec les données récupéré en POST
			$this->view->form = $form; // On envoie le formualire à la vue
		}

	}

	public function ficheProduitAction(){ // Page d'affichage d'un produit coté administrateur

		$this->view->title = "Affichage du produit"; // Attribution du titre de la page
		$this->view->id = $this->getRequest()->getParam('id'); // On envoie l'identifiant produit à la vue

	}

	public function ficheClientAction(){ // Page de visualisation/modification des clients

		$id = $this->getRequest()->getParam('id'); // On récupère l'identifiant produit
		$this->view->title = "Information du compte"; // Attribution du titre de la page
		$tableClient = new Shop_Model_Client; // On récupère la table Client
		$client = $tableClient->find($id)->current(); // On récupère le client via son identifiant
		$form = new AjoutClient(); // Nouveau formulaire d'ajout client
		if($client != NULL) {
			// Si le client existe
			$form->getElement('password')->setRequired(false); // On met les champs password et password1 à non obligatoire pour éviter de réinitialiser le mot de passe
			$form->getElement('password1')->setRequired(false);
			$Identifiant = new Zend_Form_Element_Text('Identifiant'); // On créer un élément identifiant pour afficher l'indentifiant du client
			$Identifiant->setLabel('Identifiant *');
			$Identifiant->setAttrib('disable','disable'); // On désactive l'ecriture dans le champ

			$PasswordVisible = new Zend_Form_Element_Text('passVisible'); // On créer un element Visible pour réinitialiser le mot de passe
			$PasswordVisible->setLabel('Réinitialisation du mot de passe');

			$PasswordVisible->setRequired(false); // On met ce champ non obligatoire pour ne pas réinitialiser le mot de passe si l'on veut juste modifier les infos du client

			$form->addElement($Identifiant);
			$form->addElement($PasswordVisible);

			$data = $this->getRequest()->getPost(); // Récupération des données en POST
			if($this->getRequest()->isPost())
			{
				$client1 = $tableClient->fetchAll($tableClient->select()->where('login=?',$data['login']))->current(); // On récupère le client via son identifiant
				if(($client1 == NULL) || (($client1 != NULL) && ($client1->id_client == $id)))
				{
					// Si l'on veut modifier les infos du client et que le login n'existe pas déja ou est le même qu'avant
					if($form->isValid($data))
					{
						// Si le formulaire est valide
						$client->nom = $this->getRequest()->getPost('nom');
						$client->prenom = $this->getRequest()->getPost('prenom');
						$client->mail = $this->getRequest()->getPost('mail');
						$client->login = $this->getRequest()->getPost('login');
						if(md5($this->getRequest()->getPost('passVisible')) != $client->password && ($this->getRequest()->getPost('passVisible') != "")) // Si l'on réinitialise le mot de passe sinon on fait rien
							$client->password = md5($this->getRequest()->getPost('passVisible'));
						$client->save(); // on enregistre les infos du client
						$this->view->message = "<div id='message_ok'><label>Les informations du compte ont été sauvegardées.</label></div>"; // On affiche un message de modification OK
					}
				}
				else // Si l'on veut modifier les infos du client et que le login existe déja
					$form->getElement('login')->addError("Login déjà utilisé"); // On affiche un message d'erreur
			}
			else
				$data = $client->toArray(); // On met dans data les infos du client (pour peupler le formulaire

			$data['Identifiant'] = $id; // On met l'identifiant dans data
			$form->populate($data); // On remplit le formulaire avec les données data
			$this->view->form = $form; // On envoie le formualire à la vue
			$this->view->client = $client; // On envoie le client à la vue
		}
		else // Si le client n'existe pas, on redirige la page vers la liste clients
			$this->_redirector->gotoUrl('client/liste');

	}

	public function parametreAction(){ // Page de paramètre administrateur
		$this->view->title = "Paramètre administration"; // Attribution du titre de la page
		$TableAdmin = new Shop_Model_AdministrateurUser();
		$Admin = $TableAdmin->fetchRow(); // On récupère l'unique ligne administrateur dans la bdd

		$formConnexion = new Zend_Form(); // On créer un formulaire pour modifier le login et mot de passe lors de l'administration client
		$Elogin = new Zend_Form_Element_Text('login');
		$Elogin->setLabel('Nouveau Login *');
		$Epassword = new Zend_Form_Element_Password('password');
		$Epassword->setLabel('Nouveau mot de passe *');
		$Esubmit = new Zend_Form_Element_Submit('modifier');
		$Esubmit->setLabel('Modifier');
		$formConnexion->addElement($Elogin);
		$formConnexion->addElement($Epassword);
		$formConnexion->addElement($Esubmit);
		$this->view->formConnexion = $formConnexion; // On envoie le formulaire à la vue
		$formConnexion->populate(array('login' =>$Admin->login)); // On peuple le formulaire avec les données de la bdd

		$TableParametre = new Shop_Model_Parametre();
		$Parametre = $TableParametre->fetchRow(); // On récupère l'unique ligne paramètre dans la bdd
		$formMail = new Zend_Form(); // On créer un formulaire pour modifier l'email et le mot de passe pour l'envoie des emails
		$EMail = new Zend_Form_Element_Text('mail');
		$EMail->setLabel('Adresse UPJV *');
		$EPass = new Zend_Form_Element_Password('password1');
		$EPass->setLabel('Mot de passe *');
		$Esubmit1 = new Zend_Form_Element_Submit('modifier1');
		$Esubmit1->setLabel('Modifier');
		$formMail->addElement($EMail);
		$formMail->addElement($EPass);
		$formMail->addElement($Esubmit1);
		$this->view->formMail = $formMail; // On envoie le formulaire à la vue
		$formMail->populate(array('mail'=>$Parametre->email)); // On peuple le formulaire avec les données de la bdd


		$formDetails = new Zend_Form(); // On créer un formulaire pour modifier le nombre de produits visible dans les pages (administration et catalogue)
		$Esite = new Zend_Form_Element_Text('site');
		$Esite->setLabel('Nom du site *');
		$EnbProduit = new Zend_Form_Element_Text('nbProduits');
		$EnbProduit->setLabel('Nombres d\'éléments par liste (Côté administration) *');
		$EnbElement = new Zend_Form_Element_Text('nbElements');
		$EnbElement->setLabel('Nombres d\'articles par page (Côté client) *');
		$Esubmit2 = new Zend_Form_Element_Submit('modifier2');
		$Esubmit2->setLabel('Modifier');
		$formDetails->addElement($Esite);
		$formDetails->addElement($EnbProduit);
		$formDetails->addElement($EnbElement);
		$formDetails->addElement($Esubmit2);
		$this->view->formDetails = $formDetails; // on envoie le formulaire à la vue
		$formDetails->populate(array('site'=>$Parametre->site, 'nbProduits'=>$Parametre->nbProduits,'nbElements'=>$Parametre->nbElements)); // On peuple le formulaire avec les données de la bdd

		$data = $this->getRequest()->getPost(); // Récupération des données en POST
		if($this->getRequest()->isPost())
		{
			// Si il y a des données envoyés en POST
			if(isset($data['modifier'])){
				// Si le premier formulaire a été modifier
				$Elogin->setRequired(true); // on met les 2 champs du formulaire obligatoire
				$Epassword->setRequired(true);
				if($formConnexion->isValid($data)){
					// Si le formulaire est valide
					$Admin->login = $data['login'];
					$Admin->password = md5($data['password']);
					$Admin->save(); // On enregistre dans la bdd
					$this->view->message = "<div id='message_ok'><label>La modification des paramètres de connexion est réussi !!</label></div>"; // on affiche un message OK
				}
			}
			else if(isset($data['modifier1'])){
				// Si le deuxieme formulaire a été modifier
				$EMail->setRequired(true);
				$EPass->setRequired(true);
				if($formMail->isValid($data)){
					// Si le formulaire est valide
					$Parametre->email = $data['mail'];
					$Parametre->password = $data['password1'];
					$Parametre->save(); // On enregistre dans la bdd
					$this->view->message = "<div id='message_ok'><label>La modification des paramètres d'Email est réussi !!</label></div>"; // on affiche un message OK
				}
			}
			else if(isset($data['modifier2'])){
				// Si le troisieme formulaire a été modifier
				$Esite->setRequired(true);
				$EnbProduit->setRequired(true);
				$EnbElement->setRequired(true);
				if($formDetails->isValid($data)){
					// Si le formulaire est valide
					$Parametre->site = $data['site'];
					$Parametre->nbProduits = $data['nbProduits'];
					$Parametre->nbElements = $data['nbElements'];
					$Parametre->save(); // On enregistre dans la bdd
					$this->view->message = "<div id='message_ok'><label>La modification des paramètres est réussi !!</label></div>"; // on affiche un message OK
				}
			}
		}
	}

	public function init()
	{
		$this->view->messages = $this->_helper->FlashMessenger->getMessages(); // Création des messages que l'on affiche dans certaines pages
		$this->_redirector = $this->_helper->getHelper('Redirector');
		$this->_helper->layout->setLayout('administration'); // Layout administration de base

		$SessionRole = new Zend_Session_Namespace('Role');  // Récupération de la session Role (definit dans le bootsrap)
		$acl = new Application_Acl_Acl();
		if(!($acl->isAllowed($SessionRole->Role,$this->getRequest()->getControllerName(),$this->getRequest()->getActionName()))) // Si l'utilisateur n'a pas le droit d'acceder à cette page, on le redirige vers une page d'erreur
			$this->_redirector->gotoUrl('');
	}
}
