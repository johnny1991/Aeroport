<?php
class PlanningController extends Zend_Controller_Action
{
	public function planningAction(){
		$this->view->headLink()->appendStylesheet('/css/calendar.jquery.css');
		$this->view->headScript()->appendFile('/js/PlanningFonction.js');
	}
	
	public function listevolAction(){
		$this->view->headLink()->appendStylesheet('/css/PlanningCSS.css');
		
		$timestamp = round($this->getParam('date') / 1000);
		
		$this->view->timestampNext = $timestamp + (24 * 60 * 60);
		$this->view->timestampPrec = $timestamp - (24 * 60 * 60);
		
		$NumJour = date('N', $timestamp);
		$NumMois = date('m', $timestamp);
		
		$laDateTanslate = new Aeroport_Planning($NumJour, $NumMois);
		
		$leJour = $laDateTanslate->getTranslateDay();
		$leMois = $laDateTanslate->getTranslateMonth();
		
		$this->view->laDate = $leJour.' '.date('d', $timestamp).' '.$leMois.' '.date('Y', $timestamp);
		
		//Création du tableau contenant tous les vols périodique selon le jour $NumJour;
		$tabVol = array();
		
		$TablePeriodicite = new Periodicite;
		$TableAeroport = new Aeroport;
		
		$periodiciteReq = $TablePeriodicite->select()->from($TablePeriodicite)->where('numero_jour = ?', $NumJour);
		$periodicites = $TablePeriodicite->fetchAll($periodiciteReq);
		
		foreach($periodicites as $periodicite){
			$ligne = $periodicite->findParentLigne();
			
			$aeroport_origine = $TableAeroport->find($ligne->id_aeroport_origine)->current();
			$ville_origine = $aeroport_origine->findParentVille();
			$pays_origine = $ville_origine->findParentPays();
			
			$aeroport_arrivee = $TableAeroport->find($ligne->id_aeroport_arrivee)->current();
			$ville_arrivee = $aeroport_arrivee->findParentVille();
			$pays_arrivee = $ville_arrivee->findParentPays();
			
			$tabVol[$ligne->numero_ligne] = array(
							'pays_origine' => $pays_origine->nom_pays,
							'aeroport_origine' => $aeroport_origine->id_aeroport,
							'heure_depart' => $ligne->heure_depart,
							'pays_arrivee' => $pays_arrivee->nom_pays,
							'aeroport_arrivee' => $aeroport_arrivee->id_aeroport,
							'heure_arrivee' => $ligne->heure_arrivee);
		}
		
		$this->view->assign('tabLigne', $tabVol);
		
	}
	
	public function init(){
		parent::init();
		
	}
}