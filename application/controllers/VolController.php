<?php
class VolController extends Zend_Controller_Action
{
	public function indexAction(){ // A effacer
	}

	public function ajouterLigneAction() // faire les controles de saisie t ajouter l'aeroport d'origine/!\
	{
		$this->view->title = "Ajouter une ligne";
		$this->view->headScript()->appendFile('/js/jquery-ui-sliderAccess.js');
		$this->view->headScript()->appendFile('/js/jquery-ui-timepicker-addon.js');
		$this->view->headLink()->appendStylesheet('/css/jquery-ui-timepicker-addon.css');
		$this->view->headLink()->appendStylesheet('/css/jquery-ui-1.8.23.css');
		$form = new FormulaireLigne();
		$form->setAction($this->getRequest()->getActionName());
		$TableLigne = new Ligne;
		if($this->getRequest()->isPost())
		{
			$data=$this->getRequest()->getPost();
			if($form->isValid($data))
			{
				$Ligne = $TableLigne->createRow();
				$Ligne->id_aeroport_origine = $form->getValue('aeroportOrigine');
				$Ligne->id_aeroport_depart=$form->getValue('aeroportDepart');
				$Ligne->id_aeroport_arrivee=$form->getValue('aeroportArrivee');
				$Ligne->heure_depart=$form->getValue('heureDepart');
				$Ligne->heure_arrivee=$form->getValue('heureArrivee');
				$Ligne->tarif=$form->getValue('tarif');
				$Ligne->distance=$form->getValue('distance');
				$Id=$Ligne->save();
				if($this->getRequest()->getPost('periodicite'))
				{
					$TablePeriodicite=new Periodicite;
					foreach ($form->getValue("jours") as $jour){
						$Periode=$TablePeriodicite->createRow();
						$Periode->numero_ligne=$Id;
						$Periode->numero_jour=$jour;
						$Periode->save();
					}
				}
				else
				{
					$TableVol=new Vol;
					$Vol=$TableVol->createRow();
					$Vol->id_vol=$TableVol->getLastId($Id)+1;
					$Vol->numero_ligne=$Id;
					$Vol->id_aeroport_depart_effectif=$form->getValue('aeroportDepart');
					$Vol->id_aeroport_arrivee_effectif=$form->getValue('aeroportArrivee');
					$dateDepart = new Zend_Date($form->getValue('dateDepart'), 'dd/MM/yyyy');
					$Vol->date_depart=$dateDepart->get('yyyy-MM-dd');
					$dateArrivee = new Zend_Date($form->getValue('dateArrivee'), 'dd/MM/yyyy');
					$Vol->date_arrivee=$dateArrivee->get('yyyy-MM-dd');
					$Vol->tarif_effectif=$form->getValue('tarif_effectif');
					$Vol->save();
				}
				echo "insertion reussi !!!";
			}
			else{
				$form->populate($data);
				$tableAeroport=new Aeroport;
				if(isset($data["aeroportOrigine"]))
				{
					$AeroportOrigine=$form->getElement('aeroportOrigine');
					$form->getElement('PopulateOrigine')->setValue("1");
					$requete=$tableAeroport->select()
					->setIntegrityCheck(false)
					->from(array('ae'=>'aeroport'),array('ae.nom','ae.code_ville','ae.id_aeroport'))
					->join(array('v'=>'ville'),'v.code_ville = ae.code_ville',array('v.code_pays'))
					->where('code_pays=?',$data["Origine"]);
					$aeroports=$tableAeroport->fetchAll($requete);
					foreach($aeroports as $aeroport)
						$AeroportOrigine->addMultiOption($aeroport->id_aeroport,$aeroport->nom);
				}
				if(isset($data["aeroportDepart"])){
					$AeroportDepart=$form->getElement('aeroportDepart');
					$form->getElement('PopulateDepart')->setValue("1");
					$requete=$tableAeroport->select()
					->setIntegrityCheck(false)
					->from(array('ae'=>'aeroport'),array('ae.nom','ae.code_ville','ae.id_aeroport'))
					->join(array('v'=>'ville'),'v.code_ville = ae.code_ville',array('v.code_pays'))
					->where('code_pays=?',$data["Origine"]);
					$aeroports=$tableAeroport->fetchAll($requete);
					foreach($aeroports as $aeroport)
						$AeroportDepart->addMultiOption($aeroport->id_aeroport,$aeroport->nom);
				}
				if(isset($data["aeroportArrivee"]))
				{
					$AeroportArrivee=$form->getElement('aeroportArrivee');
					$form->getElement('PopulateArrivee')->setValue("1");
					$requete=$tableAeroport->select()
					->setIntegrityCheck(false)
					->from(array('ae'=>'aeroport'),array('ae.nom','ae.code_ville','ae.id_aeroport'))
					->join(array('v'=>'ville'),'v.code_ville = ae.code_ville',array('v.code_pays'))
					->where('code_pays=?',$data["Origine"]);
					$aeroports=$tableAeroport->fetchAll($requete);
					foreach($aeroports as $aeroport)
						$AeroportArrivee->addMultiOption($aeroport->id_aeroport,$aeroport->nom);
				}

				
				
				$this->view->Form=$form;
			}
		}
		else
		{
			$form->getElement("Origine")->setValue("250");
			$form->getElement("Depart")->setValue("250");
			$form->getElement("Arrive")->setValue("250");
			$form->getElement("Numero")->setValue($TableLigne->getLastId()+1);
			$this->view->Form=$form;
		}
	}

	public function modifierLigneAction()  // Faux
	{
		$this->view->title="Modifier une ligne";
		$numero_ligne=$this->_getParam('ligne');
		$TableLigne=new Ligne;
		$Ligne=$TableLigne->find($numero_ligne)->current();
		try{
			//$Ligne->delete();
		}catch(Exception $e){
			$this->view->erreur=$e->getMessage();
		}
	}

	public function supprimerLigneAction() // Faux
	{
		$this->view->title="Supprimer une ligne";
		$numero_ligne=$this->_getParam('ligne');
		$TableLigne=new Ligne;
		$Ligne=$TableLigne->find($numero_ligne)->current();
		try{
			$Ligne->delete();
		}catch(Exception $e){
			$this->view->erreur=$e->getMessage();
		}

	}

	public function rechercherAdresseAction(){
		$this->_helper->layout->disableLayout();
		$id_aeroport=$this->_getParam('id_aeroport');
		$TableAeroport=new Aeroport;
		$Aeroport=$TableAeroport->find($id_aeroport)->current();
		$Ville=$Aeroport->findParentRow('Ville');
		echo $Aeroport->adresse.", ".$Ville->nom.", ".$Ville->findParentRow('Pays')->nom;

	}

	public function rechercherAeroportAction()
	{
		echo $isValid=$this->getParam("isValid");
		$this->_helper->layout->disableLayout();
		$tableAeroport = new Aeroport;
		$requete=$tableAeroport->select()
		->setIntegrityCheck(false)
		->from(array('ae'=>'aeroport'),array('ae.nom','ae.code_ville','ae.id_aeroport'))
		->join(array('v'=>'ville'),'v.code_ville = ae.code_ville',array('v.code_pays'))
		->where('code_pays=?',$this->_getParam('pays'));
		$aeroports=$tableAeroport->fetchAll($requete);
		echo '<option value="0" disabled="disabled"';
		if(!($isValid))
			echo " selected='selected' ";
		echo '>Choisissez l\'aeroport</option>';
		foreach ($aeroports as $aeroport)
		{
			echo '<option value="'.$aeroport->id_aeroport.'">'.$aeroport->nom.'</option>';
		}
	}

	public function consulterLigneAction(){ // A completer l'indexation de la page dans consulterligne.phtml et verifier peut etre si les valeurs et get existent

		$nbLigne=10; //Nombre de lignes par pages

		if($this->getRequest()->getParam('page'))
			$page=$this->getRequest()->getParam('page');
		else
			$page=1;
			
		if($this->getRequest()->getParam('orderBy'))
			$orderBy=$this->getRequest()->getParam('orderBy');
		else
			$orderBy="Numero_Asc";

		$TableLigne= new Ligne;
		$requete=$TableLigne
		->select()
		->setIntegrityCheck(false)
		->from(array('l'=>'ligne'))
		->join(array('a1'=>'aeroport'),'l.id_aeroport_depart=a1.id_aeroport',array('a1.nom as nom_aeroport_depart'))
		->join(array('a2'=>'aeroport'),'l.id_aeroport_arrivee=a2.id_aeroport',array('a2.nom as nom_aeroport_arrivee'));

		switch ($orderBy) {
			case "Numero_Asc": $requete->order("numero_ligne asc"); break;
			case "Numero_Desc": $requete->order("numero_ligne desc"); break;
			case "AeDepart_Asc": $requete->order("id_aeroport_depart asc"); break;
			case "AeDepart_Desc": $requete->order("id_aeroport_depart desc"); break;
			case "AeArrive_Asc": $requete->order("id_aeroport_arrivee asc"); break;
			case "AeArrive_Desc": $requete->order("id_aeroport_arrivee desc"); break;
			case "HeDepart_Asc": $requete->order("heure_depart asc"); break;
			case "HeDepart_Desc": $requete->order("heure_depart desc"); break;
			case "HeArrivee_Asc": $requete->order("heure_arrivee asc"); break;
			case "HeArrivee_Desc": $requete->order("heure_arrivee desc"); break;
		}

		$lignes=$TableLigne->fetchAll($requete);
		$this->view->order=$orderBy;
		$this->view->Numero=$this->orderColumns("Numero",$orderBy,null,"Numéro");
		$this->view->AeDepart=$this->orderColumns("AeDepart",$orderBy,null,"Aeroport de départ");
		$this->view->AeArrive=$this->orderColumns("AeArrive",$orderBy,null,"Aeroport d'arrivée");
		$this->view->HeDepart=$this->orderColumns("HeDepart",$orderBy,null,"Heure de départ");
		$this->view->HeArrivee=$this->orderColumns("HeArrivee",$orderBy,null,"Heure d'arrivée");

		$paginator = Zend_Paginator::factory($lignes);
		$paginator->setItemCountPerPage($nbLigne);
		$paginator->setCurrentPageNumber($page);
		$this->view->paginator=$paginator;
	}

	public function consulterVolAction(){  // A completer l'indexation de la page dans consulterligne.phtml et verifier peut etre si les valeurs et get existent

		$nbLigne=5;

		$numero_ligne=$this->getRequest()->getParam('ligne');
		$TableLigne= new Ligne;
		$ligne=$TableLigne->find($numero_ligne)->current();
		if( ($this->getRequest()->getParam('ligne')) && ($ligne!=NULL) )
		{
			$this->view->ligne=$ligne;
			$this->view->aeroport_origine=$ligne->findParentAeroportByaeroport_origine();
			$this->view->aeroport_depart=$ligne->findParentAeroportByaeroport_depart();
			$this->view->aeroport_arrivee=$ligne->findParentAeroportByaeroport_arrivee();
			$this->view->jours=$ligne->findJourSemaineViaPeriodicite();
			$this->view->nbJours=count($ligne->findJourSemaineViaPeriodicite());
			$this->view->villeOrigine=$ligne->findParentRow('Aeroport','aeroport_origine')->findParentRow('Ville');
			$this->view->paysOrigine=$this->view->villeOrigine->findParentRow('Pays');
			$this->view->villeDepart=$ligne->findParentRow('Aeroport','aeroport_depart')->findParentRow('Ville');
			$this->view->paysDepart=$this->view->villeDepart->findParentRow('Pays');
			$this->view->villeArrivee=$ligne->findParentRow('Aeroport','aeroport_arrivee')->findParentRow('Ville');
			$this->view->paysArrive=$this->view->villeArrivee->findParentRow('Pays');



			if($this->getRequest()->getParam('orderBy'))
				$orderBy=$this->getRequest()->getParam('orderBy');
			else
				$orderBy="Id_Asc";

			if($this->getRequest()->getParam('page'))
				$page=$this->getRequest()->getParam('page');
			else
				$page=1;

			$TableVol= new Vol;
			$requete=$TableVol
			->select()
			->from(array('v'=>'vol'))
			->setIntegrityCheck(false)
			->joinLeft(array('a'=>'avion'),'a.id_avion=v.id_avion')
			->joinLeft(array('ta'=>'type_avion'),'a.id_type_avion=ta.id_type_avion',array('ta.libelle'))
			->joinLeft(array('p'=>'pilote'),'p.id_pilote=v.id_pilote',array('p.nom'))
			->joinLeft(array('c'=>'pilote'),'c.id_pilote=v.id_copilote',array('c.nom as copilote'))
			->where("numero_ligne=?",$numero_ligne)
			->limitPage($page,$nbLigne);

			switch ($orderBy) {
				case "Id_Asc": $requete->order("v.id_vol asc"); break;
				case "Id_Desc": $requete->order("v.id_vol desc"); break;
				case "DaDepart_Asc": $requete->order("v.date_depart asc"); break;
				case "DaDepart_Desc": $requete->order("v.date_depart desc"); break;
				case "DaArrive_Asc": $requete->order("v.date_arrivee asc"); break;
				case "DaArrive_Desc": $requete->order("v.date_arrivee desc"); break;
				case "Avion_Asc": $requete->order("ta.libelle asc"); break;
				case "Avion_Desc": $requete->order("ta.libelle desc"); break;
				case "Pilote_Asc":	$requete->order("p.nom asc"); break;
				case "Pilote_Desc": $requete->order("p.nom desc"); break;
				case "Copilote_Asc": $requete->order("c.nom asc"); break;
				case "Copilote_Desc": $requete->order("c.nom desc"); break;
				default : $requete->order("v.id_vol asc"); break;
			}

			$vols=$TableVol->fetchAll($requete);
			$paginator = Zend_Paginator::factory($vols);
			$paginator->setItemCountPerPage($nbLigne);
			$paginator->setCurrentPageNumber($page);
			$this->view->count=$paginator->getAdapter()->count();
			$this->view->paginator=$paginator;

			$this->view->Id=$this->orderColumns("Id",$orderBy,null,"Numéro du vol");
			$this->view->DaDepart=$this->orderColumns("DeDepart",$orderBy,null,"Date de départ");
			$this->view->DaArrive=$this->orderColumns("DaArrive",$orderBy,null,"Date d'arrivée");
			$this->view->Avion=$this->orderColumns("Avion",$orderBy,null,"Type d'Avion");
			$this->view->Pilote=$this->orderColumns("Pilote",$orderBy,null,"Nom du pilote");
			$this->view->Copilote=$this->orderColumns("Copilote",$orderBy,null,"Nom du copilote");
		}
		else
		{
			$this->_helper->viewRenderer->setNoRender(true);/********************* A terminer ******************/
			echo $this->view->action('pageerreur','error',null,array('page'=>$this->getRequest()->getActionName()));
		}
	}

	public function ficheVolAction(){

		$numero_ligne=$this->getRequest()->getParam('ligne');
		$container=$this->view->navigation()->findOneBy("id","consulterVol");
		$container->set("params", array( 'ligne' =>$numero_ligne));
		$container->set("title", "Consulter les vols de la ligne ".$numero_ligne);


		if( ($this->getRequest()->getParam('vol')) && ($this->getRequest()->getParam('ligne')) )
		{
			$id_vol=$this->getRequest()->getParam('vol');
		}
		else
			echo "error"; // A terminer
		$tableVol=new Vol;
		$vol=$tableVol->find($id_vol,$numero_ligne)->current();
		$this->view->vol=$vol;
		$tableLigne=new Ligne;
		$ligne=$tableLigne->find($numero_ligne)->current();
		$this->view->jours=$ligne->findJourSemaineViaPeriodicite();
		$this->view->nbJours=count($ligne->findJourSemaineViaPeriodicite());
		$this->view->aeroport_origine=$ligne->findParentRow('Aeroport','aeroport_origine');
		$this->view->aeroport_depart=$ligne->findParentRow('Aeroport','aeroport_depart');
		$this->view->aeroport_arrivee=$ligne->findParentRow('Aeroport','aeroport_arrivee');
		if ($vol->id_avion!=NULL)
			$this->view->typeAvion=$vol->findParentRow("Avion")->findParentRow("TypeAvion");
		$this->view->aeroport_depart_effectif=$vol->findParentRow('Aeroport','id_aeroport_depart_effectif');
		$this->view->aeroport_arrivee_effectif=$vol->findParentRow('Aeroport','id_aeroport_arrivee_effectif');
		if ($vol->id_pilote!=NULL)
			$this->view->pilote=$vol->findParentRow('Pilote','Pilote');
		if ($vol->id_copilote!=NULL)
			$this->view->copilote=$vol->findParentRow('Pilote','Copilote');
		$this->view->villeOrigine=$ligne->findParentRow('Aeroport','aeroport_origine')->findParentRow('Ville');
		$this->view->paysOrigine=$this->view->villeOrigine->findParentRow('Pays');
		$this->view->villeDepart=$ligne->findParentRow('Aeroport','aeroport_depart')->findParentRow('Ville');
		$this->view->paysDepart=$this->view->villeDepart->findParentRow('Pays');
		$this->view->villeArrivee=$ligne->findParentRow('Aeroport','aeroport_arrivee')->findParentRow('Ville');
		$this->view->paysArrive=$this->view->villeArrivee->findParentRow('Pays');
		$this->view->ligne=$ligne;
	}

	public function orderColumns($nomOrder,$order,$class,$nom){
		$orderAsc=$nomOrder."_Asc";
		$orderDesc=$nomOrder."_Desc";
		$params=$this->getRequest()->getParams();
		$Html="<th ";

		if( (strstr($order, "_Desc")) && ($orderDesc==$order) )
			$Html .= "id='desc'";
		else if ( (strstr($order, "_Asc")) && ($orderAsc==$order) )
			$Html .= "id='asc'";

		$Html .="><a href='";

		if( (strstr($order, "_Asc")) && (strstr($order, $nomOrder)) )
			$params["orderBy"]=$nomOrder."_Desc";
		else
			$params["orderBy"]=$nomOrder."_Asc";

		$Html.=$this->view->url($params)."'>".$nom."</a></th>";

		return $Html;
	}

	public function init(){
		$this->view->headScript()->appendFile('/js/VolFonction.js');
		$this->view->headScript()->appendFile('http://maps.googleapis.com/maps/api/js?sensor=false&libraries=geometry');

		parent::init();
	}
}