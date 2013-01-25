<?php

class CrudController extends Zend_Controller_Action
{
	public function init(){
		$this->view->headLink()->appendStylesheet('/css/DrhCSS.css');
		$this->view->headScript()->appendFile('/js/CrudFonction.js');
		parent::init();
	}

	public function indexAction(){
		
	}
	
	public function listePaysAction(){
		$tablePays = new Pays();
		
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
			$orderBy = 'nom_Asc';
		
		switch ($orderBy)
		{
			case 'code_Asc': $req = $tablePays->getPaysWithOrder('code_pays asc')->toArray(); break;
			case 'code_Desc': $req = $tablePays->getPaysWithOrder('code_pays desc')->toArray(); break;
			case 'nom_Asc': $req = $tablePays->getPaysWithOrder('nom asc')->toArray(); break;
			case 'nom_Desc': $req = $tablePays->getPaysWithOrder('nom desc')->toArray(); break;
			case 'alpha2_Asc': $req = $tablePays->getPaysWithOrder('alpha2 asc')->toArray(); break;
			case 'alpha2_Desc': $req = $tablePays->getPaysWithOrder('alpha2 desc')->toArray(); break;
			case 'alpha3_Asc': $req = $tablePays->getPaysWithOrder('alpha3 desc')->toArray(); break;
			case 'alpha3_Desc': $req = $tablePays->getPaysWithOrder('alpha3 desc')->toArray(); break;
		}
		
		$this->view->order = $orderBy;
		$this->view->code = Aeroport_Tableau_OrderColumn::orderColumns($this, 'code', $orderBy, null, 'Code du pays');
		$this->view->nom = Aeroport_Tableau_OrderColumn::orderColumns($this, 'nom', $orderBy, null, 'Nom');
		$this->view->alpha2 = Aeroport_Tableau_OrderColumn::orderColumns($this, 'alpha2', $orderBy, 'thDateEmbauche', 'Code alpha2');
		$this->view->alpha3 = Aeroport_Tableau_OrderColumn::orderColumns($this, 'alpha3', $orderBy, 'thDateEmbauche', 'Code alpha3');
		
		$paginator = Zend_Paginator::factory($req);
		$paginator->setItemCountPerPage(25);
		$paginator->setCurrentPageNumber($page);
		$this->view->pays = $paginator;
		$this->view->param = $this->getAllParams();
	}
	
	public function ajouterPaysAction(){
		$formAjouter = new AjouterPays();
		
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
		
			if($formAjouter->isValid($data)){
				$nom = $formAjouter->getValue('nom');
				$code = htmlentities($formAjouter->getValue('code'), ENT_QUOTES, 'UTF-8');
				$alpha2 = htmlentities($formAjouter->getValue('alpha2'), ENT_QUOTES, 'UTF-8');
				$alpha3 = htmlentities($formAjouter->getValue('alpha3'), ENT_QUOTES, 'UTF-8');
		
				$tablePays = new Pays();
					
				$newPays = $tablePays->createRow();
				$newPays->code_pays = $code;
				$newPays->nom = $nom;
				$newPays->alpha2 = $alpha2;
				$newPays->alpha3 = $alpha3;
				$newPays->save();
		
				Aeroport_Fonctions::redirector('/crud/liste-pays/');
		
			}else{
				$this->view->form = $formAjouter;
			}
		}else{
			$this->view->form = $formAjouter;
		}
	}
	
	public function modifierPaysAction(){
		$formModifier = new AjouterPays();
		$tablePays = new Pays();
		
		$id = htmlentities($this->getParam('id'), ENT_QUOTES, 'UTF-8');
		$infos = $tablePays->getInfosById($id);
		
		$this->view->nom = $infos->nom;
		
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
		
			if($formModifier->isValid($data)){
				$nom = $formModifier->getValue('nom');
				$code = htmlentities($formModifier->getValue('code'), ENT_QUOTES, 'UTF-8');
				$alpha2 = htmlentities($formModifier->getValue('alpha2'), ENT_QUOTES, 'UTF-8');
				$alpha3 = htmlentities($formModifier->getValue('alpha3'), ENT_QUOTES, 'UTF-8');
					
				$newPays = $tablePays->getInfosById($id);
				$newPays->code_pays = $code;
				$newPays->nom = $nom;
				$newPays->alpha2 = $alpha2;
				$newPays->alpha3 = $alpha3;
				$newPays->save();
		
				Aeroport_Fonctions::redirector('/crud/liste-pays/');
		
			}else{
				$this->view->form = $formModifier;
			}
		}else{
			$this->view->form = $formModifier;
		}
	}
	
	public function listeVilleAction(){
		$tableVille = new Ville();
		
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
			$orderBy = 'nom_Asc';
		
		switch ($orderBy){
			case 'nom_Asc': $req = $tableVille->getVilleWithPaysOrder('ville.nom asc')->toArray(); break;
			case 'nom_Desc': $req = $tableVille->getVilleWithPaysOrder('ville.nom desc')->toArray(); break;
			case 'pays_Asc': $req = $tableVille->getVilleWithPaysOrder('pays.nom asc')->toArray(); break;
			case 'pays_Desc': $req = $tableVille->getVilleWithPaysOrder('pays.nom desc')->toArray(); break;
		}
		
		$this->view->order = $orderBy;
		$this->view->pays = Aeroport_Tableau_OrderColumn::orderColumns($this, 'pays', $orderBy, null, 'Nom du pays');
		$this->view->nom = Aeroport_Tableau_OrderColumn::orderColumns($this, 'nom', $orderBy, null, 'Nom');
		
		$paginator = Zend_Paginator::factory($req);
		$paginator->setItemCountPerPage(25);
		$paginator->setCurrentPageNumber($page);
		$this->view->ville = $paginator;
		$this->view->param = $this->getAllParams();
	}
	
	public function ajouterVilleAction(){
		$formAjouter = new AjouterVille();
	
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
	
			if($formAjouter->isValid($data)){
				$nom = $formAjouter->getValue('nom');
				$codePays = htmlentities($formAjouter->getValue('code_pays'), ENT_QUOTES, 'UTF-8');
				$codePostal = htmlentities($formAjouter->getValue('code_postal'), ENT_QUOTES, 'UTF-8');
				
				if($codePostal == '')
					$codePostal = null;
				
				$tableVille = new Ville();
				
				$lastId = intval($tableVille->getLastId()->lastId) + 1;
				
				$new = $tableVille->createRow();
				$new->code_ville = $lastId;	
				$new->nom = $nom;
				$new->code_pays = $codePays;
				$new->code_postal = $codePostal;
				$new->save();
	
				Aeroport_Fonctions::redirector('/crud/liste-ville/');
	
			}else{
				$this->view->form = $formAjouter;
			}
		}else{
			$this->view->form = $formAjouter;
		}
	}
	
	public function modifierVilleAction(){
		$formModifier = new AjouterVille();
		$tableVille = new Ville();
	
		$id = htmlentities($this->getParam('id'), ENT_QUOTES, 'UTF-8');
		$infos = $tableVille->getInfosById($id);
	
		$this->view->nom = $infos->nom;
	
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
	
			if($formModifier->isValid($data)){
				$nom = $formModifier->getValue('nom');
				$codePays = htmlentities($formModifier->getValue('code_pays'), ENT_QUOTES, 'UTF-8');
				$codePostal = htmlentities($formModifier->getValue('code_postal'), ENT_QUOTES, 'UTF-8');
					
				$new = $tableVille->find($id)->current();
				$new->nom = $nom;
				$new->code_pays = $codePays;
				$new->code_postal = $codePostal;
				$new->save();
	
				Aeroport_Fonctions::redirector('/crud/liste-ville/');
	
			}else{
				$this->view->form = $formModifier;
			}
		}else{
			$this->view->form = $formModifier;
		}
	}
	
	public function listeAeroportAction(){
		$tableAeroport = new Aeroport();
		
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
			$orderBy = 'nom_Asc';
		
		switch ($orderBy)
		{
			case 'id_Asc': $req = $tableAeroport->getAeroportWithVilleAndPays('id_aeroport asc')->toArray(); break;
			case 'id_Desc': $req = $tableAeroport->getAeroportWithVilleAndPays('id_aeroport desc')->toArray(); break;
			case 'nom_Asc': $req = $tableAeroport->getAeroportWithVilleAndPays('nom asc')->toArray(); break;
			case 'nom_Desc': $req = $tableAeroport->getAeroportWithVilleAndPays('nom desc')->toArray(); break;
			case 'ville_Asc': $req = $tableAeroport->getAeroportWithVilleAndPays('nomVille asc')->toArray(); break;
			case 'ville_Desc': $req = $tableAeroport->getAeroportWithVilleAndPays('nomVille desc')->toArray(); break;
			case 'pays_Asc': $req = $tableAeroport->getAeroportWithVilleAndPays('nomPays desc')->toArray(); break;
			case 'pays_Desc': $req = $tableAeroport->getAeroportWithVilleAndPays('nomPays desc')->toArray(); break;
		}
		
		$this->view->order = $orderBy;
		$this->view->trigramme = Aeroport_Tableau_OrderColumn::orderColumns($this, 'id', $orderBy, null, 'Abréviation');
		$this->view->nom = Aeroport_Tableau_OrderColumn::orderColumns($this, 'nom', $orderBy, null, 'Nom');
		$this->view->ville = Aeroport_Tableau_OrderColumn::orderColumns($this, 'ville', $orderBy, 'thDateEmbauche', 'Ville');
		$this->view->pays = Aeroport_Tableau_OrderColumn::orderColumns($this, 'pays', $orderBy, 'thDateEmbauche', 'Pays');
		
		$paginator = Zend_Paginator::factory($req);
		$paginator->setItemCountPerPage(25);
		$paginator->setCurrentPageNumber($page);
		$this->view->aeroport = $paginator;
		$this->view->param = $this->getAllParams();
	}
	
	public function ajouterAeroportAction(){
		$formAjouter = new AjouterAeroport();
		
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
		
			if($formAjouter->isValid($data)){
				$idAeroport = htmlentities($formAjouter->getValue('id'), ENT_QUOTES, 'UTF-8');
				$nom = $formAjouter->getValue('nom');
				$ville = htmlentities($formAjouter->getValue('ville'), ENT_QUOTES, 'UTF-8');
				$adresse = $formAjouter->getValue('adresse');
				$longueur = htmlentities($formAjouter->getValue('longueur'), ENT_QUOTES, 'UTF-8');
		
				$tableAeroport = new Aeroport();

				$new = $tableAeroport->createRow();
				$new->id_aeroport = $idAeroport;
				$new->nom = $nom;
				$new->code_ville = $ville;
				$new->adresse = $adresse;
				$new->longueur_piste = $longueur;
				$new->save();
		
				Aeroport_Fonctions::redirector('/crud/liste-aeroport/');
		
			}else{
				$this->view->form = $formAjouter;
			}
		}else{
			$this->view->form = $formAjouter;
		}
	}
	
	public function modifierAeroportAction(){
		$formModifier = new AjouterAeroport();
		$tableAeroport = new Aeroport();
		
		$id = htmlentities($this->getParam('id'), ENT_QUOTES, 'UTF-8');
		$infos = $tableAeroport->getInfosById($id);
		
		$this->view->nom = $infos->nom;
		
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
		
			if($formModifier->isValid($data)){
				$idAeroport = htmlentities($formModifier->getValue('id'), ENT_QUOTES, 'UTF-8');
				$nom = $formModifier->getValue('nom');
				$ville = htmlentities($formModifier->getValue('ville'), ENT_QUOTES, 'UTF-8');
				$adresse = $formModifier->getValue('adresse');
				$longueur = htmlentities($formModifier->getValue('longueur'), ENT_QUOTES, 'UTF-8');
					
				$new = $tableAeroport->find($id)->current();
				$new->id_aeroport = $idAeroport;
				$new->nom = $nom;
				$new->code_ville = $ville;
				$new->adresse = $adresse;
				$new->longueur_piste = $longueur;
				$new->save();
		
				Aeroport_Fonctions::redirector('/crud/liste-aeroport/');
		
			}else{
				$this->view->form = $formModifier;
			}
		}else{
			$this->view->form = $formModifier;
		}
	}
	
	public function rechercherVilleAction(){
		$this->_helper->layout()->disableLayout();
		
		$idPays = htmlentities($this->getParam('id'), ENT_QUOTES, 'UTF-8');
		$tableVille = new Ville();
		
		$villes = $tableVille->getVillesByIdPays($idPays);
		foreach($villes as $ville){
			echo '<option value="'.$ville->code_ville.'">'.$ville->nom.'</option>';
		}
	}
}
  

