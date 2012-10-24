<?php
class VolController extends Zend_Controller_Action
{
	public function ajoutAction() // faire les controles de saisie /!\
	{
		$this->view->headScript()->appendFile('/js/jquery-ui-sliderAccess.js');
		$this->view->headScript()->appendFile('/js/jquery-ui-timepicker-addon.js');
		$this->view->headScript()->appendFile('/js/VolFonction.js');
		$this->view->headLink()->appendStylesheet('/css/jquery-ui-timepicker-addon.css');
		$this->view->headLink()->appendStylesheet('http://code.jquery.com/ui/1.8.23/themes/smoothness/jquery-ui.css');
		$TableLigne = new Ligne;
		$this->view->title="Ajouter un vol";
		$form= new FormulaireVol();
		if($this->getRequest()->isPost())
		{
			$data=$this->getRequest()->getPost();
			if($form->isValid($data))
			{
				$Ligne=$TableLigne->createRow();
				$Ligne->id_aeroport_origine='CDG';
				$Ligne->id_aeroport_depart=$form->getValue('aeroportDepart');
				$Ligne->id_aeroport_arrivee=$form->getValue('aeroportArrivee');
				$Ligne->heure_depart=$form->getValue('heureDepart');
				$Ligne->heure_arrivee=$form->getValue('heureArrivee');
				$Ligne->periodique=$form->getValue('periodicite');
				$Id=$Ligne->save();
				if($this->getRequest()->getPost('periodicite'))
				{
					$TablePeriodicite=new Periodicite;
					$jours=$form->getValue("jours");
					$Periode=$TablePeriodicite->createRow();
					$Periode->numero_ligne=1;
					$Periode->numero_jour=1;
					foreach ($jours as $jour){
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
			$this->view->Form=$form;
		}
	}

	public function modifierAction()
	{
		$this->view->title="Modifier un vol";
		$TableVol= new Vol;
		$numeroVol=$this->_getParam('numero');
		$Vol=$TableVol->find($numeroVol)->current();
		$Vol->save();
	}

	public function supprimerAction()
	{
		$this->view->title="Supprimer un vol";
		$TableVol= new Vol;
		$numeroVol=$this->_getParam('numero');
		$Vol=$TableVol->find($numeroVol)->current();
		try{
			$Vol->delete();
		}catch(Exception $e){
			$this->view->erreur=$e->getMessage();
		}

	}

	public function rechercheaeroportAction()
	{
		$this->_helper->layout->disableLayout();
		$pays=$this->_getParam('pays');
		$tableAeroport = new Aeroport;
		$requete=$tableAeroport->select()
		->setIntegrityCheck(false)
		->from(array('ae'=>'aeroport'),array('ae.nom','ae.code_ville','ae.id_aeroport'))
		->join(array('v'=>'ville'),'v.code_ville = ae.code_ville',array('v.code_pays'))
		->where('code_pays=?',$pays);
		$aeroports=$tableAeroport->fetchAll($requete);
		foreach ($aeroports as $aeroport){
			echo '<option value="'.$aeroport->id_aeroport.'">'.$aeroport->nom.'</option>';
		}
	}

	public function consulterligneAction(){
		$TableLigne= new Ligne;
		$nbLigne=20;
		$page=1;
		if($this->getRequest()->getParam('orderBy'))
		{
			$orderBy=$this->getRequest()->getParam('orderBy');
		}
		else
			$orderBy="Numero_Asc";
		
		if($orderBy=="Numero_Asc")
			$this->view->lignes=$TableLigne->fetchAll($TableLigne->select()->from($TableLigne)->order("numero_ligne asc")->limitPage($page,$nbLigne));
		else if($orderBy=="Numero_Desc")
			$this->view->lignes=$TableLigne->fetchAll($TableLigne->select()->from($TableLigne)->order("numero_ligne desc")->limitPage($page,$nbLigne));
		if($orderBy=="AeDepart_Asc")
			$this->view->lignes=$TableLigne->fetchAll($TableLigne->select()->from($TableLigne)->order("id_aeroport_depart asc")->limitPage($page,$nbLigne));
		else if($orderBy=="AeDepart_Desc")
			$this->view->lignes=$TableLigne->fetchAll($TableLigne->select()->from($TableLigne)->order("id_aeroport_depart desc")->limitPage($page,$nbLigne));
		if($orderBy=="AeArrive_Asc")
			$this->view->lignes=$TableLigne->fetchAll($TableLigne->select()->from($TableLigne)->order("id_aeroport_arrivee asc")->limitPage($page,$nbLigne));
		else if($orderBy=="AeArrive_Desc")
			$this->view->lignes=$TableLigne->fetchAll($TableLigne->select()->from($TableLigne)->order("id_aeroport_arrivee desc")->limitPage($page,$nbLigne));
		if($orderBy=="HeDepart_Asc")
			$this->view->lignes=$TableLigne->fetchAll($TableLigne->select()->from($TableLigne)->order("heure_depart asc")->limitPage($page,$nbLigne));
		else if($orderBy=="HeDepart_Desc")
			$this->view->lignes=$TableLigne->fetchAll($TableLigne->select()->from($TableLigne)->order("heure_depart desc")->limitPage($page,$nbLigne));
		if($orderBy=="HeArrivee_Asc")
			$this->view->lignes=$TableLigne->fetchAll($TableLigne->select()->from($TableLigne)->order("heure_arrivee asc")->limitPage($page,$nbLigne));
		else if($orderBy=="HeArrivee_Desc")
			$this->view->lignes=$TableLigne->fetchAll($TableLigne->select()->from($TableLigne)->order("heure_arrivee desc")->limitPage($page,$nbLigne));
		if($orderBy=="Periodique_Asc")
			$this->view->lignes=$TableLigne->fetchAll($TableLigne->select()->from($TableLigne)->order("periodique asc")->limitPage($page,$nbLigne));
		else if($orderBy=="Periodique_Desc")
			$this->view->lignes=$TableLigne->fetchAll($TableLigne->select()->from($TableLigne)->order("periodique desc")->limitPage($page,$nbLigne));
		
		
		
		
		
		$this->view->order=$orderBy;
		//$lignes=$TableLigne->fetchAll();
		//$this->view->lignes=$lignes;
	}
	
	public function consultervolAction(){
		$TableVol= new Vol;
		$numero_ligne=$this->_getParam('id');
		$requete=$TableVol->select()->from($TableVol)->where("numero_ligne=?",$numero_ligne);
		$vols=$TableVol->fetchAll($requete);
		$this->view->vols=$vols;
	}
	
	public function init(){
		parent::init();
	}
}