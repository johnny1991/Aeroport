<?php
class PlanningController extends Zend_Controller_Action
{
	public function planningAction(){
		
	}
	
	public function planificationvolAction(){
		
		$form = new PlanificationVol($this->getParam('date'), $this->getParam('numeroligne'), $this->getParam('actions'));

		$laDateArray = explode('-', $this->getParam('date'));

		$tableLigne = new Ligne;
		$tableAeroport = new Aeroport;
		$tableVol = new Vol;
		$tableAvion = new Avion;
		
		$numeroLigne = $this->getParam('numeroligne');
		
		$laLigne = $tableLigne->find($numeroLigne)->current();
		
		if($laLigne->heure_arrivee < $laLigne->heure_depart)
			$dateArrivee = $laDateArray[0].'-'.$laDateArray[1].'-'.intval($laDateArray[2]) + 1;
		else
			$dateArrivee = $this->getParam('date');
		
		$aeroport_origine = $tableAeroport->find($laLigne->id_aeroport_depart)->current();
		$aeroport_arrivee = $tableAeroport->find($laLigne->id_aeroport_arrivee)->current();
		
		$this->view->nomAeroportOrigine = $aeroport_origine->nom;
		$this->view->nomAeroportArrivee = $aeroport_arrivee->nom;
		$this->view->numeroLigne = $laLigne->numero_ligne;
		$this->view->laDate = $laDateArray[2].'/'.$laDateArray[1].'/'.$laDateArray[0];
		
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();

			if($form->isValid($data)){
				
				$reqVol = $tableVol->select()->from($tableVol)->where('numero_ligne = ?', $numeroLigne)->where('date_depart = ?', $this->getParam('date'));
				$leVol = $tableVol->fetchRow($reqVol);
				$testVolExist = count($leVol);
				
				$infosLigne = $tableLigne->getAeroportByAeroportArrivee($numeroLigne);
				
				$tabVol = array('dateDepart' => $this->getParam('date'), 
								'heureDepart' => $infosLigne->heure_depart,
								'distance' => $infosLigne->distance,
								'longueurPiste' => $infosLigne->longueur_piste,
								'numeroLigne' => $this->getParam('numeroligne'),
								'idTypeAvion' => $form->getValue('avion'));
				
				$idAvion = $tableAvion->getAvionDispoByType($tabVol, $this->getParam('actions'));

				if($testVolExist == 0){
					$idVol = $tableVol->getLastId($numeroLigne) + 1;
					
					$Vol = $tableVol->createRow();
					$Vol->id_vol = $idVol;
					$Vol->numero_ligne = $numeroLigne;
					$Vol->id_aeroport_depart_effectif = $laLigne->id_aeroport_depart;
					$Vol->id_aeroport_arrivee_effectif = $laLigne->id_aeroport_arrivee;
					$Vol->date_depart = $this->getParam('date');
					$Vol->date_arrivee = $dateArrivee;
					$Vol->id_avion = $idAvion->id_avion;
					$Vol->id_pilote = $form->getValue('pilote');
					$Vol->id_copilote = $form->getValue('co_pilote');
					$Vol->heure_arrivee_effective = $laLigne->heure_arrivee;
					$Vol->save();
				}
				else{
					$leVol->id_avion = $idAvion->id_avion;
					$leVol->id_pilote = $form->getValue('pilote');
					$leVol->id_copilote = $form->getValue('co_pilote');
					$leVol->heure_arrivee_effective = $laLigne->heure_arrivee;
					$leVol->save();
				}
				
				//echo 'Planification réussi';
				$redirector = $this->_helper->getHelper('Redirector');
				$redirector->gotoUrl('/planning/listevol/date/'.strtotime($this->getParam('date')));
			}
			else{
				$form->populate($data);
				$this->view->Form = $form;
			}
		}
		else{
			$this->view->Form = $form;
		}
	
	}
	
	public function listevolAction(){
		$timestamp = $this->getParam('date');
		
		if($timestamp < 1325379600)
			$timestamp = 1325379600;
		
		$this->view->timestampNext = $timestamp + 86400;
		$this->view->timestampPrec = $timestamp - 86400;
		
		$NumJour = date('N', $timestamp);
		$NumMois = date('m', $timestamp);
		
		$formatDate = date('Y-m-d', $timestamp);

		$PlanningDate = new Aeroport_Planning($NumJour, $NumMois);
		$timestampPremierLundi = $PlanningDate->getTimestampFirstMonday();
		
		$leJour = $PlanningDate->getTranslateDay();
		$leMois = $PlanningDate->getTranslateMonth();
		
		$tabVol = array();
		$TablePeriodicite = new Periodicite;
		$TableAeroport = new Aeroport;
		$TableVol = new Vol;
		
		$this->view->laDate = $leJour.' '.date('d', $timestamp).' '.$leMois.' '.date('Y', $timestamp);
		
		//Si le jour sélectionné est présent dans la semaine S à S+4
		if($timestamp >= $timestampPremierLundi){
			
			//Création du tableau contenant tous les vols périodique selon le jour $NumJour;
			$periodiciteReq = $TablePeriodicite->select()->from($TablePeriodicite)->where('numero_jour = ?', $NumJour);
			$periodicites = $TablePeriodicite->fetchAll($periodiciteReq);
			
			foreach($periodicites as $periodicite){
				$ligne = $periodicite->findParentLigne();
				
				$aeroport_origine = $TableAeroport->find($ligne->id_aeroport_depart)->current();
				$aeroport_arrivee = $TableAeroport->find($ligne->id_aeroport_arrivee)->current();
				
				$tabVol[$ligne->numero_ligne] = array(
						'numero_ligne' => $ligne->numero_ligne,
						'aeroport_origine' => $aeroport_origine->id_aeroport,
						'nom_aeroport_origine' =>$aeroport_origine->nom,
						'heure_depart' => $ligne->heure_depart,
						'aeroport_arrivee' => $aeroport_arrivee->id_aeroport,
						'nom_aeroport_arrivee' =>$aeroport_arrivee->nom,
						'heure_arrivee' => $ligne->heure_arrivee,
						'date_depart' => $formatDate,
						'numero_ligne' => $ligne->numero_ligne);
				
				$reqVol = $TableVol->select()
								->from($TableVol)
								->where('numero_ligne = ?', $ligne->numero_ligne)
								->where('date_depart = ?', $formatDate);
				$vols = $TableVol->fetchAll($reqVol);	
				$nbVol = count($vols);
				
				if($nbVol == 0){
					$tabVol[$ligne->numero_ligne]['options'] = 'Planifier';
				}
				else{
					$tabVol[$ligne->numero_ligne]['options'] = 'Modifier';
				}
			}
			
			//Sélection des vols à la carte pour les vols correspondants à la date;
			$db = Zend_Registry::get('db');
			$reqVolsCarte = $db->select()->from(array('v'=>'vol'))
									->join(array('l'=>'ligne'), 'v.numero_ligne = l.numero_ligne')
									->where('l.periodique = ?', 0)
									->where('v.date_depart = ?', $formatDate);
			$volsCarte = $db->fetchAll($reqVolsCarte);

			foreach($volsCarte as $volCarte){
				
					
				$aeroport_origine = $TableAeroport->find($volCarte['id_aeroport_depart'])->current();
				$aeroport_arrivee = $TableAeroport->find($volCarte['id_aeroport_arrivee'])->current();
				
				$tabVol[$volCarte['numero_ligne']] = array(
						'numero_ligne' => $volCarte['numero_ligne'],
						'aeroport_origine' => $aeroport_origine->id_aeroport,
						'nom_aeroport_origine' =>$aeroport_origine->nom,
						'heure_depart' => $volCarte['heure_depart'],
						'aeroport_arrivee' => $aeroport_arrivee->id_aeroport,
						'nom_aeroport_arrivee' =>$aeroport_origine->nom,
						'heure_arrivee' => $volCarte['heure_arrivee'],
						'date_depart' => $formatDate,
						'numero_ligne' => $volCarte['numero_ligne']);
				
				if($volCarte['id_avion'] == null){
					$tabVol[$volCarte['numero_ligne']]['options'] = 'Planifier';
				}	
				else{
					$tabVol[$volCarte['numero_ligne']]['options'] = 'Modifier';
				}
				
			}
			$tabVol = Aeroport_Fonctions::array_arsort($tabVol,'options');
			$this->view->assign('tabLigne', $tabVol);
		}
		else{
			$reqVol = $TableVol->select()->from($TableVol)->where('date_depart = ?', $formatDate);
			$vols = $TableVol->fetchAll($reqVol);
			
			foreach($vols as $vol){
				$ligne = $vol->findParentLigne();
				
				$aeroport_origine = $TableAeroport->find($vol->id_aeroport_depart_effectif)->current();
				$aeroport_arrivee = $TableAeroport->find($vol->id_aeroport_arrivee_effectif)->current();
				
				$tabVol[$vol->id_vol] = array(
						'numero_ligne' => $ligne->numero_ligne,
						'aeroport_origine' => $aeroport_origine->id_aeroport,
						'nom_aeroport_origine' =>$aeroport_origine->nom,
						'heure_depart' => $ligne->heure_depart,
						'aeroport_arrivee' => $aeroport_arrivee->id_aeroport,
						'nom_aeroport_arrivee' =>$aeroport_origine->nom,
						'heure_arrivee' => $vol->heure_arrivee_effective,
						'options' => 'Consulter');
			}
			
			//$tabVol = Aeroport_Fonctions::array_asort($tabVol,'numero_ligne');
			$this->view->assign('tabLigne', $tabVol);
		}
	}
	
	public function recherchepiloteAction(){
		$this->_helper->layout->disableLayout();
		
		$heureDepart = $this->_getParam('heureDepart');
		$dateDepart = $this->_getParam('dateDepart');
		$idTypeAvion = $this->_getParam('idTypeAvion');
		$action = $this->_getParam('action');
		$numeroLigne = $this->_getParam('numeroligne');
		
		$TablePilote = new Pilote();
		
		if($action == 'Planifier')
			$infosPilote = $TablePilote->getPiloteByTypeAvion($heureDepart, $dateDepart, $idTypeAvion);
		else
			$infosPilote = $TablePilote->getPiloteByTypeAvionUpdate($heureDepart, $dateDepart, $idTypeAvion, $numeroLigne);

		foreach($infosPilote as $pilote){
			echo '<option class="pilote-'.$pilote->id_pilote.'" value="'.$pilote->id_pilote.'">'.$pilote->nom.' '.$pilote->prenom.'</option>';
		}
	}
	
	public function init(){
		$this->view->headLink()->appendStylesheet('/css/calendar.jquery.css');
		$this->view->headScript()->appendFile('/js/PlanningFonction.js');
		$this->view->headLink()->appendStylesheet('/css/PlanningCSS.css');
		
		parent::init();
	}
}