<?php
class Shop_CommandeController extends Zend_Controller_Action
{
	public function listeAction(){ // Liste des Commandes

		$this->view->title = "Catalogue commande";
		$TableReservation = new Reservation();
		$requete = $TableReservation
		->select()
		->from(array('r'=>'reservation'))
		
		->setIntegrityCheck(false)
		->joinLeft(array('cl'=>'Client'),'cl.id_client=r.id_client',array('cl.nom','cl.prenom'))
		->joinLeft(array('l'=>'ligne'),'r.numero_ligne=l.numero_ligne',array('l.tarif'))
		->joinLeft(array('v'=>'vol'),'r.id_vol=v.id_vol',array('v.tarif_effectif'))
		->group('r.id_reservation')
		
		
		/*
		 ->from(array('c'=>'Commande'))
		->joinLeft(array('cp'=>'CommandeProduit'), 'cp.id_commande=c.id_commande',array("num"=>"COUNT(cp.id_produit)",'cp.id_produit'))
		->group('c.id_commande')
		*/
		;
		
		echo $requete;
		$nbCommande = $this->view->nbCommande;

		/*if($this->getRequest()->getParam('livre') == 'livre')
			$this->view->livre = false;
		else
		{
			$requete->where('c.Islivre=?',0);
			$this->view->livre = true;
		}*/

	/*	if($this->getRequest()->getParam('orderBy'))
			$orderBy = $this->getRequest()->getParam('orderBy');
		else
			$orderBy = "Id_Asc";

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
			case "Etat_Asc": $requete->order("c.Islivre asc"); break;
			case "Etat_Desc": $requete->order("c.Islivre desc"); break;
		}*/

	/*	$this->view->HeadId = Application_Tableau_OrderColumn::orderColumns($this, "Id",$orderBy,"idLigneCommande","Id");
		$this->view->HeadNom = Application_Tableau_OrderColumn::orderColumns($this,"Nom",$orderBy,"nomLigneCommande","Nom client");
		$this->view->HeadPrenom = Application_Tableau_OrderColumn::orderColumns($this,"Prenom",$orderBy,"prenomLigneCommande","Prénom client");
		$this->view->HeadNombre = Application_Tableau_OrderColumn::orderColumns($this,"Nombre",$orderBy,"nombreLigneCommande","Nb de produits");
		$this->view->HeadMontant = Application_Tableau_OrderColumn::orderColumns($this,"Montant",$orderBy,"montantLigneCommande","Montant");
		$this->view->HeadDate = Application_Tableau_OrderColumn::orderColumns($this,"Date",$orderBy,"dateLigneCommande","Date et Heure");
		$this->view->HeadLivre = Application_Tableau_OrderColumn::orderColumns($this,"Etat",$orderBy,"livreLigneCommande","Etat");
*/
		$Commandes = $TableReservation->fetchAll($requete);
		//$this->view->order = $orderBy;
		$paginator = Zend_Paginator::factory($Commandes);
		$paginator->setItemCountPerPage($nbCommande);
		$paginator->setCurrentPageNumber($this->getRequest()->getParam('page'));
		$this->view->pagination = $this->view->paginationControl($paginator, 'Sliding', 'pagination.phtml',array("param"=>$this->getAllParams()));
		$this->view->paginator = $paginator;

	}

	public function ficheAction(){ // Fiche commandes

		$this->view->title = "Fiche commande";
		$id = $this->getRequest()->getParam('id');
		$TableCommande = new Shop_Model_Commande;
		$TableAdresseCLient = new Shop_Model_AdresseClient;
		$Commande = $TableCommande->find($id)->current();
		$TableTransport = new Shop_Model_Transport;
		$this->view->transport = $TableTransport->find($Commande->id_transport)->current();
		$TablePaiement = new Shop_Model_Paiement;
		$this->view->paiement = $TablePaiement->find($Commande->id_paiement)->current();
		if(($id != NULL) && ($Commande))
		{
			$this->view->client = $Commande->findParentRow('Client');
			if($this->view->client != NULL)
				$this->view->adresse = $TableAdresseCLient->fetchRow($TableAdresseCLient->select()->where('id_client=?',$this->view->client->id_client)->where('id_adresse=?',$Commande->id_adresse));
			$this->view->commande = $Commande;
			$this->view->commandeProduits = $Commande->findDependentRowset('CommandeProduit');
		}
		else
			$this->_redirector->gotoUrl('liste_commandes');

	}

	public function envoyeAction(){ // Page de commande envoyé

		$id = $this->getRequest()->getParam('id');
		$TableCommande = new Shop_Model_Commande;
		$TableProduit = new Shop_Model_Produit;
		$Commande = $TableCommande->find($id)->current();
		if(($id != NULL) && ($Commande))
		{
			if($this->getRequest()->getParam('islivre') == 'livre')
			{
				$CommandeProduits = $Commande->findDependentRowset('CommandeProduit');
				$Commande->Islivre = true;
				$nomSite = $this->view->parametre->site;
				$TableClient = new Shop_Model_Client();
				$client = $TableClient->find($Commande->id_client)->current();

				$TableTransport = new Shop_Model_Transport;
				$TablePaiement = new Shop_Model_Paiement;
				$TableAdresseCLient = new Shop_Model_AdresseClient;
				$transport = $TableTransport->find($Commande->id_transport)->current();
				$paiement = $TablePaiement->find($Commande->id_paiement)->current();
				if($client != NULL)
					$adresse = $TableAdresseCLient->fetchRow($TableAdresseCLient->select()->where('id_client=?',$client->id_client)->where('id_adresse=?',$Commande->id_adresse));
				$commandeProduits = $Commande->findDependentRowset('CommandeProduit');

				$mail = new Zend_Mail('UTF-8');
				$mail->setBodyHtml($this->view->partial('email/confirmer.phtml',
						array('commande1'=>$Commande,
								'transport'=>$transport,
								'paiement'=>$paiement,
								'commandeProduits'=>$commandeProduits,
								'client'=>$client,
								'adresse'=>$adresse)));
				$mail->addTo($client->mail, $client->prenom." ".$client->nom);
				$mail->setSubject("Votre commande ".$Commande->id_commande." vient d'être envoyée");
				if($this->view->parametre->email != NULL){
					$emailAdmin = $this->view->parametre->email;
					$pass = $this->view->parametre->password;
					$nomSite = $this->view->parametre->site;
					$mail->setFrom($emailAdmin, $nomSite);
					
					$transport = new Zend_Mail_Transport_Smtp('mailx.u-picardie.fr', array('port' => '25', 'username' => $emailAdmin, 'password' => $pass));
					//$transport = new Zend_Mail_Transport_Smtp('smtp.gmail.com', array('auth'=>'login', 'ssl'=>'ssl', 'port' => '465', 'username' => $emailAdmin, 'password' => $pass));
						
					try {
						$mail->send($transport);
						$message = "<div id='message_ok'><label>Commande ".$Commande->id_commande." livré (Email envoyé au client).</label></div>";

					} catch (Exception $e) {
						$message = "<div id='message_nok'><label>Commande ".$Commande->id_commande." livré (Email non envoyé au client).<BR> Erreur : ".$e->getMessage()."</label></div>";
					}
				}
				else{
					$message = "<div id='message_nok'><label>Commande ".$Commande->id_commande." livré (Email non envoyé au client).</label></div>";
				}
			}
			else if($this->getRequest()->getParam('islivre') == 'nonlivre')
			{
				$CommandeProduits = $Commande->findDependentRowset('CommandeProduit');
				$Commande->Islivre = false;
				$message = "<div id='message_ok'><label>Commande ".$Commande->id_commande." affiché comme non livré .</label></div>";
			}
			else
				$this->_redirector->gotoUrl($_SERVER["HTTP_REFERER"]);

			$this->_helper->FlashMessenger($message);
			$Commande->save();
		}

		$this->_redirector->gotoUrl($_SERVER["HTTP_REFERER"]);

	}

	public function init(){

		$this->_helper->layout->setLayout('administration');
		$this->view->messages = $this->_helper->FlashMessenger->getMessages();
		$this->_redirector = $this->_helper->getHelper('Redirector');

		$TableParametre = new Shop_Model_Parametre();
		$Parametre = $TableParametre->fetchRow();
		$this->view->parametre = $Parametre;
		$this->view->nbCommande = $Parametre->nbProduits;

		$SessionRole = new Zend_Session_Namespace('Role');
		$acl = new Application_Acl_Acl();
		if(!($acl->isAllowed($SessionRole->Role,$this->getRequest()->getControllerName(),$this->getRequest()->getActionName())))
			$this->_redirector->gotoUrl('');

	}
}
