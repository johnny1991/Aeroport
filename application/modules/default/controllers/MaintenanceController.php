<?php
class MaintenanceController extends Zend_Controller_Action
{
	public function indexAction(){

	}

	public function consulterAvionAction(){

		$this->view->title = "Consultation des avions";
		$nbLigne = 25; //Nombre de lignes par pages
		$TableAvion = new Avion;

		if($this->getRequest()->getParam('orderBy'))
			$orderBy = $this->getRequest()->getParam('orderBy');
		else
			$orderBy = "Revision_Asc";

		$requete = $TableAvion->select();
		$this->view->order = $orderBy;
		$this->view->Id = Aeroport_Tableau_OrderColumn::orderColumns($this, "Id",$orderBy,null,"Id");
		$this->view->Type = Aeroport_Tableau_OrderColumn::orderColumns($this, "Type",$orderBy,null,"Modèle");
		$this->view->Places = Aeroport_Tableau_OrderColumn::orderColumns($this, "Places",$orderBy,null,"Nombre de places");
		$this->view->Heures = Aeroport_Tableau_OrderColumn::orderColumns($this, "Heures",$orderBy,null,"Nombres d'heures total");
		$this->view->Revision = Aeroport_Tableau_OrderColumn::orderColumns($this, "Revision",$orderBy,null,"Nombres d'heures avant dernière gde révision");
		$this->view->Disponibilite = Aeroport_Tableau_OrderColumn::orderColumns($this, "Disponibilite",$orderBy,null,"Etat");

		switch ($orderBy)
		{
			case "Id_Asc": $requete->order("id_avion asc"); break;
			case "Id_Desc": $requete->order("id_avion desc"); break;
			case "Type_Asc": $requete->order("id_type_avion asc"); break;
			case "Type_Desc": $requete->order("id_type_avion desc"); break;
			case "Places_Asc": $requete->order("nb_places asc"); break;
			case "Places_Desc": $requete->order("nb_places desc"); break;
			case "Heures_Asc": $requete->order("total_heure_vol asc"); break;
			case "Heures_Desc": $requete->order("total_heure_vol desc"); break;
			case "Revision_Asc": $requete->order("nb_heures_gd_revision asc"); break;
			case "Revision_Desc": $requete->order("nb_heures_gd_revision desc"); break;
			case "Disponibilite_Asc": $requete->order("disponibilite_avion asc"); break;
			case "Disponibilite_Desc": $requete->order("disponibilite_avion desc"); break;

		}

		$paginator = Zend_Paginator::factory($TableAvion->fetchAll($requete));
		$paginator->setItemCountPerPage($nbLigne);
		$paginator->setCurrentPageNumber($this->getRequest()->getParam('page'));
		$this->view->param=$this->getAllParams();
		$this->view->paginator = $paginator;
	}

	public function ajouterAvionAction(){
		$this->view->title = "Ajouter un avion";
		$form = new FormulaireAvion();
		if( ($this->getRequest()->isPost()) && ($form->isValid($this->getRequest()->getPost())) )
		{
			$TableAvion = new Avion();
			$avion = $TableAvion->createRow();
			$avion->id_type_avion = $form->getValue('type');
			$avion->nb_places = $form->getValue('places');
			$avion->total_heure_vol = $form->getValue('heure');
			$avion->nb_heures_gd_revision = $form->getValue('revision');
			$avion->disponibilite_avion = $form->getValue('disponibilite');
			$this->_helper->FlashMessenger("<div class='insertion-ok'><label>Insertion réussi</label></div>");
			$this->_redirector->gotoUrl('/maintenance/fiche-avion/id/'.$avion->save());
		}
		$form->populate($this->getRequest()->getPost());
		$this->view->form = $form;
	}

	public function modifierAvionAction(){
		$this->view->title = "Modifier un avion";
		$form = new FormulaireAvion();
		$form->removeElement('disponibilite');
		$TableAvion = new Avion();
		$id = $this->_getParam('id');
		$avion = $TableAvion->find($id)->current();
		$data = $this->getRequest()->getPost();

		if( ($avion != NULL) && ($id != NULL) )
		{
			if($this->getRequest()->isPost())
			{
				if($form->isValid($data))
				{
					$avion->id_type_avion = $form->getValue('type');
					$avion->nb_places = $form->getValue('places');
					$avion->total_heure_vol = $form->getValue('heure');
					$avion->nb_heures_gd_revision = $form->getValue('revision');
					$avion->disponibilite_avion = $form->getValue('disponibilite');
					$avion->save();
					$this->_helper->FlashMessenger("<div class='insertion-ok'><label>Modification réussi</label></div>");
					$this->_redirector->gotoUrl('/maintenance/fiche-avion/id/'.$id);
				}
			}
			else
					
				$data = array(
						'type' => $avion->id_type_avion,
						'places' => $avion->nb_places,
						'heure' => $avion->total_heure_vol,
						'revision' => $avion->nb_heures_gd_revision,
						'disponibilite' => $avion->disponibilite_avion);
			$form->populate($data);
			$this->view->form = $form;
		}
		else
		{
			$this->_helper->FlashMessenger("<div class='no-exist'><label>Cette avion n'existe pas</label></div>");
			$this->_redirector->gotoUrl($_SERVER["HTTP_REFERER"]);
		}
	}

	public function ficheAvionMaintenanceAction(){
		$id = $this->getParam('id');
		$this->view->title = "Consultation des maintenances pour l'avion ".$id;
		$this->view->id_avion = $id;
		$nbLigne = 25; //Nombre de lignes par pages
		$TableMaintenance = new Maintenance();
		$requete = $TableMaintenance->select()->where('id_avion=?', $id);//->order('id_maintenance asc');
		$maintenances = $TableMaintenance->fetchAll($requete);
		$doublons = array();
		foreach ($maintenances as $maintenance)
		{
			if(isset($doublons[$maintenance->id_avion]))
				$this->view->double = true;
			$doublons[$maintenance->id_avion] = true;
		}
		$paginator = Zend_Paginator::factory($maintenances);
		$paginator->setItemCountPerPage($nbLigne);
		$paginator->setCurrentPageNumber($this->getRequest()->getParam('page'));
		$this->view->param=$this->getAllParams();
		$this->view->paginator = $paginator;
		}
	
	public function ficheAvionAction(){
		$TableAvion = new Avion();
		$id = $this->_getParam('id');
		$TableMaintenance = new Maintenance();
		$currentDate = new Zend_Date();
		$requete = $TableMaintenance->select()->where('fin_prevue >?',$currentDate->get('yyyy-MM-dd'))->where('id_avion=?',$id);
		$requete1 = $TableMaintenance->select()->where('id_avion=?',$id);
		
		$TableVol = new Vol();
		$requete2 = $TableVol->select()->setIntegrityCheck(false)->from(array('v'=>'vol'))
		->joinLeft(array('l'=>'ligne'),'l.numero_ligne = v.numero_ligne',array('l.heure_depart','l.heure_arrivee'))
		
		->where('v.id_avion=?',$id);
		$this->view->vols = $TableVol->fetchAll($requete2);
		$Maintenances = $TableMaintenance->fetchAll($requete);
		$Maintenances1 = $TableMaintenance->fetchAll();
		$this->view->maintenanceAvion = $TableMaintenance->fetchAll($requete1);
		
		$this->view->title = "Fiche de l'avion ".$id;
		$avion = $TableAvion->find($id)->current();

		if( ($avion != NULL) && ($id != NULL) ){
			$this->view->avion = $avion;
			$this->view->maintenance = $Maintenances;
		}
		else
		{
			$this->_helper->FlashMessenger("<div class='no-exist'><label>Cette avion n'existe pas</label></div>");
			$this->_redirector->gotoUrl($_SERVER["HTTP_REFERER"]);
		}
		$dates1 = array();
		foreach ($Maintenances1 as $Maintenance)
		{
			$dateDebut = new Zend_Date($Maintenance->date_prevue, 'dd-MM-yy');
			$dateFin = new Zend_Date($Maintenance->fin_prevue, 'dd-MM-yy');
			$nbJours = ($dateFin->getTimestamp()-$dateDebut->getTimestamp())/86400;
			$first = true;
			for($i=0; $i<$nbJours+1;$i++)
			{
				$dateTraitement = new Zend_Date();
				if($first) $dateTraitement->set($dateDebut->get('dd/MM/yyyy')); else $dateTraitement->set($dateDebut->addDay(1)->get('dd/MM/yyyy'));
				$requete = $TableMaintenance->select()->where('date_prevue <= ?', $dateTraitement->get('yyyy-MM-dd'))->where('fin_prevue >= ?', $dateTraitement->get('yyyy-MM-dd'));
				$nombreAvionParDate = $TableMaintenance->fetchAll($requete);
				$texte = '('.$nombreAvionParDate->count().' avion';
				if($nombreAvionParDate->count()>1) $texte .='s';
				$texte .=')';
				$indexClass = $nombreAvionParDate->count();
				if($nombreAvionParDate->count() > 4)
					$indexClass = 4;
				$dates1[] = array(
						'date' => $dateTraitement->get('dd/MM/yyyy'),
						'texte' => $texte,
						'indexClass' => $indexClass);
				$first = false;
			}
		}

		$this->view->datePlanning = $dates1;
	}

	public function ajouterMaintenanceAction(){
		$id = $this->getParam('id');
		$mode = $this->getParam('mode');
		$dateDebut = new Zend_Date($this->getParam('date'),'dd-MM-yy');
		$TableMaintenance = new Maintenance();
		$currentDate = new Zend_Date();
		$requete = $TableMaintenance->select()->where('fin_prevue >?',$currentDate->get('yyyy-MM-dd'))->where('id_avion=?',$id);
		$MaintenancesDejaExistantes = $TableMaintenance->fetchAll($requete);
		foreach ($MaintenancesDejaExistantes as $MaintenancesDejaExistante){
			$MaintenancesDejaExistante->delete();
		}
		$Maintenance = $TableMaintenance->createRow();
		$Maintenance->id_avion  = $id;
		$Maintenance->date_prevue = $dateDebut->get('yyyy-MM-dd');
		if($mode=="big")
			$Maintenance->fin_prevue = $dateDebut->addDay(10)->get('yyyy-MM-dd');
		else
			$Maintenance->fin_prevue = $dateDebut->addDay(2)->get('yyyy-MM-dd');
		if($Maintenance->save())
			$message = "<div class='insertion-ok'><label>Insertion réussi</label></div>";
		else
			$message = "<div class='no-exist'><label>Erreur lors de l'enregistrement</label></div>";
		$this->_helper->FlashMessenger($message);
		$this->_redirector->gotoUrl($_SERVER["HTTP_REFERER"]);

	}

	public function supprimerAvionAction(){
		$TableAvion = new Avion();
		$id = $this->_getParam('id');
		$avion = $TableAvion->find($id)->current();

		if( ($id != NULL) && ($avion != NULL) )
		{
			$avion->delete();
			$message = "<div class='insertion-ok'><label>Suppression réussi</label></div>";
		}
		else
			$message = "<div class='no-exist'><label>Cette avion n'existe pas</label></div>";
		$this->_helper->FlashMessenger($message);
		$this->_redirector->gotoUrl($_SERVER["HTTP_REFERER"]);
	}

	public function ajouterModeleAvionAction(){
		$this->view->title = "Ajouter un modèle d'avion";
		$form = new FormulaireTypeAvion();
		if( ($this->getRequest()->isPost()) && ($form->isValid($this->getRequest()->getPost())))
		{
			$TableTypeAvion = new TypeAvion();
			$typeAvion = $TableTypeAvion->createRow();
			$typeAvion->libelle = $form->getValue('nom');
			$typeAvion->rayon_action = $form->getValue('rayon');
			$typeAvion->longueur_decollage = $form->getValue('decollage');
			$typeAvion->longueur_atterrissage = $form->getValue('atterrissage');
			$this->_helper->FlashMessenger("<div class='insertion-ok'><label>Insertion réussi</label></div>");
			$this->_redirector->gotoUrl('/maintenance/fiche-modele-avion/id/'.$typeAvion->save());
		}
		$form->populate($this->getRequest()->getPost());
		$this->view->form = $form;
	}

	public function modifierModeleAvionAction(){
		$this->view->title = "Modifier un modèle d'avion";
		$form = new FormulaireTypeAvion();
		$TableTypeAvion = new TypeAvion();
		$id = $this->_getParam('id');
		$typeAvion = $TableTypeAvion->find($id)->current();
		$data = $this->getRequest()->getPost();

		if( ($typeAvion != NULL) && ($id != NULL) )
		{
			if($this->getRequest()->isPost())
			{
				if($form->isValid($data))
				{
					$typeAvion->libelle = $form->getValue('nom');
					$typeAvion->rayon_action = $form->getValue('rayon');
					$typeAvion->longueur_decollage = $form->getValue('decollage');
					$typeAvion->longueur_atterrissage = $form->getValue('atterrissage');
					$typeAvion->save();
					$this->_helper->FlashMessenger("<div class='insertion-ok'><label>Modification réussi</label></div>");
					$this->_redirector->gotoUrl('/maintenance/fiche-modele-avion/id/'.$id);
				}
			}
			else
				$data = array(
						'nom' => $typeAvion->libelle,
						'rayon' => $typeAvion->rayon_action,
						'decollage' => $typeAvion->longueur_decollage,
						'atterrissage' => $typeAvion->longueur_atterrissage);

			$form->populate($data);
			$this->view->form = $form;
		}
		else
		{
			$this->_helper->FlashMessenger("<div class='no-exist'><label>Cette avion n'existe pas</label></div>");
			$this->_redirector->gotoUrl($_SERVER["HTTP_REFERER"]);
		}
	}

	public function supprimerModeleAvionAction(){
		$TableTypeAvion = new TypeAvion();
		$id = $this->_getParam('id');
		$typeAvion = $TableTypeAvion->find($id)->current();

		if( ($id != NULL) && ($typeAvion != NULL) )
		{
			if($typeAvion->findDependentRowset('Avion')->count() == 0){
				$typeAvion->delete();
				$message = "<div class='insertion-ok'><label>Suppression réussi</label></div>";
			}
			else
				$message = "<div class='no-exist'><label>Suppression impossible car un ou plusieurs avions correspondent à ce modèle</label></div>";
		}
		else
			$message = "<div class='no-exist'><label>Cette avion n'existe pas</label></div>";
		$this->_helper->FlashMessenger($message);
		$this->_redirector->gotoUrl($_SERVER["HTTP_REFERER"]);
	}

	public function consulterModeleAvionAction(){
		$this->view->title = "Consultation des modèles avions";
		$nbLigne = 25; //Nombre de lignes par pages
		$TableTypeAvion = new TypeAvion;

		if($this->getRequest()->getParam('orderBy'))
			$orderBy = $this->getRequest()->getParam('orderBy');
		else
			$orderBy = "Id_Asc";

		$requete = $TableTypeAvion->select();
		$this->view->order = $orderBy;
		$this->view->Id = Aeroport_Tableau_OrderColumn::orderColumns($this, "Id",$orderBy,null,"Id");
		$this->view->Nom = Aeroport_Tableau_OrderColumn::orderColumns($this, "Nom",$orderBy,null,"Nom");
		$this->view->Rayon = Aeroport_Tableau_OrderColumn::orderColumns($this, "Rayon",$orderBy,null,"Rayon d'action");
		$this->view->Decollage = Aeroport_Tableau_OrderColumn::orderColumns($this, "Decollage",$orderBy,null,"Longueur de décollage");
		$this->view->Atterrissage = Aeroport_Tableau_OrderColumn::orderColumns($this, "Atterrissage",$orderBy,null,"Longueur d'atterrissage");

		switch ($orderBy)
		{
			case "Id_Asc": $requete->order("id_type_avion asc"); break;
			case "Id_Desc": $requete->order("id_type_avion desc"); break;
			case "Nom_Asc": $requete->order("libelle asc"); break;
			case "Nom_Desc": $requete->order("libelle desc"); break;
			case "Rayon_Asc": $requete->order("rayon_action asc"); break;
			case "Rayon_Desc": $requete->order("rayon_action desc"); break;
			case "Decollage_Asc": $requete->order("longueur_decollage asc"); break;
			case "Decollage_Desc": $requete->order("longueur_decollage desc"); break;
			case "Atterrissage_Asc": $requete->order("longueur_atterrissage asc"); break;
			case "Atterrissage_Desc": $requete->order("longueur_atterrissage desc"); break;
		}

		$paginator = Zend_Paginator::factory($TableTypeAvion->fetchAll($requete));
		$paginator->setItemCountPerPage($nbLigne);
		$paginator->setCurrentPageNumber($this->getRequest()->getParam('page'));
		$this->view->param = $this->getAllParams();
		$this->view->paginator = $paginator;
	}

	public function ficheModeleAvionAction(){
		$TableTypeAvion = new TypeAvion();
		$id = $this->_getParam('id');
		$this->view->title = "Fiche du modèle d'avion ".$id;
		$typeAvion = $TableTypeAvion->find($id)->current();
		if( ($typeAvion != NULL) && ($id != NULL) )
			$this->view->typeAvion = $typeAvion;
		else
		{
			$this->_helper->FlashMessenger("<div class='no-exist'><label>Ce modèle d'avion n'existe pas</label></div>");
			$this->_redirector->gotoUrl($_SERVER["HTTP_REFERER"]);
		}
	}

	public function supprimerMaintenanceAction(){
		$this->view->title = "Supprimer une maintenance";
		$id = $this->_getParam('id');
		$TableMaintenance = new Maintenance();
		$Maintenance = $TableMaintenance->find($id)->current();
		if( ($id!=NULL) && ($Maintenance!=NULL) )
		{
			$Maintenance->delete();
			$message = "<div class='insertion-ok'><label>Suppression réussi</label></div>";
		}
		else
			$message = "<div class='no-exist'><label>Cette maintenance n'existe pas</label></div>";
		$this->_helper->FlashMessenger($message);
		$this->_redirector->gotoUrl($_SERVER["HTTP_REFERER"]);
	}

	public function planningAction(){
		$TableMaintenance = new Maintenance();

		$dates = $TableMaintenance->fetchAll();
		foreach ($dates as $date)
		{
			$dateDebut = new Zend_Date($date->date_prevue, 'dd-MM-yy');
			$dateFin = new Zend_Date($date->fin_prevue, 'dd-MM-yy');
			$nbJours = ($dateFin->getTimestamp()-$dateDebut->getTimestamp())/86400;
			$first = true;
			for($i=0; $i<$nbJours+1;$i++)
			{
				$dateTraitement = new Zend_Date();
				if($first) $dateTraitement->set($dateDebut->get('dd/MM/yyyy')); else $dateTraitement->set($dateDebut->addDay(1)->get('dd/MM/yyyy'));
				$requete = $TableMaintenance->select()->where('date_prevue <= ?', $dateTraitement->get('yyyy-MM-dd'))->where('fin_prevue >= ?', $dateTraitement->get('yyyy-MM-dd'));
				$nombreAvionParDate = $TableMaintenance->fetchAll($requete);
				$texte = '('.$nombreAvionParDate->count().' avion';
				if($nombreAvionParDate->count()>1) $texte .='s';
				$texte .=')';
				$indexClass = $nombreAvionParDate->count();
				if($nombreAvionParDate->count() > 4) $indexClass = 4;
				$dates1[] = array(
						'date' => $dateTraitement->get('dd/MM/yyyy'),
						'texte' => $texte,
						'indexClass' => $indexClass);
				$first = false;
			}
		}

		$this->view->datePlanning = $dates1;
	}

	public function planningJourAction(){

		$date = $this->getParam('date');
		$this->view->title = "Consultation des maintenances le ".$date;
		$nbLigne = 25; //Nombre de lignes par pages
		$TableMaintenance = new Maintenance();
		$dateTraitement = new Zend_Date($date,'dd-MM-yy');
		$requete = $TableMaintenance->select()->where('date_prevue <= ?', $dateTraitement->get('yyyy-MM-dd'))->where('fin_prevue >= ?', $dateTraitement->get('yyyy-MM-dd'))->order('id_avion asc');
		$maintenances = $TableMaintenance->fetchAll($requete);
		$doublons = array();
		foreach ($maintenances as $maintenance)
		{
			if(isset($doublons[$maintenance->id_avion]))
				$this->view->double = true;
			$doublons[$maintenance->id_avion] = true;
		}
		$paginator = Zend_Paginator::factory($maintenances);
		$paginator->setItemCountPerPage($nbLigne);
		$paginator->setCurrentPageNumber($this->getRequest()->getParam('page'));
		$this->view->param=$this->getAllParams();
		$this->view->paginator = $paginator;
		$this->view->date = $dateTraitement->get('dd-MM-yyyy');
	}

	public function consulterMaintenanceAction(){

		$this->view->title = "Consultation des maintenances";
		$nbLigne = 25; //Nombre de lignes par pages
		$TableAvion = new Avion;

		if($this->getRequest()->getParam('orderBy'))
			$orderBy = $this->getRequest()->getParam('orderBy');
		else
			$orderBy = "Revision_Asc";

		$requete = $TableAvion->select()
		->setIntegrityCheck(false)
		->from(array('a'=>'avion'))
		->joinLeft(array('v'=>'vol'),'v.id_avion = a.id_avion',array('v.date_depart','v.date_arrivee'))
		->joinLeft(array('l'=>'ligne'),'l.numero_ligne = v.numero_ligne',array('l.heure_depart','l.heure_arrivee'))
		->join(array('t'=>'type_avion'),'t.id_type_avion = a.id_type_avion',array('t.libelle'))
		->group('a.id_avion');

		$this->view->order = $orderBy;
		$this->view->Id = Aeroport_Tableau_OrderColumn::orderColumns($this, "Id",$orderBy,null,"Id");
		$this->view->Type = Aeroport_Tableau_OrderColumn::orderColumns($this, "Type",$orderBy,null,"Modèle");
		$this->view->Heures = Aeroport_Tableau_OrderColumn::orderColumns($this, "Heures",$orderBy,null,"Nombre d'heures total");
		$this->view->Revision = Aeroport_Tableau_OrderColumn::orderColumns($this, "Revision",$orderBy,null,"Nombre d'heures restants");
		$this->view->NbVol = Aeroport_Tableau_OrderColumn::orderColumns($this, "NbVol",$orderBy,null,"Nombre de vols restants");
		$this->view->Disponibilite = Aeroport_Tableau_OrderColumn::orderColumns($this, "Disponibilite",$orderBy,null,"Etat");

		switch ($orderBy)
		{
			case "Id_Asc": $requete->order("id_avion asc"); break;
			case "Id_Desc": $requete->order("id_avion desc"); break;
			case "Type_Asc": $requete->order("id_type_avion asc"); break;
			case "Type_Desc": $requete->order("id_type_avion desc"); break;
			case "Heures_Asc": $requete->order("total_heure_vol asc"); break;
			case "Heures_Desc": $requete->order("total_heure_vol desc"); break;
			case "Revision_Asc": $Revision = "Asc"; break;
			case "Revision_Desc": $Revision = "Desc"; break;
			case "NbVol_Asc": $NbVol = "Asc"; break;
			case "NbVol_Desc": $NbVol = "Desc"; break;
			case "Disponibilite_Asc": $requete->order("disponibilite_avion asc"); break;
			case "Disponibilite_Desc": $requete->order("disponibilite_avion desc"); break;
		}

		$TableauAvions = $TableAvion->fetchAll($requete)->toArray();

		foreach ($TableauAvions as &$TableauAvion)
		{
			try {
				$TableVol = new Vol;

				$requete1 = $TableVol
				->select()
				->setIntegrityCheck(false)
				->from(array('v'=>'vol'))
				->joinLeft(array('a'=>'avion'),'v.id_avion = a.id_avion')
				->joinLeft(array('l'=>'ligne'),'l.numero_ligne = v.numero_ligne',array('l.heure_depart','l.heure_arrivee'))
				->where('v.id_avion=?',$TableauAvion['id_avion']);
				$Vols = $TableVol->fetchAll($requete1);
				$heureInitiale = $TableauAvion['nb_heures_gd_revision'];
				$diff = 0;
				$TableauAvion['nbVol'] = count($Vols);
				foreach ($Vols as $vol)
				{
					$dateDepart = new Zend_Date($vol->date_depart.' '.$vol->heure_depart);
					$dateArrivee = new Zend_Date($vol->date_arrivee.' '.$vol->heure_arrivee);
					$diff += $dateArrivee->sub($dateDepart)->toValue()/3600;

				}
				$TableauAvion['diff'] = $heureInitiale - $diff;
			} catch (Exception $e) {
				$TableauAvion['diff'] = null;
				$TableauAvion['nbVol'] = null;
			}

		}

		if(isset($Revision)){
			if($Revision == "Asc")
				$TableauAvions = MaintenanceController::array_sort($TableauAvions, "diff","asc");
			else if($Revision == "Desc")
				$TableauAvions = MaintenanceController::array_sort($TableauAvions, "diff","desc");
		} else if(isset($NbVol))
			if($NbVol == "Asc")
			$TableauAvions = MaintenanceController::array_sort($TableauAvions, "nbVol","asc");
		else if($NbVol == "Desc")
			$TableauAvions = MaintenanceController::array_sort($TableauAvions, "nbVol","desc");

		$paginator = Zend_Paginator::factory($TableauAvions);
		$paginator->setItemCountPerPage($nbLigne);
		$paginator->setCurrentPageNumber($this->getRequest()->getParam('page'));
		$this->view->param=$this->getAllParams();
		$this->view->paginator = $paginator;

	}

	function array_sort($array, $key, $ordre)
	{
		for ($i = 0; $i < sizeof($array); $i++) {
			$sort_values[$i] = $array[$i][$key];
		}
		if($ordre == "asc")
			asort ($sort_values);
		else if($ordre == "desc")
			arsort ($sort_values);
		reset ($sort_values);

		while (list ($arr_key, $arr_val) = each ($sort_values)) {
			$sorted_arr[] = $array[$arr_key];
		}
		unset($array);
		return $sorted_arr;
	}

	public function interventionAction(){
		$this->view->title = "Intervention";
		$nbLigne = 25; //Nombre de lignes par pages
		
		$id = $this->_getParam('id');
		$this->view->id = $id;
		
		$form = new InterventionForm();
		$TableIntervention = new Intervention();
		
		$auth = Zend_Auth::getInstance();
		$nom = $auth->getIdentity()->nom;
		
		$TableAvion = new Avion();
		$id = $this->_getParam('id');
		$avion = $TableAvion->find($id)->current();
		
		$TableMaintenance = new Maintenance();
		$requeteM = $TableMaintenance->select()
		->setIntegrityCheck(false)
		->from(array('ma'=>'maintenance'))
		->where('ma.date_prevue <=?',Zend_Date::now()->get('yyyy-MM-dd'))
		->where('ma.fin_prevue >=?',Zend_Date::now()->get('yyyy-MM-dd'))
		->where('ma.id_avion=?',$id);
		$resultM = $TableMaintenance->fetchRow($requeteM);
		//Zend_Debug::dump($requeteM);
		//echo $requeteM;
		//exit();
		if( ($this->getRequest()->isPost()) && ($form->isValid($this->getRequest()->getPost())) )
		{

			$TableIntervention = new Intervention();
			$intervention = $TableIntervention->createRow();
			$intervention->login = $nom;
			$intervention->id_maintenance = $resultM->id_maintenance;
			$intervention->date_effective = Zend_Date::now()->get('yyyy-MM-dd');
			$intervention->duree_effective = $form->getValue('Duree');
			$intervention->commentaire = $form->getValue('Commentaire');
			$intervention->save();
		}
		
		$requete = $TableIntervention->select()
		->setIntegrityCheck(false)
		->from(array('i'=>'intervention'))
		->joinLeft(array('ma'=>'maintenance'), 'ma.id_maintenance = i.id_maintenance')
		->joinLeft(array('a'=>'avion'), 'a.id_avion = ma.id_avion')
		->where('ma.id_maintenance=?',$resultM->id_maintenance);
		
		$result = $TableIntervention->fetchAll($requete);
			
		$paginator = Zend_Paginator::factory($result);
		$paginator->setItemCountPerPage($nbLigne);
		$paginator->setCurrentPageNumber($this->getRequest()->getParam('page'));
		$this->view->param=$this->getAllParams();
		$this->view->paginator = $paginator;
		
		$this->view->form = $form;
	
	}
	
	public function consulterInterventionAction(){
		$this->view->title = "Consultation des interventions";
		$nbLigne = 25; //Nombre de lignes par pages
		
		$TableAvion = new Avion();
		$id = $this->_getParam('id');

		
		$TableIntervention = new Intervention();
		$requete = $TableIntervention->select()
		->setIntegrityCheck(false)
		->from(array('i'=>'intervention'))
		->joinLeft(array('ma'=>'maintenance'), 'ma.id_maintenance = i.id_maintenance')
		->joinLeft(array('a'=>'avion'), 'a.id_avion = ma.id_avion')
		->where('ma.id_maintenance=?',$id);
		
		$this->view->id = $id;
		
		//echo $requete;exit();
		$result = $TableIntervention->fetchAll($requete);
		
		
		
		$paginator = Zend_Paginator::factory($result);
		$paginator->setItemCountPerPage($nbLigne);
		$paginator->setCurrentPageNumber($this->getRequest()->getParam('page'));
		$this->view->param=$this->getAllParams();
		$this->view->paginator = $paginator;
	}
	
	public function init(){ //OK
		$this->view->messages = $this->_helper->FlashMessenger->getMessages();
		$this->_redirector = $this->_helper->getHelper('Redirector');

		$this->view->headLink()->appendStylesheet('/css/calendar.jquery.css');
		$this->view->headScript()->appendFile('/js/calendar.jquery.js');
		$this->view->headScript()->appendFile('/js/jquery-ui-sliderAccess.js');
		$this->view->headScript()->appendFile('/js/jquery-ui-timepicker-addon.js');
		$this->view->headLink()->appendStylesheet('/css/jquery-ui-timepicker-addon.css');
		$this->view->headLink()->appendStylesheet('/css/jquery-ui-1.8.23.css');
		
		$acl = new Aeroport_LibraryAcl();
		$SRole = new Zend_Session_Namespace('Role');
		if(!$acl->isAllowed($SRole->id_service, $this->getRequest()->getControllerName(), $this->getRequest()->getActionName()))
		{
			$this->_redirector->gotoUrl('/');
		}
	}
}