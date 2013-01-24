<?php

class LogistiqueController extends Zend_Controller_Action
{
	public function init(){
		$this->view->headLink()->appendStylesheet('/css/DrhCSS.css');
		$this->view->headScript()->appendFile('/js/LogistiqueFonction.js');
		date_default_timezone_set('Europe/Paris');
		parent::init();
	}

	public function indexAction(){
		$theDate = date('Y-m-d');
		$tableVol = new Vol();
		
		if($this->getRequest()->getParam('page')){
			$pageParam = htmlentities($this->getParam('page'), ENT_QUOTES, 'UTF-8');
			$params = array($pageParam => 'int');
			if(Aeroport_Fonctions::validParam($params)){
				$page = $pageParam;
			}
			else{
				$page = 1;
			}
		}
		else
			$page = 1;
		
		if($this->getRequest()->getParam('orderBy'))
			$orderBy = $this->getRequest()->getParam('orderBy');
		else
			$orderBy = 'ligne_Asc';
		
		switch ($orderBy)
		{
			case 'ligne_Asc': $req = $tableVol->getVolByDate($theDate, 'numero_ligne asc')->toArray(); break;
			case 'ligne_Desc': $req = $tableVol->getVolByDate($theDate, 'numero_ligne desc')->toArray(); break;
			case 'aerdep_Asc': $req = $tableVol->getVolByDate($theDate, 'id_aeroport_depart_effectif asc')->toArray(); break;
			case 'aerdep_Desc': $req = $tableVol->getVolByDate($theDate, 'id_aeroport_depart_effectif desc')->toArray(); break;
			case 'aerarr_Asc': $req = $tableVol->getVolByDate($theDate, 'id_aeroport_arrivee_effectif asc')->toArray(); break;
			case 'aerarr_Desc': $req = $tableVol->getVolByDate($theDate, 'id_aeroport_arrivee_effectif desc')->toArray(); break;
			case 'heudep_Asc': $req = $tableVol->getVolByDate($theDate, 'heure_depart asc')->toArray(); break;
			case 'heudep_Desc': $req = $tableVol->getVolByDate($theDate, 'heure_depart desc')->toArray(); break;
			case 'heuarr_Asc': $req = $tableVol->getVolByDate($theDate, 'heure_arrivee_effective asc')->toArray(); break;
			case 'heuarr_Desc': $req = $tableVol->getVolByDate($theDate, 'heure_arrivee_effective desc')->toArray(); break;
		}
		
		$this->view->order = $orderBy;
		$this->view->ligne = Aeroport_Tableau_OrderColumn::orderColumns($this, 'ligne', $orderBy, null, 'Ligne');
		$this->view->aerdep = Aeroport_Tableau_OrderColumn::orderColumns($this, 'aerdep', $orderBy, null, 'Aéroport de départ');
		$this->view->aerarr = Aeroport_Tableau_OrderColumn::orderColumns($this, 'aerarr', $orderBy, null, 'Aéroport d\'arrivée');
		$this->view->heudep = Aeroport_Tableau_OrderColumn::orderColumns($this, 'heudep', $orderBy, 'thDateEmbauche', 'Heure de départ');
		$this->view->heuarr = Aeroport_Tableau_OrderColumn::orderColumns($this, 'heuarr', $orderBy, 'thDateEmbauche', 'Heure d\'arrivée');
		
		$paginator = Zend_Paginator::factory($req);
		$paginator->setItemCountPerPage(25);
		$paginator->setCurrentPageNumber($page);
		$this->view->vol = $paginator;
		$this->view->param = $this->getAllParams();
	}
	
	public function ficheVolAction(){
		if(!($this->hasParam('ligne')) || !($this->hasParam('vol'))){
			Aeroport_Fonctions::redirector('/logistique');
		}else{
			$ligneParam = htmlentities($this->getParam('ligne'), ENT_QUOTES, 'UTF-8');
			$volParam = htmlentities($this->getParam('vol'), ENT_QUOTES, 'UTF-8');
		
			$params = array($volParam => 'int', $ligneParam => 'int');
			if(!Aeroport_Fonctions::validParam($params)){
				Aeroport_Fonctions::redirector('/logistique');
			}
		}
		
		$tableRemarque = new Remarque();
		$this->view->idVol = $volParam;
		$this->view->numeroLigne = $ligneParam;
		$this->view->remarques = $tableRemarque->getRemarqueByIdVolAndLigne($ligneParam, $volParam);
	}
	
	public function consulterRemarqueAction(){
		
		$tableTypeRemarque = new TypeRemarque();
		
		if($this->getRequest()->getParam('page')){
			$pageParam = htmlentities($this->getParam('page'), ENT_QUOTES, 'UTF-8');
			$params = array($pageParam => 'int');
			if(Aeroport_Fonctions::validParam($params)){
				$page = $pageParam;
			}
			else{
				$page = 1;
			}
		}
		else
			$page = 1;
		
		if($this->getRequest()->getParam('orderBy'))
			$orderBy = $this->getRequest()->getParam('orderBy');
		else
			$orderBy = 'libelle_Asc';
		
		switch ($orderBy)
		{
			case 'libelle_Asc': $remarques = $tableTypeRemarque->getRemarques('libelle_type_remarque asc')->toArray(); break;
			case 'libelle_Desc': $remarques = $tableTypeRemarque->getRemarques('libelle_type_remarque desc')->toArray(); break;
		}
		
		$this->view->order = $orderBy;
		$this->view->page = $page;
		$this->view->libelle = Aeroport_Tableau_OrderColumn::orderColumns($this, 'libelle', $orderBy, null, 'Libelle');
		
		$paginator = Zend_Paginator::factory($remarques);
		$paginator->setItemCountPerPage(25);
		$paginator->setCurrentPageNumber($page);
		$this->view->remarques = $paginator;
		$this->view->param = $this->getAllParams();
	}
	
	public function ajouterRemarqueAction(){
		$formAjouter = new AjouterTypeRemarque();
		
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
		
			if($formAjouter->isValid($data)){
		
				$libelle = htmlentities($formAjouter->getValue('remarque'), ENT_QUOTES, 'UTF-8');
		
				$tableTypeRemarque = new TypeRemarque();
				$newRemarque = $tableTypeRemarque->createRow();
				$newRemarque->libelle_type_remarque = $libelle;
				$newRemarque->save();
		
				$page = 'page/';
		
				if($this->getRequest()->getParam('page')){
					$pageParam = htmlentities($this->getParam('page'), ENT_QUOTES, 'UTF-8');
					$params = array($pageParam => 'int');
					if(Aeroport_Fonctions::validParam($params)){
						$page .= $pageParam.'/';
					}
					else{
						$page .= '1/';
					}
				}
				else
					$page .= '1/';
		
				Aeroport_Fonctions::redirector('/logistique/consulter-remarque/'.$page);
		
			}else{
				$this->view->form = $formAjouter;
				foreach ($formAjouter->getMessages('remarque') as $value){
					$this->view->error = $value;
				}
			}
		}else{
			$this->view->form = $formAjouter;
		}
	}
	
	public function modifierRemarqueAction(){
		$this->_helper->layout()->disableLayout();
		
		$libelleRemarque = $this->getParam('lib');
		$idRemarque = $this->getParam('id');
		
		$tableTypeRemarque = new TypeRemarque();
		
		$checkLib = $tableTypeRemarque->checkLibelleRemarque($libelleRemarque);
		if(count($checkLib) == 0){
			$updateRemarque = $tableTypeRemarque->find($idRemarque)->current();
			$updateRemarque->libelle_type_remarque = $libelleRemarque;
			$updateRemarque->save();
				
			if($this->getParam('orderBy'))
				echo 'page/'.$this->getParam('page').'/orderBy/'.$this->getParam('orderBy');
			else
				echo 'page/'.$this->getParam('page').'/';
		}else{
			echo 'errors';
		}
	}
	
	public function supprimerRemarqueAction(){
		$this->_helper->layout()->disableLayout();
		
		$idRemarque = htmlentities($this->getParam('id'), ENT_QUOTES, 'UTF-8');
		
		$tableTypeRemarque = new TypeRemarque();
		$tableRemarque = new Remarque();
		
		$remarques = $tableRemarque->getRemarqueByIdType($idRemarque);
		foreach($remarques as $remarque){
			$remarque->id_type_remarque = 5;
			$remarque->save();
		}
		
		$tableTypeRemarque->delete('id_type_remarque = '.$idRemarque);
	}
	
	public function traiterAllAction(){
		$this->_helper->layout()->disableLayout();
		
		$idVol = htmlentities($this->getParam('idVol'), ENT_QUOTES, 'UTF-8');
		$numeroLigne = htmlentities($this->getParam('numeroLigne'), ENT_QUOTES, 'UTF-8');
		
		$tableRemarque = new Remarque();
		$remarques = $tableRemarque->getRemarqueByIdVolAndLigne($numeroLigne, $idVol);
		foreach($remarques as $remarque){
			$remarque->traiter = 1;
			$remarque->save();
		}
	}
	
	public function traiterTypeAction(){
		$this->_helper->layout()->disableLayout();
		
		$idVol = htmlentities($this->getParam('idVol'), ENT_QUOTES, 'UTF-8');
		$numeroLigne = htmlentities($this->getParam('numeroLigne'), ENT_QUOTES, 'UTF-8');
		$idType = htmlentities($this->getParam('idType'), ENT_QUOTES, 'UTF-8');
		
		$tableRemarque = new Remarque();
		$remarques = $tableRemarque->getRemarqueByTypeByVol($idType, $numeroLigne, $idVol);
		foreach($remarques as $remarque){
			$remarque->traiter = 1;
			$remarque->save();
		}
	}
	
	public function traiterRemarqueAction(){
		$this->_helper->layout()->disableLayout();
		
		$idVol = htmlentities($this->getParam('idVol'), ENT_QUOTES, 'UTF-8');
		$numeroLigne = htmlentities($this->getParam('numeroLigne'), ENT_QUOTES, 'UTF-8');
		$idRemarque = htmlentities($this->getParam('idRemarque'), ENT_QUOTES, 'UTF-8');
		
		$tableRemarque = new Remarque();
		$remarques = $tableRemarque->getRemarqueByIdByVol($idRemarque, $numeroLigne, $idVol);
		foreach($remarques as $remarque){
			$remarque->traiter = 1;
			$remarque->save();
		}
	}

}