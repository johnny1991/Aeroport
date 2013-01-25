<?php
class Shop_ClientController extends Zend_Controller_Action
{
	public function ajoutAction(){ // Page d'ajout d'un client
		if($this->getParam('admin')==true)
			$this->_helper->layout->setLayout('administration');

		$this->view->title = "Créer un nouveau compte client";
		$form = new Shop_Form_AjoutClient();
		$Commande = new Zend_Session_Namespace('commande');
		$tableClient = new Shop_Model_Client;
		$data = $this->getRequest()->getPost();

		if($this->getRequest()->isPost())
		{
			if($data['password'] == $data['password1'])
			{
				if($tableClient->fetchAll($tableClient->select()->where('login=?',$data['login']))->current() == NULL)
				{
					if($form->isValid($data))
					{
						$client = $tableClient->createRow();
						$client->nom = $this->getRequest()->getPost('nom');
						$client->prenom = $this->getRequest()->getPost('prenom');
						$client->mail = $this->getRequest()->getPost('mail');
						$client->login = $this->getRequest()->getPost('login');
						$client->password = md5($this->getRequest()->getPost('password'));
						$client->save();
						if($this->getRequest()->getParam('admin')){
							$message = "<div id='message_ok'><label>L'ajout du client '".$client->nom."' '".$client->prenom."' est réussi !!</label></div>";
							$this->_helper->FlashMessenger($message);
							$this->_redirector->gotoUrl('/client/liste');
						}
						else
						{
							$auth = Zend_Auth::getInstance();
							$DbAdapter = new Zend_Auth_Adapter_DbTable(Zend_Registry::get('db'),'Client','login','password');
							$DbAdapter->setIdentity($data['login'])->setCredential(md5($data['password']));
							if($auth->authenticate($DbAdapter)->isValid())
								$auth->getStorage()->write($DbAdapter->getResultRowObject(null,'password'));
							if($Commande->isLogin == "commande")
							{
								$Commande->isLogin = false;
								$this->_redirector->gotoUrl('/client/checkout-adresse');
							}
							else
								$this->_redirector->gotoUrl('compte');
						}
					}
				}
				else
					$form->getElement('login')->addError("Login déjà utilisé");
			}
			else
				$form->getElement('password')->addError("Les mots de passe ne sont pas les mêmes");
		}
		$form->populate($data);
		$this->view->form = $form;

	}

	public function ajoutAdminAction(){ // Page d'ajout d'un client coté administration

		$this->view->title = "Créer un nouveau compte client";
		$this->_helper->layout->setLayout('administration');

	}

	public function deleteAction(){ // Page de suppression d'un client

		$tableClient = new Shop_Model_Client;
		$Client = $tableClient->find($this->getRequest()->getParam('id'))->current();
		$message = "<div id='message_ok'><label>La suppression du client '".$Client->nom."' '".$Client->prenom."' est réussi !!</label></div>";
		$Client->delete();
		$this->_helper->FlashMessenger($message);
		$this->_redirector->gotoUrl('client/liste');

	}

	public function listeAction(){ // Liste des clients (coté administration)

		$this->view->title = "Catalogue client";
		$this->_helper->layout->setLayout('administration');
		$tableClient = new Shop_Model_Client;

		if($this->getRequest()->getParam('orderBy'))
			$orderBy=$this->getRequest()->getParam('orderBy');
		else
			$orderBy="Id_Asc";

		$this->view->order=$orderBy;

		$requete = $tableClient->select()
		->setIntegrityCheck(false)
		->from(array('cl'=>'Client'),array('cl.id_client','cl.nom','cl.mail','cl.login'))
		->joinLeft(array('co'=>'Commande'),'co.id_client = cl.id_client',array('co.id_commande','montant'=>'sum(co.montant)','co.date'))
		->group('cl.id_client')
		;

		switch ($orderBy)
		{
			case "Id_Asc": $requete->order("cl.id_client asc"); break;
			case "Id_Desc": $requete->order("cl.id_client desc"); break;
			case "Nom_Asc": $requete->order("cl.nom asc"); break;
			case "Nom_Desc": $requete->order("cl.nom desc"); break;
			case "Montant_Asc": $requete->order("montant asc"); break;
			case "Montant_Desc": $requete->order("montant desc"); break;
			case "Mail_Asc": $requete->order("cl.mail asc"); break;
			case "Mail_Desc": $requete->order("cl.mail desc"); break;
			case "Login_Asc": $requete->order("cl.login asc"); break;
			case "Login_Desc": $requete->order("cl.login desc"); break;
			case "Date_Asc": $requete->order("co.date asc"); break;
			case "Date_Desc": $requete->order("co.date desc"); break;
		}

		$this->view->HeadId = Application_Tableau_OrderColumn::orderColumns($this, "Id",$orderBy,"idLigneClient","Id");
		$this->view->HeadNom = Application_Tableau_OrderColumn::orderColumns($this,"Nom",$orderBy,"nomLigneClient","Nom");
		$this->view->HeadMontant = Application_Tableau_OrderColumn::orderColumns($this,"Montant",$orderBy,"montantLigneClient","Montant Total");
		$this->view->HeadMail = Application_Tableau_OrderColumn::orderColumns($this,"Mail",$orderBy,"mailLigneClient","Email");
		$this->view->HeadLogin = Application_Tableau_OrderColumn::orderColumns($this,"Login",$orderBy,"loginLigneClient","Login");
		$this->view->HeadDate = Application_Tableau_OrderColumn::orderColumns($this,"Date",$orderBy,"dateLigneClient","Date et Heure");

		$clients = $tableClient->fetchAll($requete);

		$paginator = Zend_Paginator::factory($clients);
		$paginator->setItemCountPerPage($this->view->nbClient);
		$paginator->setCurrentPageNumber($this->getRequest()->getParam('page'));
		$this->view->pagination = $this->view->paginationControl($paginator, 'Sliding', 'pagination.phtml',array("param"=>$this->getAllParams()));
		$this->view->paginator = $paginator;

	}

	public function tableauBordAction(){ /// Tableau de bord d'un client

		if( (Zend_Auth::getInstance()->getIdentity()) && (isset(Zend_Auth::getInstance()->getIdentity()->id_client)) )
		{
			$this->view->title = "Mon compte";
			$this->_helper->layout->setLayout('client');
			$id = Zend_Auth::getInstance()->getIdentity()->id_client;
			$TableClient = new Shop_Model_Client;
			$TableAdresseClient = new Shop_Model_AdresseClient;
			$this->view->client = $TableClient->find($id)->current();
			$requete = $TableAdresseClient->select()->where('id_client=? AND defaut=1',$id);
			$this->view->adresse = $TableAdresseClient->fetchRow($requete);
		}
		else
			$this->_redirector->gotoUrl('connexion');

	}

	public function informationCompteAction(){ // Page d'information du compte pour un client

		$this->view->title = "Information du compte";
		$this->_helper->layout->setLayout('client');
		$id = Zend_Auth::getInstance()->getIdentity()->id_client;
		$TableClient = new Shop_Model_Client;
		$client = $TableClient->find($id)->current();
		$this->view->client = $client;
		$form = new Shop_Form_AjoutClient();
		$OldPassword = new Zend_Form_Element_Password('oldPassword');
		$form->addElement($OldPassword);
		$form->getElement('password')->setRequired(false);
		$form->getElement('password1')->setRequired(false);
		$data = $this->getRequest()->getPost();
		if($this->getRequest()->isPost())
		{
			$client1 = $TableClient->fetchAll($TableClient->select()->where('login=?',$data['login']))->current();
			if(($client1 == NULL) || (($client1 != NULL) && ($client1->id_client == $id)))
			{
				if($form->isValid($data))
				{
					$client->nom = $this->getRequest()->getPost('nom');
					$client->prenom = $this->getRequest()->getPost('prenom');
					$client->mail = $this->getRequest()->getPost('mail');
					$client->login = $this->getRequest()->getPost('login');
					if(md5($this->getRequest()->getPost('oldPassword')) == $client->password)
						$client->password = md5($this->getRequest()->getPost('password'));
					$client->save();
					$this->view->message = "<div id='message_ok'><label>Les informations du compte ont été sauvegardées.</label></div>";
				}
			}
			else
				$form->getElement('login')->addError("Login déjà utilisé");
		}
		else
			$data = $client->toArray();

		$form->populate($data);
		$this->view->form = $form;

	}

	public function carnetAdresseAction(){ // Page de carnet d'adresse d'un client

		$this->view->title = "Carnet d'adresses";
		$this->_helper->layout->setLayout('client');
		$id = Zend_Auth::getInstance()->getIdentity()->id_client;
		$TableClient = new Shop_Model_Client;
		$TableAdresseClient = new Shop_Model_AdresseClient;
		$this->view->client = $TableClient->find($id)->current();
		$this->view->adresseDefault = $TableAdresseClient->fetchRow($TableAdresseClient->select()->where('id_client=? AND defaut=1',$id));
		$this->view->adresses = $TableAdresseClient->fetchAll($TableAdresseClient->select()->where('id_client=? AND defaut=0',$id));

	}

	public function ajoutCarnetAdresseAction(){ // Page d'ajout une adresse d'un client

		$this->_helper->layout->setLayout('client');
		$form = new Shop_Form_AjoutAdresse;
		$id = Zend_Auth::getInstance()->getIdentity()->id_client;
		$TableAdresseClient = new Shop_Model_AdresseClient;
		$data = $this->getRequest()->getPost();

		if($this->getRequest()->isPost())
		{
			if($form->isValid($data))
			{
				$AdresseDefaults = $TableAdresseClient->fetchAll($TableAdresseClient->select()->where('id_client=? AND defaut=1',$id));
				if($this->getRequest()->getParam('id'))
					$adresse = $TableAdresseClient->find($this->getRequest()->getParam('id'),$id)->current();
				else
				{
					$adresse = $TableAdresseClient->createRow();
					$adresse->id_adresse = $TableAdresseClient->getLastId($id)+1;
				}
				$adresse->id_client = $id;
				$adresse->pays = $this->getRequest()->getPost('pays');
				$adresse->code_postal = $this->getRequest()->getPost('code_postal');
				$adresse->ville = $this->getRequest()->getPost('ville');
				$adresse->adresse = $this->getRequest()->getPost('adresse');
				if(!($AdresseDefaults->count()))
					$adresse->defaut = 1;

				if($this->getRequest()->getPost('defaut'))
				{
					$AdresseDefault = $AdresseDefaults->current();
					$AdresseDefault->defaut = 0;
					$AdresseDefault->save();
					$adresse->defaut = 1;
				}
				$adresse->save();
				$this->_redirector->gotoUrl('carnet_adresses');
			}
			else
			{
				if($this->getRequest()->getParam('id'))
				{
					$this->view->title = "Modifier une adresse";
					$this->view->h2 = "Modifier une adresse";
				}
				else{
					$this->view->h2 = "Ajouter une nouvelle adresse";
					$this->view->title = "Ajouter une nouvelle adresse";
				}
			}
		}
		else
		{
			if($this->getRequest()->getParam('id'))
			{
				$this->view->title = "Modifier une adresse";
				$this->view->h2 = "Modifier une adresse";
				$Adresse = $TableAdresseClient->find($this->getRequest()->getParam('id'),$id)->current();
				$data = $Adresse->toArray();
			}
			else
			{
				$this->view->h2 = "Ajouter une nouvelle adresse";
				$this->view->title = "Ajouter une nouvelle adresse";
			}
		}
		$form->populate($data);
		$this->view->form =$form;

	}

	public function deleteCarnetAdresseAction(){ // Page de suppression d'une adresse d'un client

		$TableAdresseClient = new Shop_Model_AdresseClient;
		if($this->getRequest()->getParam('id'))
		{
			$Adresse = $TableAdresseClient->find($this->getRequest()->getParam('id'),Zend_Auth::getInstance()->getIdentity()->id_client)->current();
			if($Adresse->defaut == 1)
				$message = "<div id='message_nok'><label>La suppression de cette adresse est impossible car c'est l'adresse par défaut !!</label></div>";
			else{
				$Adresse->delete();
				$message = "<div id='message_ok'><label>La suppression de l'adresse est réussi !!</label></div>";
			}
			$this->_helper->FlashMessenger($message);
		}
		$this->_redirector->gotoUrl('carnet_adresses');

	}

	public function commandeAction(){ // page d'affichage des commandes d'un client

		$this->view->title = "Mes commandes";
		$this->_helper->layout->setLayout('client');
		$nbCommande = $this->view->nbCommande;
		$TableCommande = new Shop_Model_Commande;
		$requete = $TableCommande
		->select()
		->from(array('c'=>'Commande'))
		->setIntegrityCheck(false)
		->join(array('cl'=>'Client'),'cl.id_client=c.id_client',array('cl.nom','cl.prenom'))
		->join(array('cp'=>'CommandeProduit'), 'cp.id_commande=c.id_commande',array("num"=>"COUNT(cp.id_produit)",'cp.id_produit'))
		->where('c.id_client=?',Zend_Auth::getInstance()->getIdentity()->id_client)
		->group('c.id_commande');

		if($this->getRequest()->getParam('orderBy'))
			$orderBy = $this->getRequest()->getParam('orderBy');
		else
			$orderBy = "Id_Desc";

		switch ($orderBy)
		{
			case "Id_Asc": $requete->order("c.id_commande asc"); break;
			case "Id_Desc": $requete->order("c.id_commande desc"); break;
			case "Nom_Asc": $requete->order("cl.nom asc"); break;
			case "Nom_Desc": $requete->order("cl.nom desc"); break;
			case "Prenom_Asc": $requete->order("cl.prenom asc"); break;
			case "Prenom_Desc": $requete->order("cl.prenom desc"); break;
			case "Nombre_Asc": $requete->order("num asc"); break;
			case "Nombre_Desc": $requete->order("num desc"); break;
			case "Montant_Asc": $requete->order("montant asc"); break;
			case "Montant_Desc": $requete->order("montant desc"); break;
			case "Date_Asc": $requete->order("c.date asc"); break;
			case "Date_Desc": $requete->order("c.date desc"); break;
			case "Livre_Asc": $requete->order("c.Islivre asc"); break;
			case "Livre_Desc": $requete->order("c.Islivre desc"); break;
		}

		$this->view->HeadId = Application_Tableau_OrderColumn::orderColumns($this, "Id",$orderBy,"idLigneCommandeClient","Id");
		$this->view->HeadNom = Application_Tableau_OrderColumn::orderColumns($this,"Nom",$orderBy,"nomLigneCommandeClient","Livré à");
		$this->view->HeadNombre = Application_Tableau_OrderColumn::orderColumns($this,"Nombre",$orderBy,"nombreLigneCommandeClient","Nb de produits");
		$this->view->HeadMontant = Application_Tableau_OrderColumn::orderColumns($this,"Montant",$orderBy,"montantLigneCommandeClient","Montant");
		$this->view->HeadDate = Application_Tableau_OrderColumn::orderColumns($this,"Date",$orderBy,"dateLigneCommandeClient","Date et Heure");
		$this->view->HeadLivre = Application_Tableau_OrderColumn::orderColumns($this,"Livre",$orderBy,"livreLigneCommandeClient","Statut");
			
		$paginator = Zend_Paginator::factory($TableCommande->fetchAll($requete));
		$paginator->setItemCountPerPage($nbCommande);
		$paginator->setCurrentPageNumber($this->getRequest()->getParam('page'));
		$this->view->pagination = $this->view->paginationControl($paginator, 'Sliding', 'pagination.phtml',array("param"=>$this->getAllParams()));
		$this->view->paginator = $paginator;

	}

	public function ficheCommandeAction(){ // page d'affichage d'un fiche commande d'un client

		$this->_helper->layout->setLayout('client');
		$id = $this->getRequest()->getParam('id');
		$TableCommande = new Shop_Model_Commande;
		$TableAdresseCLient = new Shop_Model_AdresseClient;
		$Commande = $TableCommande->find($id)->current();
		$TableTransport = new Shop_Model_Transport;
		$this->view->transport = $TableTransport->find($Commande->id_transport)->current();
		$TablePaiement = new Shop_Model_Paiement;
		$this->view->paiement = $TablePaiement->find($Commande->id_paiement)->current();

		if( ($id != NULL) && ($Commande) && ($Commande->id_client == Zend_Auth::getInstance()->getIdentity()->id_client) )
		{
			$this->view->client = $Commande->findParentRow('Client');
			if($this->view->client != NULL)
				$this->view->adresse = $TableAdresseCLient->fetchRow($TableAdresseCLient->select()->where('id_client=?',$this->view->client->id_client)->where('id_adresse=?',$Commande->id_adresse));

			$this->view->commande = $Commande;
			$this->view->commandeProduits = $Commande->findDependentRowset('CommandeProduit');
		}
		else
			$this->_redirector->gotoUrl('mes_commandes');

		$title = "Commande n°".$this->view->commande->id_commande." - ";
		if($this->view->commande->Islivre)
			$title .= "Reçu";
		else
			$title .= "En attente";
			
		$this->view->title = $title;

	}

	public function panierAction(){ // Page panier

		$this->view->title = "Mon panier";
		$form = new Shop_Form_Panier;
		$sessionPanier = new Zend_Session_Namespace('panier');
		$TableVol = new Vol();
		$produits = array();
		$totalProduit = 0;
		if($sessionPanier->content != NULL){
			foreach($sessionPanier->content as $id => &$quantite){
				$nomElement = 'quantite_'.$id;
				$element = new Zend_Form_Element_Text($nomElement);
				$form->addElement($element);

				$produit = $TableVol->find($id)->current();
				if($this->getRequest()->isPost())
				{
					$data = $this->getRequest()->getPost();
					if($form->isValid($data))
					{
						if($produit->quantite >= $data[$nomElement])
							$quantite = $data[$nomElement];
						else
						{
							$quantite = $produit->quantite;
							$form->getElement($nomElement)->addError("Max : ".$produit->quantite);
							$form->getElement($nomElement)->setValue($produit->quantite);
						}
					}
				}
				if( (($this->getRequest()->getParam('rm') != NULL) && ($this->getRequest()->getParam('rm') == $id)) || ($quantite == 0) )
				{
					unset($sessionPanier->content[$id]);
					$this->_redirector->gotoUrl($_SERVER["HTTP_REFERER"]);
				}
				else{
					$urlProd = new Application_Url();
					$produits[] = array($id,$produit,$quantite,$nomElement,$urlProd::getUrlProduit($produit));
				}
				$totalProduit += ($produit->prix * $quantite);
				$element->setAttrib('size',2);
				$element->setValue($quantite);
			}
		}
		$sessionPanier->sousTotal = $totalProduit;
		$this->view->sousTotal = $totalProduit;
		$this->view->form = $form;
		$this->view->produits = $produits;

	}

	public function checkoutAdresseAction(){ // Page de choix d'adresse pour une commande client

		$this->view->title = "Commander";
		$commande = new Zend_Session_Namespace('commande');
		$sessionPanier = new Zend_Session_Namespace('panier');
		if(count($sessionPanier->content) == 0){
			$commande->isLogin = false;
			$this->_redirector->gotoUrl('/panier');
		}
		if( (Zend_Auth::getInstance()->getIdentity()) && (isset(Zend_Auth::getInstance()->getIdentity()->id_client)) )
		{
			$form = new Shop_Form_AjoutAdresse;
			$TableAdresseClient = new Shop_Model_AdresseClient;
			$commande->isLogin = "commande";
			$id = Zend_Auth::getInstance()->getIdentity()->id_client;
			$TableClient = new Shop_Model_Client;
			$this->view->client = $TableClient->find($id)->current();
			$this->view->adresseDefault = $TableAdresseClient->fetchAll($TableAdresseClient->select()->where('id_client=? AND defaut=1',$id));
			$this->view->adresses = $TableAdresseClient->fetchAll($TableAdresseClient->select()->where('id_client=? AND defaut=0',$id));
			$form->getElement('ajouter')->setLabel('Continuer');
			$this->view->form = $form;
			if($this->view->adresseDefault->current() != NULL)
				$this->view->choose = $this->view->adresseDefault->current()->id_adresse;
			$data = $this->getRequest()->getPost();

			if($this->getRequest()->isPost())
			{
				if(( $form->isValid($data)) || (isset($data['choix']) && ($data['choix'] != -1 )))
				{
					if($data['choix'] == -1)
					{
						$adresse = $TableAdresseClient->createRow();
						$adresse->id_adresse = $TableAdresseClient->getLastId($id)+1;
						$adresse->id_client = $id;
						$adresse->pays = $this->getRequest()->getPost('pays');
						$adresse->code_postal = $this->getRequest()->getPost('code_postal');
						$adresse->ville = $this->getRequest()->getPost('ville');
						$adresse->adresse = $this->getRequest()->getPost('adresse');
						if(!($this->view->adresseDefault->count()))
							$adresse->defaut = 1;
						$adresse->save();
						$commande->adresse = $TableAdresseClient->find($adresse->id_adresse,$id)->current()->toArray();
					}
					else
						$commande->adresse = $TableAdresseClient->find($data['choix'],$id)->current()->toArray();
					$this->_redirector->gotoUrl('Shop/client/checkout-mode-livraison');
				}
				else {
					$this->view->choose = $data['choix'];
					$form->populate($data);
				}
			}
		}
		else
		{
			$commande->isLogin = "commande";
			$this->_redirector->gotoUrl('/compte');
		}

	}

	public function checkoutModeLivraisonAction(){ // Page de choix de livraison pour une commande client

		$this->view->title = "Commander";
		$commande = new Zend_Session_Namespace('commande');
		$sessionPanier = new Zend_Session_Namespace('panier');
		if(count($sessionPanier->content) == 0){
			$commande->isLogin = false;
			$this->_redirector->gotoUrl('/client/panier');
		}
		if( (Zend_Auth::getInstance()->getIdentity()) && (isset(Zend_Auth::getInstance()->getIdentity()->id_client)) )
		{
			$form = new Zend_Form();
			$submit = new Zend_Form_Element_Submit('Commander');
			$choix = new Zend_Form_Element_Radio('choix');
			$TableTransport = new Shop_Model_Transport;
			$transports = $TableTransport->fetchAll();
			$this->view->transports = $transports;
			foreach ($transports as $transport)
				$choix->addMultiOption($transport->id_transport,"");
			$choix->setRequired(true);
			$form->addElement($choix);
			$form->addElement($submit);
			$form->setMethod('post');
			$this->view->form = $form;
			$data = $this->getRequest()->getPost();

			if(($this->getRequest()->isPost()) && ($form->isValid($data)))
			{
				$commande->transport = $TableTransport->find($data['choix'])->current()->toArray();
				$this->_redirector->gotoUrl('/client/checkout-mode-paiement');
			}
		}
		else
		{
			$commande->isLogin = false;
			$this->_redirector->gotoUrl('/index/login');
		}
	}

	public function checkoutModePaiementAction(){ // Page de choix de paiement pour une commande client

		$this->view->title = "Commander";
		$commande = new Zend_Session_Namespace('commande');
		$sessionPanier = new Zend_Session_Namespace('panier');
		if(count($sessionPanier->content) == 0){
			$commande->isLogin = false;
			$this->_redirector->gotoUrl('/client/panier');
		}
		if( (Zend_Auth::getInstance()->getIdentity()) && (isset(Zend_Auth::getInstance()->getIdentity()->id_client)) )
		{
			$form = new Zend_Form();
			$submit = new Zend_Form_Element_Submit('Commander');
			$choix = new Zend_Form_Element_Radio('choix');
			$TablePaiement = new Shop_Model_Paiement;
			$paiements = $TablePaiement->fetchAll();
			$this->view->paiements = $paiements;
			foreach ($paiements as $paiement)
				$choix->addMultiOption($paiement->id_paiement,"");
			$choix->setRequired(true);
			$form->addElement($choix);
			$form->addElement($submit);
			$form->setMethod('post');
			$this->view->form = $form;
			$data = $this->getRequest()->getPost();

			if(($this->getRequest()->isPost()) && ($form->isValid($data)))
			{
				$commande->paiement = $TablePaiement->find($data['choix'])->current()->toArray();
				$this->_redirector->gotoUrl('/client/checkout-confirmation');
			}
		}
		else
		{
			$commande->isLogin = false;
			$this->_redirector->gotoUrl('/index/login');
		}

	}

	public function checkoutConfirmationAction(){ // Page de confirmation pour une commande client

		$this->view->title = "Commander";
		$commande = new Zend_Session_Namespace('commande');
		$sessionPanier = new Zend_Session_Namespace('panier');
		if(count($sessionPanier->content) == 0){
			$commande->isLogin = false;
			$this->_redirector->gotoUrl('/client/panier');
		}
		if( (Zend_Auth::getInstance()->getIdentity()) && (isset(Zend_Auth::getInstance()->getIdentity()->id_client)) )
		{
			$TableClient = new Shop_Model_Client;
			$form = new Zend_Form();
			$Texte = new Zend_Form_Element_Textarea('texte');
			$Texte->setAttrib('rows',10);
			$Texte->setAttrib('cols',100);
			$Texte->setLabel('Commentaire :');
			$submit = new Zend_Form_Element_Submit('confirmer');
			$submit->setLabel('Passer la commande');
			$form->addElement($Texte);
			$form->addElement($submit);
			$form->setMethod('post');
			$this->view->form = $form;
			$this->view->client = $TableClient->find(Zend_Auth::getInstance()->getIdentity()->id_client)->current();
			$this->view->adresse = $commande->adresse;
			$this->view->transport = $commande->transport;
			$this->view->paiement = $commande->paiement;
			$this->view->produits = $sessionPanier->content;
			$this->view->montant = $sessionPanier->sousTotal;
			$data = $this->getRequest()->getPost();

			if(($this->getRequest()->isPost()) && ($form->isValid($data)))
			{
				$TableCommande = new Shop_Model_Commande;
				$NewCommande = $TableCommande->createRow();
				$NewCommande->id_client = Zend_Auth::getInstance()->getIdentity()->id_client;
				$NewCommande->montant = $sessionPanier->sousTotal+$commande->transport['prix'];
				$NewCommande->Islivre = 0;
				$NewCommande->commentaire = $data['texte'];
				$NewCommande->id_paiement = $commande->paiement['id_paiement'];
				$NewCommande->id_transport = $commande->transport['id_transport'];
				$NewCommande->id_adresse = $commande->adresse['id_adresse'];

				$TableCommandeProduit = new Shop_Model_CommandeProduit();
				$IdCommande = $NewCommande->save();
				$TableProduit = new Shop_Model_Produit;
				foreach ($sessionPanier->content as $id => $quantite){
					$commandeProduit = $TableCommandeProduit->createRow();
					$commandeProduit->id_commande = $IdCommande;
					$commandeProduit->id_produit = $id;
					$commandeProduit->quantite = $quantite;
					$commandeProduit->save();
					$produit = $TableProduit->find($id)->current();
					if($produit != NULL){
						$produit->quantite -= $quantite;
						$produit->save();
					}
				}
				$sessionPanier->unsetAll();
				$commande->id_commande = $IdCommande;
				$this->_redirector->gotoUrl('/client/commande-confirmer');
			}
		}
		else
		{
			$commande->isLogin = false;
			$this->_redirector->gotoUrl('/index/login');
		}

	}

	public function commandeConfirmerAction(){ // Page de commande confirmer pour une commande client

		$this->view->title = "Commander";
		$commande = new Zend_Session_Namespace('commande');
		if($commande->id_commande != NULL){
			$this->view->numeroCommande = $commande->id_commande;
			$TableClient = new Shop_Model_Client();
			$client = $TableClient->find(Zend_Auth::getInstance()->getIdentity()->id_client)->current();
			$TableCommande = new Shop_Model_Commande;

			$TableTransport = new Shop_Model_Transport;
			$TablePaiement = new Shop_Model_Paiement;
			$TableAdresseCLient = new Shop_Model_AdresseClient;
			$commande1 = $TableCommande->find($commande->id_commande)->current();
			$transport = $TableTransport->find($commande1->id_transport)->current();
			$paiement = $TablePaiement->find($commande1->id_paiement)->current();
			if($client != NULL)
				$adresse = $TableAdresseCLient->fetchRow($TableAdresseCLient->select()->where('id_client=?',$client->id_client)->where('id_adresse=?',$commande->adresse['id_adresse']));
			$commandeProduits = $commande1->findDependentRowset('CommandeProduit');

			$mail = new Zend_Mail('UTF-8');
			$mail->setBodyHtml($this->view->partial('email/commande.phtml',
					array('commande1'=>$commande1,
							'transport'=>$transport,
							'paiement'=>$paiement,
							'commandeProduits'=>$commandeProduits,
							'client'=>$client,
							'adresse'=>$adresse)));
			$mail->addTo($client->mail, $client->prenom." ".$client->nom);
			$mail->setSubject("Confirmation commande ".$commande->id_commande);
			if($this->view->parametre->email != NULL){
				$emailAdmin = $this->view->parametre->email;
				$mail->addCc($emailAdmin);
				$pass = $this->view->parametre->password;
				$nomSite = $this->view->parametre->site;
				$mail->setFrom($emailAdmin, $nomSite);
				$transport = new Zend_Mail_Transport_Smtp('mailx.u-picardie.fr', array('port' => '25', 'username' => $emailAdmin, 'password' => $pass));
				//$transport = new Zend_Mail_Transport_Smtp('smtp.gmail.com', array('auth'=>'login', 'ssl'=>'ssl', 'port' => '465', 'username' => $emailAdmin, 'password' => $pass));
					

				try {
					$mail->send($transport);
				} catch (Exception $e) {
				}
			}
			else{
				$message = "<div id='message_nok'><label>Commande ".$Commande->id_commande." livré (Email envoyé au client).</label></div>";
			}

			$commande->unsetAll();
		}
		else
			$this->_redirector->gotoUrl('');

	}

	public function init(){

		$this->view->messages = $this->_helper->FlashMessenger->getMessages();
		$this->_redirector = $this->_helper->getHelper('Redirector');
		$this->_helper->layout->setLayout('default');

		$TableParametre = new Shop_Model_Parametre();
		$Parametre = $TableParametre->fetchRow();
		$this->view->parametre = $Parametre;
		$this->view->nbClient = $Parametre->nbProduits;
		$this->view->nbCommande = $Parametre->nbElements;
		$SessionRole = new Zend_Session_Namespace('Role');
		$acl = new Application_Acl_Acl();
		if(!($acl->isAllowed($SessionRole->Role,$this->getRequest()->getControllerName(),$this->getRequest()->getActionName())))
			$this->_redirector->gotoUrl('accueil');

	}
}