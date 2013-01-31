<?php
class Shop_ProduitController extends Zend_Controller_Action
{

	public function listeAction(){ // Page Liste vols (coté administrateur)
		$this->view->title = "Catalogue des vols"; // Attribution du titre de la page
		$tableVol = new Vol();

		if($this->getRequest()->getParam('orderBy')) // On récupère l'ordre dans les paramètres
			$orderBy = $this->getRequest()->getParam('orderBy');
		else
			$orderBy = "Ligne_Asc"; // Sinon on l'initialise

		$requete = $tableVol->select()->setIntegrityCheck(false)->from(array('v'=>'vol'))
		->join(array('l'=>'ligne'), 'v.numero_ligne = l.numero_ligne')
		->join(array('ad'=>'aeroport'),'ad.id_aeroport = l.id_aeroport_depart',array('ad.nom as aeroportDepart','ad.id_aeroport'))
		->join(array('aa'=>'aeroport'),'aa.id_aeroport = l.id_aeroport_arrivee',array('aa.nom as aeroportArrivee','aa.id_aeroport'))
		->joinLeft(array('av'=>'avion'),'av.id_avion = v.id_avion',array('av.nb_places'))
		->joinLeft(array('r'=>'reservation'),'(r.numero_ligne = v.numero_ligne) and (r.id_vol = v.id_vol)',array('SUM(r.nbreservation) as nbreservations'))
		->group('v.numero_ligne')
		->group('v.id_vol');

		if($this->getRequest()->getParam('mode'))
			$mode = $this->getRequest()->getParam('mode');
		else
			$mode = "now_futur";

		switch ($mode)
		{
			case "now_futur" :$requete->where('v.date_depart >=?',Zend_Date::now()->get('yyyy-MM-dd')); break;
			case "precedent" : $requete->where('v.date_depart <?',Zend_Date::now()->get('yyyy-MM-dd')); break;
			case "attente" : $requete->where('v.date_arrivee <=?',Zend_Date::now()->get('yyyy-MM-dd'))->where('v.is_arrive=?',0); break;
		}

		switch ($orderBy) // Selon la valeur de $orderBy, les vols seront affiché differemment
		{
			case "Id_Asc": $requete->order("v.id_vol asc"); break;
			case "Id_Desc": $requete->order("v.id_vol desc"); break;
			case "Ligne_Asc": $requete->order("v.numero_ligne asc"); break;
			case "Ligne_Desc": $requete->order("v.numero_ligne desc"); break;
			case "AeroDepart_Asc": $requete->order("aeroportDepart asc"); break;
			case "AeroDepart_Desc": $requete->order("aeroportDepart desc"); break;
			case "AeroArrive_Asc": $requete->order("aeroportArrivee asc"); break;
			case "AeroArrive_Desc": $requete->order("aeroportArrivee desc"); break;
			case "DateDepart_Asc": $requete->order("date_depart asc"); break;
			case "DateDepart_Desc": $requete->order("date_depart desc"); break;
			case "DateArrivee_Asc": $requete->order("date_arrivee asc"); break;
			case "DateArrivee_Desc": $requete->order("date_arrivee desc"); break;
			case "RestantesPlaces_Asc": $requete->order("nbreservations asc"); break;
			case "RestantesPlaces_Desc": $requete->order("nbreservations desc"); break;
		}

		/* La fonction orderColumns dans Application_Tableau_OrderColumn permet de créer les liens pour trier
		 * les elements comme l'on souhaite
		* */
		$this->view->HeadIdVol = Application_Tableau_OrderColumn::orderColumns($this, "Id",$orderBy,"idLigneVol","Id");
		$this->view->HeadIdLigne = Application_Tableau_OrderColumn::orderColumns($this,"Ligne",$orderBy,"idLigneVol","Numero");
		$this->view->HeadAeroDepart = Application_Tableau_OrderColumn::orderColumns($this,"AeroDepart",$orderBy,"designationAeroportVol","Aéroport de Départ");
		$this->view->HeadAeroArrive = Application_Tableau_OrderColumn::orderColumns($this,"AeroArrive",$orderBy,"designationAeroportVol","Aéroport d'arrivé");
		$this->view->HeadDateDepart= Application_Tableau_OrderColumn::orderColumns($this,"DateDepart",$orderBy,"dateVol","Date départ");
		$this->view->HeadDateArrivee = Application_Tableau_OrderColumn::orderColumns($this,"DateArrivee",$orderBy,"dateVol","Date arrivée");
		$this->view->HeadTotalPlaces= Application_Tableau_OrderColumn::orderColumns($this,"TotalPlaces",$orderBy,"quantiteVol","Places Restantes");
		$this->view->HeadEtatPlaces = Application_Tableau_OrderColumn::orderColumns($this,"RestantesPlaces",$orderBy,"etatVol","Etat");

		$vols = $tableVol->fetchAll($requete);
		$this->view->order = $orderBy;
		$paginator = Zend_Paginator::factory($vols); // Zend_Paginator permet d'indexer les pages
		$paginator->setItemCountPerPage($this->view->nbProduits);
		$paginator->setCurrentPageNumber($this->getRequest()->getParam('page'));
		$this->view->pagination = $this->view->paginationControl($paginator, 'Sliding', 'pagination.phtml',array("param"=>$this->getAllParams()));
		$this->view->paginator = $paginator;

	}

	public function catalogueAction(){ // Page de catalogue produit

		$tableAeroport = new Aeroport;
		$formRecherche = new RechercheLigne();
		$formRecherche->setMethod("POST");
		$formRecherche->getElement('Depart')->setRequired(true);
		$formRecherche->getElement('aeroportDepart')->setRequired(true);
		$formRecherche->getElement('Arrivee')->setRequired(true);
		$formRecherche->getElement('aeroportArrivee')->setRequired(true);
		$formRecherche->getElement('dateDepart')->setRequired(true);


		$this->view->title = "Reserver un billet"; // Attribution du titre de la page
		$this->_helper->layout->setLayout('categories');
		$tableVol = new Vol();
		$requete = $tableVol->select()->setIntegrityCheck(false)->from(array('v'=>'vol'))
		->join(array('l'=>'ligne'), 'v.numero_ligne = l.numero_ligne')
		->join(array('ad'=>'aeroport'),'ad.id_aeroport = l.id_aeroport_depart',array('ad.nom as aeroportDepart','ad.id_aeroport'))
		->join(array('aa'=>'aeroport'),'aa.id_aeroport = l.id_aeroport_arrivee',array('aa.nom as aeroportArrivee','aa.id_aeroport'));

		$AeroportDepart = $formRecherche->getElement('aeroportDepart');

		if($this->getParam('aeroportDepart')){
			$requete1 = $tableAeroport->select()
			->setIntegrityCheck(false)
			->from(array('ae'=>'aeroport'),array('ae.nom','ae.code_ville','ae.id_aeroport'))
			->join(array('v'=>'ville'),'v.code_ville = ae.code_ville',array('v.code_pays'))
			->where('code_pays=?',$this->getParam('Depart'));
			$aeroports = $tableAeroport->fetchAll($requete1);
			$AeroportDepart->addMultiOption("0","Choisissez l'aéroport");
			$AeroportDepart->setAttrib("disable",array("0"));
			foreach($aeroports as $aeroport)
				$AeroportDepart->addMultiOption($aeroport->id_aeroport,$aeroport->nom);
			$AeroportDepart->setValue($this->getParam('Depart'));
		}

		if($this->getParam('aeroportArrivee')){
			$AeroportArrivee = $formRecherche->getElement('aeroportArrivee');
			$requete2 = $tableAeroport->select()
			->setIntegrityCheck(false)
			->from(array('ae'=>'aeroport'),array('ae.nom','ae.code_ville','ae.id_aeroport'))
			->join(array('v'=>'ville'),'v.code_ville = ae.code_ville',array('v.code_pays'))
			->where('code_pays=?',$this->getParam('Arrivee'));
			$aeroports = $tableAeroport->fetchAll($requete2);
			$AeroportArrivee->addMultiOption("0","Choisissez l'aéroport");
			$AeroportArrivee->setAttrib("disable",array("0"));
			foreach($aeroports as $aeroport)
				$AeroportArrivee->addMultiOption($aeroport->id_aeroport,$aeroport->nom);
			$AeroportArrivee->setValue($this->getParam('Arrivee'));

		}

		$data = $this->getRequest()->getPost();
		if($this->getRequest()->isPost($data)){
			if($formRecherche->isValid($data)){
				$dateDepart = new Zend_Date($this->getParam('dateDepart'), 'dd-MM-yy');

				$requete->where('date_depart >= ?',$dateDepart->get('yyyy-MM-dd'))
				->where('date_arrivee <= ?',$dateDepart->addDay(7)->get('yyyy-MM-dd'))
				->where('id_aeroport_depart = ?',$this->getParam('aeroportDepart'))
				->where('id_aeroport_arrivee = ?',$this->getParam('aeroportArrivee'))->order('date_depart');
				$this->view->valid = true;
			}
			else
			{
				$this->view->valid = false;
			}
		}

		$formRecherche->populate($this->getRequest()->getPost());
		$this->view->formRecherche = $formRecherche;
		$paginator = Zend_Paginator::factory($tableVol->fetchAll($requete));
		$paginator->setItemCountPerPage($this->view->nbElements);
		$paginator->setCurrentPageNumber($this->getRequest()->getParam('page'));
		$this->view->pagination = $this->view->paginationControl($paginator, 'Sliding', 'pagination.phtml',array("param"=>$this->getAllParams()));
		$this->view->paginator = $paginator;

	}

	public function ficheAction(){ // Fiche d'un produit

		$form = new Shop_Form_AjoutProduitPanier;
		$panier = new Zend_Session_Namespace('panier');
		if (!($this->getRequest()->getParam('layout')))
			$this->_helper->layout->setLayout('categories');
		$TableVol = new Vol();
		$ids = explode("_",$this->getRequest()->getParam('id'));
		$requete = $TableVol->select()->setIntegrityCheck(false)->from(array('v'=>'vol'))
		->join(array('l'=>'ligne'), 'v.numero_ligne = l.numero_ligne')
		->join(array('ad'=>'aeroport'),'ad.id_aeroport = l.id_aeroport_depart',array('ad.nom as aeroportDepart','ad.id_aeroport'))
		->join(array('vd'=>'ville'),'ad.code_ville = vd.code_ville',array('vd.code_pays as code_pays_Depart','vd.code_ville'))
		->join(array('pd'=>'pays'),'pd.code_pays = vd.code_pays',array('pd.nom as pays_Depart','pd.nom'))
		->join(array('aa'=>'aeroport'),'aa.id_aeroport = l.id_aeroport_arrivee',array('aa.nom as aeroportArrivee','aa.id_aeroport'))
		->join(array('va'=>'ville'),'aa.code_ville = va.code_ville',array('va.code_pays as code_pays_Arrivee','va.code_ville'))
		->join(array('pa'=>'pays'),'pa.code_pays = va.code_pays',array('pa.nom as pays_Arrive','pa.nom'))
		->joinLeft(array('av'=>'avion'),'av.id_avion = v.id_avion',array('av.nb_places'))
		->joinLeft(array('r'=>'reservation'),'(r.numero_ligne = v.numero_ligne) and (r.id_vol = v.id_vol)',array('SUM(r.nbreservation) as nbreservations'))
		->group('v.numero_ligne')
		->group('v.id_vol')
		->where('v.id_vol=?',$ids[1])
		->where('v.numero_ligne=?',$ids[0]);
		$vol = $TableVol->fetchRow($requete);
		$navigation = Zend_Registry::get('navigation');

		if($vol != NULL){
			if($this->getRequest()->isPost())
			{
				$data = $this->getRequest()->getPost();
				if($form->isValid($data))
				{

					$panier->id_vol = $ids[1];
					$panier->numero_ligne = $ids[0];
					if(($vol->nb_places - $vol->nbreservations) >= $data['quantite']){ // quantite Ok
						$panier->quantite = $data['quantite'];
					}
					else
					{ // quantite trop grande
						$panier->quantite = $vol->nb_places - $vol->nbreservations;
						$form->getElement('quantite')->addError("Maximum : ".($vol->nb_places - $vol->nbreservations));
						$form->getElement('quantite')->setValue($vol->nb_places - $vol->nbreservations);
					}
				}
			}

			$navigation->findOneBy("id",$vol->numero_ligne."_".$vol->id_vol)->setActive(true);

			if (($this->getRequest()->getParam('layout')))
				$form->getElement('quantite')->setValue($vol->quantite);
			$this->view->vol = $vol;
			$form->getElement('id')->setValue($this->getRequest()->getParam('id'));
			$this->view->form = $form;
		}
		else
			$this->_redirector->gotoUrl('liste_produit');

	}

	public function init(){

		$this->_helper->layout->setLayout('administration'); // Layout administration de base
		$this->view->messages = $this->_helper->FlashMessenger->getMessages(); // Création des messages que l'on affiche dans certaines pages
		$this->_redirector = $this->_helper->getHelper('Redirector');

		$TableParametre = new Shop_Model_Parametre();
		$Parametre = $TableParametre->fetchRow();
		$this->view->nbProduits = $Parametre->nbProduits;
		$this->view->nbElements = $Parametre->nbElements;

		$SessionRole = new Zend_Session_Namespace('Role');  // Récupération de la session Role (definit dans le bootsrap)
		$acl = new Aeroport_LibraryAcl();
		if(!($acl->isAllowed($SessionRole->id_service,'Shop/'.$this->getRequest()->getControllerName(),$this->getRequest()->getActionName()))) // Si l'utilisateur n'a pas le droit d'acceder à cette page, on le redirige vers une page d'erreur
			$this->_redirector->gotoUrl('accueil');
	}

}