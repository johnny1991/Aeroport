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
		->joinLeft(array('cl'=>'client'),'cl.id_client=r.id_client',array('cl.nom','cl.prenom'))
		->group('r.id_reservation');

		$nbCommande = $this->view->nbCommande;

		if($this->getRequest()->getParam('orderBy'))
			$orderBy = $this->getRequest()->getParam('orderBy');
		else
			$orderBy = "Id_Asc";

		switch ($orderBy)
		{
			case "Id_Asc": $requete->order("r.id_reservation asc"); break;
			case "Id_Desc": $requete->order("r.id_reservation desc"); break;
			case "Vol_Asc": $requete->order("v.id_vol asc"); break;
			case "Vol_Desc": $requete->order("v.id_vol desc"); break;
			case "Ligne_Asc": $requete->order("v.numero_ligne asc"); break;
			case "Ligne_Desc": $requete->order("v.numero_ligne desc"); break;
			case "Client_Asc": $requete->order("cl.nom asc"); break;
			case "Client_Desc": $requete->order("cl.nom desc"); break;
			case "Quantite_Asc": $requete->order("r.nbreservation asc"); break;
			case "Quantite_Desc": $requete->order("r.nbreservation desc"); break;
			case "Montant_Asc": $requete->order("r.montant asc"); break;
			case "Montant_Desc": $requete->order("r.montant desc"); break;
			case "Date_Asc": $requete->order("r.date asc"); break;
			case "Date_Desc": $requete->order("r.date desc"); break;
			case "Etat_Asc": $requete->order("r.is_valid asc"); break;
			case "Etat_Desc": $requete->order("r.is_valid desc"); break;
		}

		$this->view->HeadId = Application_Tableau_OrderColumn::orderColumns($this, "Id",$orderBy,"idLigneCommande","Id");
		$this->view->HeadVol = Application_Tableau_OrderColumn::orderColumns($this,"Vol",$orderBy,"idLigneCommande","Vol");
		$this->view->HeadLigne = Application_Tableau_OrderColumn::orderColumns($this,"Ligne",$orderBy,"idLigneCommande","Ligne");
		$this->view->HeadClient = Application_Tableau_OrderColumn::orderColumns($this,"Client",$orderBy,"nombreLigneCommande","Client");
		$this->view->HeadQuantite = Application_Tableau_OrderColumn::orderColumns($this,"Quantite",$orderBy,"quantiteLigneCommande","Réservations");
		$this->view->HeadMontant = Application_Tableau_OrderColumn::orderColumns($this,"Montant",$orderBy,"montantLigneCommande","Montant");
		$this->view->HeadDate = Application_Tableau_OrderColumn::orderColumns($this,"Date",$orderBy,"dateLigneCommande","Temps écoulé");
		$this->view->HeadEtat = Application_Tableau_OrderColumn::orderColumns($this,"Etat",$orderBy,"livreLigneCommande","Etat");

		$Reservations = $TableReservation->fetchAll($requete);
		$this->view->order = $orderBy;
		$paginator = Zend_Paginator::factory($Reservations);
		$paginator->setItemCountPerPage($nbCommande);
		$paginator->setCurrentPageNumber($this->getRequest()->getParam('page'));
		$this->view->pagination = $this->view->paginationControl($paginator, 'Sliding', 'pagination.phtml',array("param"=>$this->getAllParams()));
		$this->view->paginator = $paginator;

	}

	public function ficheAction(){ // Fiche commandes

		$this->view->title = "Fiche commande";
		$id = $this->getRequest()->getParam('id');
		$TableReservation = new Reservation();
		$TableAdresseCLient = new Shop_Model_AdresseClient;
		$Reservation = $TableReservation->find($id)->current();
		$TablePaiement = new Shop_Model_Paiement;
		$this->view->paiement = $TablePaiement->find($Reservation->id_paiement)->current();
		if(($id != NULL) && ($Reservation))
		{
			$this->view->client = $Reservation->findParentRow('Shop_Model_Client');
			if($this->view->client != NULL)
				$this->view->adresse = $TableAdresseCLient->fetchRow($TableAdresseCLient->select()->where('id_client=?',$this->view->client->id_client)->where('id_adresse=?',$Reservation->id_adresse));
			$this->view->reservation = $Reservation;
		}
		else
			$this->_redirector->gotoUrl('liste_commandes');

	}

	public function envoyeAction(){ // Page de commande envoyé

		$id = $this->getRequest()->getParam('id');
		$TableReservation = new Reservation();
		$Reservation = $TableReservation->find($id)->current();
		if(($id != NULL) && ($Reservation))
		{
			if($this->getRequest()->getParam('is_valid') == 'oui')
			{
				$Reservation->is_valid = true;
				$nomSite = $this->view->parametre->site;
				$TableClient = new Shop_Model_Client();
				$client = $TableClient->find($Reservation->id_client)->current();

				$TablePaiement = new Shop_Model_Paiement;
				$TableAdresseCLient = new Shop_Model_AdresseClient;
				$paiement = $TablePaiement->find($Reservation->id_paiement)->current();
				if($client != NULL)
					$adresse = $TableAdresseCLient->fetchRow($TableAdresseCLient->select()->where('id_client=?',$client->id_client)->where('id_adresse=?',$Reservation->id_adresse));

				$mail = new Zend_Mail('UTF-8');
				$mail->setBodyHtml($this->view->partial('email/confirmer.phtml',
						array('reservation'=>$Reservation,
								'paiement'=>$paiement,
								'client'=>$client,
								'adresse'=>$adresse)));
				$mail->addTo($client->mail, $client->prenom." ".$client->nom);
				$mail->setSubject("Votre reservation ".$Reservation->id_reservation." vient d'être envoyée");
				if($this->view->parametre->email != NULL){
					$emailAdmin = $this->view->parametre->email;
					$pass = $this->view->parametre->password;
					$nomSite = $this->view->parametre->site;
					$mail->setFrom($emailAdmin, $nomSite);

					//$transport = new Zend_Mail_Transport_Smtp('mailx.u-picardie.fr', array('port' => '25', 'username' => $emailAdmin, 'password' => $pass));
					$transport = new Zend_Mail_Transport_Smtp('smtp.gmail.com', array('auth'=>'login', 'ssl'=>'ssl', 'port' => '465', 'username' => $emailAdmin, 'password' => $pass));

					try {
						$mail->send($transport);
						$message = "<div id='message_ok'><label>Reservation ".$Reservation->id_reservation." livré (Email envoyé au client).</label></div>";

					} catch (Exception $e) {
						$message = "<div id='message_nok'><label>Reservation ".$Reservation->id_reservation." livré (Email non envoyé au client).<BR> Erreur : ".$e->getMessage()."</label></div>";
					}
				}
				else{
					$message = "<div id='message_nok'><label>Reservation ".$Reservation->id_reservation." livré (Email non envoyé au client).</label></div>";
				}
			}
			else if($this->getRequest()->getParam('is_valid') == 'non')
			{
				$Reservation->id_valid = false;
				$message = "<div id='message_ok'><label>Reservation ".$Reservation->id_reservation." affiché comme non livré .</label></div>";
			}
			else
				$this->_redirector->gotoUrl('liste_commandes');

			$this->_helper->FlashMessenger($message);
			$Reservation->save();
		}

		$this->_redirector->gotoUrl('liste_commandes');

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
		$acl = new Aeroport_LibraryAcl();
		if(!($acl->isAllowed($SessionRole->id_service,'Shop/'.$this->getRequest()->getControllerName(),$this->getRequest()->getActionName())))
			$this->_redirector->gotoUrl('accueil');

	}
}
