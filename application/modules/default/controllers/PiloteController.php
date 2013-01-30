<?php
class PiloteController extends Zend_Controller_Action
{
	public function init(){ //OK
		$this->view->headLink()->appendStylesheet('/css/DrhCSS.css');

		$acl = new Aeroport_LibraryAcl();
		$SRole = new Zend_Session_Namespace('Role');
		if(!$acl->isAllowed($SRole->id_service, $this->getRequest()->getControllerName(), $this->getRequest()->getActionName()))
		{
			$this->_redirector->gotoUrl('/');
		}
		date_default_timezone_set('Europe/Paris');
		
		parent::init();
	}
	
	public function indexAction(){ 
		$auth = Zend_Auth::getInstance();
		$identity = $auth->getIdentity();
		
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
			$orderBy = 'datedep_Asc';
		
		$idPilote = $identity->id_user_pilote;
		$planning = new Aeroport_Planning();
		$tableVol = new Vol();
		$tableAstreinte = new Astreinte();
		
		$debutJournee = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
		$finJournee = mktime(23, 59, 59, date('m'), date('d'), date('Y'));
		$timestampLundi = $planning->getTimestampFirstMonday();
		$timestampDimanche = $planning->getTimestampLastSunday(1);
		
		switch ($orderBy){
			case 'datedep_Asc': $listeVolJournee = $tableVol->getVolByPiloteByDate($idPilote, $debutJournee, $finJournee, 'date_depart asc')->toArray(); break;
			case 'datedep_Desc': $listeVolJournee = $tableVol->getVolByPiloteByDate($idPilote, $debutJournee, $finJournee, 'date_depart desc')->toArray(); break;
			case 'datearr_Asc': $listeVolJournee = $tableVol->getVolByPiloteByDate($idPilote, $debutJournee, $finJournee, 'date_arrivee asc')->toArray(); break;
			case 'datearr_Desc': $listeVolJournee = $tableVol->getVolByPiloteByDate($idPilote, $debutJournee, $finJournee, 'date_arrivee desc')->toArray(); break;
			case 'heuredep_Asc': $listeVolJournee = $tableVol->getVolByPiloteByDate($idPilote, $debutJournee, $finJournee, 'heure_depart asc')->toArray(); break;
			case 'heuredep_Desc': $listeVolJournee = $tableVol->getVolByPiloteByDate($idPilote, $debutJournee, $finJournee, 'heure_depart desc')->toArray(); break;
			case 'heurearr_Asc': $listeVolJournee = $tableVol->getVolByPiloteByDate($idPilote, $debutJournee, $finJournee, 'heure_arrivee_effective asc')->toArray(); break;
			case 'heurearr_Desc': $listeVolJournee = $tableVol->getVolByPiloteByDate($idPilote, $debutJournee, $finJournee, 'heure_arrivee_effective asc')->toArray(); break;
			case 'aerdep_Asc': $listeVolJournee = $tableVol->getVolByPiloteByDate($idPilote, $debutJournee, $finJournee, 'aeroportDepart asc')->toArray(); break;
			case 'aerdep_Desc': $listeVolJournee = $tableVol->getVolByPiloteByDate($idPilote, $debutJournee, $finJournee, 'aeroportDepart desc')->toArray(); break;
			case 'aerarr_Asc': $listeVolJournee = $tableVol->getVolByPiloteByDate($idPilote, $debutJournee, $finJournee, 'aeroportArrivee asc')->toArray(); break;
			case 'aerarr_Desc': $listeVolJournee = $tableVol->getVolByPiloteByDate($idPilote, $debutJournee, $finJournee, 'aeroportArrivee asc')->toArray(); break;
		}
		
		switch ($orderBy){
			case 'datedep_Asc': $listeVolSemaine = $tableVol->getVolByPiloteByDate($idPilote, $timestampLundi, $timestampDimanche, 'date_depart asc')->toArray(); break;
			case 'datedep_Desc': $listeVolSemaine = $tableVol->getVolByPiloteByDate($idPilote, $timestampLundi, $timestampDimanche, 'date_depart desc')->toArray(); break;
			case 'datearr_Asc': $listeVolSemaine = $tableVol->getVolByPiloteByDate($idPilote, $timestampLundi, $timestampDimanche, 'date_arrivee asc')->toArray(); break;
			case 'datearr_Desc': $listeVolSemaine = $tableVol->getVolByPiloteByDate($idPilote, $timestampLundi, $timestampDimanche, 'date_arrivee desc')->toArray(); break;
			case 'heuredep_Asc': $listeVolSemaine = $tableVol->getVolByPiloteByDate($idPilote, $timestampLundi, $timestampDimanche, 'heure_depart asc')->toArray(); break;
			case 'heuredep_Desc': $listeVolSemaine = $tableVol->getVolByPiloteByDate($idPilote, $timestampLundi, $timestampDimanche, 'heure_depart desc')->toArray(); break;
			case 'heurearr_Asc': $listeVolSemaine = $tableVol->getVolByPiloteByDate($idPilote, $timestampLundi, $timestampDimanche, 'heure_arrivee_effective asc')->toArray(); break;
			case 'heurearr_Desc': $listeVolSemaine = $tableVol->getVolByPiloteByDate($idPilote, $timestampLundi, $timestampDimanche, 'heure_arrivee_effective asc')->toArray(); break;
			case 'aerdep_Asc': $listeVolSemaine = $tableVol->getVolByPiloteByDate($idPilote, $timestampLundi, $timestampDimanche, 'aeroportDepart asc')->toArray(); break;
			case 'aerdep_Desc': $listeVolSemaine = $tableVol->getVolByPiloteByDate($idPilote, $timestampLundi, $timestampDimanche, 'aeroportDepart desc')->toArray(); break;
			case 'aerarr_Asc': $listeVolSemaine = $tableVol->getVolByPiloteByDate($idPilote, $timestampLundi, $timestampDimanche, 'aeroportArrivee asc')->toArray(); break;
			case 'aerarr_Desc': $listeVolSemaine = $tableVol->getVolByPiloteByDate($idPilote, $timestampLundi, $timestampDimanche, 'aeroportArrivee asc')->toArray(); break;	
		}
		
		switch ($orderBy){
			case 'datedep_Asc': $listeAstreinteJournee = $tableAstreinte->getAstreintebyDatebyPilote($idPilote, $debutJournee, $finJournee, 'date_astreinte asc')->toArray(); break;
			case 'datedep_Desc': $listeAstreinteJournee = $tableAstreinte->getAstreintebyDatebyPilote($idPilote, $debutJournee, $finJournee, 'date_astreinte desc')->toArray(); break;
			case 'aer_Asc': $listeAstreinteJournee = $tableAstreinte->getAstreintebyDatebyPilote($idPilote, $debutJournee, $finJournee, 'nomAeroport asc')->toArray(); break;
			case 'aer_Desc': $listeAstreinteJournee = $tableAstreinte->getAstreintebyDatebyPilote($idPilote, $debutJournee, $finJournee, 'nomAeroport desc')->toArray(); break;
		}
		
		switch ($orderBy){
			case 'datedep_Asc': $listeAstreinteSemaine = $tableAstreinte->getAstreintebyDatebyPilote($idPilote, $timestampLundi, $timestampDimanche, 'date_astreinte asc')->toArray(); break;
			case 'datedep_Desc': $listeAstreinteSemaine = $tableAstreinte->getAstreintebyDatebyPilote($idPilote, $timestampLundi, $timestampDimanche, 'date_astreinte desc')->toArray(); break;
			case 'aer_Asc': $listeAstreinteSemaine = $tableAstreinte->getAstreintebyDatebyPilote($idPilote, $timestampLundi, $timestampDimanche, 'nomAeroport asc')->toArray(); break;
			case 'aer_Desc': $listeAstreinteSemaine = $tableAstreinte->getAstreintebyDatebyPilote($idPilote, $timestampLundi, $timestampDimanche, 'nomAeroport desc')->toArray(); break;
		}
		
		$this->view->order = $orderBy;
		$this->view->datedep = Aeroport_Tableau_OrderColumn::orderColumns($this, 'datedep', $orderBy, 'thDateEmbauche', 'Date de départ');
		$this->view->heuredep = Aeroport_Tableau_OrderColumn::orderColumns($this, 'heuredep', $orderBy, 'thDateEmbauche', 'Heure de départ');
		$this->view->aeroportdep = Aeroport_Tableau_OrderColumn::orderColumns($this, 'aerdep', $orderBy, 'thDateEmbauche', 'Aéroport de départ');
		$this->view->datearr = Aeroport_Tableau_OrderColumn::orderColumns($this, 'datearr', $orderBy, 'thDateEmbauche', 'Date d\'arrivée');
		$this->view->heurearr = Aeroport_Tableau_OrderColumn::orderColumns($this, 'heurearr', $orderBy, 'thDateEmbauche', 'Heure d\'arrivée');
		$this->view->aeroportarr = Aeroport_Tableau_OrderColumn::orderColumns($this, 'aerarr', $orderBy, 'thDateEmbauche', 'Aéroport d\'arrivée');
		
		$this->view->date = Aeroport_Tableau_OrderColumn::orderColumns($this, 'datedep', $orderBy, 'thDateEmbauche', 'Date d\'astreinte');
		$this->view->aeroport = Aeroport_Tableau_OrderColumn::orderColumns($this, 'aer', $orderBy, null, 'Aéroport d\'astreinte');
		
		$volJournee = Zend_Paginator::factory($listeVolJournee);
		$volJournee->setItemCountPerPage(25);
		$volJournee->setCurrentPageNumber($page);
		$this->view->volJournee = $volJournee;
		
		$volSemaine = Zend_Paginator::factory($listeVolSemaine);
		$volSemaine->setItemCountPerPage(25);
		$volSemaine->setCurrentPageNumber($page);
		$this->view->volSemaine = $volSemaine;
		
		$AstreinteJournee = Zend_Paginator::factory($listeAstreinteJournee);
		$AstreinteJournee->setItemCountPerPage(25);
		$AstreinteJournee->setCurrentPageNumber($page);
		$this->view->astreinteJournee = $AstreinteJournee;
		
		$AstreinteSemaine = Zend_Paginator::factory($listeAstreinteSemaine);
		$AstreinteSemaine->setItemCountPerPage(25);
		$AstreinteSemaine->setCurrentPageNumber($page);
		$this->view->astreinteSemaine = $AstreinteSemaine;
		
		$this->view->param = $this->getAllParams();
		
	}


	
}