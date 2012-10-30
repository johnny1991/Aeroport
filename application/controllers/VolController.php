<?php
class VolController extends Zend_Controller_Action
{
	public function indexAction(){ // A effacer
	}

	public function ajouterLigneAction() // faire les controles de saisie t ajouter l'aeroport d'origine/!\
	{
		$this->view->title = "Ajouter un vol";
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
					$Vol->date_depart=$form->getValue('dateDepart');
					$Vol->date_arrivee=$form->getValue('dateArrivee');
					$Vol->save();
				}
				echo "insertion reussi !!!";
			}
			else{
				$form->populate($data);
				$this->view->Form=$form;
			}
		}
		else
		{
			/*$form->getElement("Origine")->setValue("250");
			$form->getElement("Depart")->setValue("250");
			$form->getElement("Arrive")->setValue("250");
			$form->getElement("Numero")->setValue($TableLigne->getLastId()+1);*/
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

	public function rechercherAeroportAction()
	{
		$this->_helper->layout->disableLayout();
		$tableAeroport = new Aeroport;
		$requete=$tableAeroport->select()
		->setIntegrityCheck(false)
		->from(array('ae'=>'aeroport'),array('ae.nom','ae.code_ville','ae.id_aeroport'))
		->join(array('v'=>'ville'),'v.code_ville = ae.code_ville',array('v.code_pays'))
		->where('code_pays=?',$this->_getParam('pays'));
		$aeroports=$tableAeroport->fetchAll($requete);
		foreach ($aeroports as $aeroport)
		{
			echo '<option value="'.$aeroport->id_aeroport.'">'.$aeroport->nom.'</option>';
		}
	}

	public function consulterligneAction(){ // A completer l'indexation de la page dans consulterligne.phtml et verifier peut etre si les valeurs et get existent

		$nbLigne=2; //Nombre de lignes par pages

		if($this->getRequest()->getParam('page'))
			$page=$this->getRequest()->getParam('page');
		else
			$page=1;
			
		if($this->getRequest()->getParam('orderBy'))
			$orderBy=$this->getRequest()->getParam('orderBy');
		else
			$orderBy="Numero_Asc";

		$TableLigne= new Ligne;
		$requete=$TableLigne->select()->from($TableLigne)->limitPage($page,$nbLigne);

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
			case "Periodique_Asc": $requete->order("periodique asc"); break;
			case "Periodique_Desc": $requete->order("periodique desc"); break;
		}

		$lignes=$TableLigne->fetchAll($requete);
		$this->view->order=$orderBy;
		$this->view->lignes=$lignes;
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
			$this->view->vols=$vols;

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
		$this->view->aeroport_origine=$ligne->findParentRow('Aeroport','aeroport_origine');
		$this->view->aeroport_depart=$ligne->findParentRow('Aeroport','aeroport_depart');
		$this->view->aeroport_arrivee=$ligne->findParentRow('Aeroport','aeroport_arrivee');
		$this->view->typeAvion=$vol->findParentRow("Avion")->findParentRow("TypeAvion");
		$this->view->aeroport_depart_effectif=$vol->findParentRow('Aeroport','id_aeroport_depart_effectif');
		$this->view->aeroport_arrivee_effectif=$vol->findParentRow('Aeroport','id_aeroport_arrivee_effectif');
		$this->view->copilote=$vol->findParentRow('Pilote','Copilote');
		$this->view->pilote=$vol->findParentRow('Pilote','Pilote');
		$this->view->ligne=$ligne;
	}

	public function orderColumns($nomOrder,$order,$class,$nom){

		$params=$this->getRequest()->getParams();
		$Html="<div class='".$class."' ";

		if(strstr($order, "_Desc"))
			$Html .= "id='desc'";
		else if (strstr($order, "_Asc"))
			$Html .= "id='asc'";

		$Html .="><b><a href='";

		if( (strstr($order, "_Asc")) && (strstr($order, $nomOrder)) )
			$params["orderBy"]=$nomOrder."_Desc";
		else
			$params["orderBy"]=$nomOrder."_Asc";

		$Html.=$this->view->url($params)."'>".$nom."</a></b></div>";

		return $Html;
	}

	public function init(){
		$this->view->headScript()->appendFile('/js/VolFonction.js');
		$this->view->headLink()->appendStylesheet('/css/VolStyle.css');
		
		parent::init();
	}
}