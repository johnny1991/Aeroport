<?php
class PlanningController extends Zend_Controller_Action
{
	public function planningAction(){
		
	}
	
	public function planificationvolAction(){
		
		$form = new PlanificationVol();
		$params = $this->getRequest()->getParams();
		$laDateArray = explode('-', $params['date']);
		
		$this->view->timestamp = $params['date'];

		$tableLigne = new Ligne;
		$tableAeroport = new Aeroport;
		$tableVol = new Vol;
		$tableAvion = new Avion;
		
		$numeroLigne = $params['numeroligne'];
		$laLigne = $tableLigne->find($numeroLigne)->current();
		$dateArrivee = ($laLigne->heure_arrivee < $laLigne->heure_depart) ? $laDateArray[0].'-'.$laDateArray[1].'-'.(intval($laDateArray[2]) + 1) : $params['date'];
		$aeroport_origine = $tableAeroport->find($laLigne->id_aeroport_depart)->current();
		$aeroport_arrivee = $tableAeroport->find($laLigne->id_aeroport_arrivee)->current();
		
		$this->view->nomAeroportOrigine = $aeroport_origine->nom;
		$this->view->nomAeroportArrivee = $aeroport_arrivee->nom;
		$this->view->numeroLigne = $laLigne->numero_ligne;
		$this->view->laDate = $laDateArray[2].'/'.$laDateArray[1].'/'.$laDateArray[0];
		
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();

			if($form->isValid($data)){
				$leVol = $tableVol->getInfosVol($numeroLigne, $params['date']);
				$nbVol = count($leVol);
				
				$infosAeroportArrivee = $tableLigne->getAeroportByAeroportArrivee($numeroLigne);
				
				if($params['actions'] == 'Planifier'){
					$tabVol = array('dateDepart' => $params['date'], 
									'heureDepart' => $infosAeroportArrivee->heure_depart,
									'heureArrivee' => $infosAeroportArrivee->heure_arrivee,
									'distance' => $infosAeroportArrivee->distance,
									'longueurPiste' => $infosAeroportArrivee->longueur_piste,
									'numeroLigne' => $params['numeroligne'],
									'idTypeAvion' => $form->getValue('avion'));
				}
				else{
					$idAvionVol = $tableVol->getInfosVolWithAvion($numeroLigne, $params['date']);
					$tabVol = array('dateDepart' => $params['date'],
							'heureDepart' => $infosAeroportArrivee->heure_depart,
							'heureArrivee' => $infosAeroportArrivee->heure_arrivee,
							'distance' => $infosAeroportArrivee->distance,
							'longueurPiste' => $infosAeroportArrivee->longueur_piste,
							'numeroLigne' => $params['numeroligne'],
							'idTypeAvion' => $form->getValue('avion'),
							'idAvion' => $idAvionVol->id_avion);
				}
				
				$idAvion = $tableAvion->getAvionDispoByTypeByLigne($tabVol, $params['actions']);
				
				if($nbVol == 0){
					
					$idVol = $tableVol->getLastId($numeroLigne) + 1;
					
					$Vol = $tableVol->createRow();
					$Vol->id_vol = $idVol;
					$Vol->numero_ligne = $numeroLigne;
					$Vol->id_aeroport_depart_effectif = $laLigne->id_aeroport_depart;
					$Vol->id_aeroport_arrivee_effectif = $laLigne->id_aeroport_arrivee;
					$Vol->date_depart = $params['date'];
					$Vol->date_arrivee = $dateArrivee;
					$Vol->id_avion = $idAvion->id_avion;
					$Vol->id_pilote = $form->getValue('pilote');
					$Vol->id_copilote = $form->getValue('co_pilote');
					$Vol->heure_arrivee_effective = $laLigne->heure_arrivee;
					$Vol->tarif_effectif = $laLigne->tarif;
					$Vol->save();
				}
				else{
					$leVol->id_avion = $idAvion->id_avion;
					$leVol->id_pilote = $form->getValue('pilote');
					$leVol->id_copilote = $form->getValue('co_pilote');
					$leVol->heure_arrivee_effective = $laLigne->heure_arrivee;
					$leVol->save();
				}

				$redirector = $this->_helper->getHelper('Redirector');
				$redirector->gotoUrl('/planning/listevol/date/'.$this->getParam('date'));
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

		$timestamp = strtotime($this->getParam('date'));
		
		if($timestamp < 1325379600)
			$timestamp = 1325379600;
		
		$this->view->timestampNext = date('Y-m-d', $timestamp + 86400);
		$this->view->timestampPrec = date('Y-m-d', $timestamp - 86400);
		
		$NumJour = date('N', $timestamp);
		$NumMois = date('m', $timestamp);
		
		$formatDate = date('Y-m-d', $timestamp);

		$PlanningDate = new Aeroport_Planning($NumJour, $NumMois);
		$timestampPremierLundi = $PlanningDate->getTimestampFirstMonday();
		$timestampDernierDimanche = $PlanningDate->getTimestampLastSunday();
		
		$leJour = $PlanningDate->getTranslateDay();
		$leMois = $PlanningDate->getTranslateMonth();
		
		$tabVol = array();
		$TablePeriodicite = new Periodicite;
		$TableAeroport = new Aeroport;
		$TableVol = new Vol;
		$TableAstreinte = new Astreinte;
		$TablePilote = new Pilote;
		$TableAvion = new Avion;
		
		$this->view->laDate = $leJour.' '.date('d', $timestamp).' '.$leMois.' '.date('Y', $timestamp);
		
		if($timestamp <= $timestampDernierDimanche + 86400){
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
					$vols = $TableVol->fetchRow($reqVol);	
					$nbVol = count($vols);
					
					if($nbVol == 0){
						$tabVol[$ligne->numero_ligne]['options'] = 'Planifier';
						$tabVol[$ligne->numero_ligne]['error'] = false;
					}
					else{
						$testPilote = count($TablePilote->checkPiloteDispo($vols->date_depart, $vols->numero_ligne, $vols->id_pilote));
						$testCoPilote = count($TablePilote->checkPiloteDispo($vols->date_depart, $vols->numero_ligne, $vols->id_copilote));
						$testAvion = count($TableAvion->checkAvionDispo($vols->date_depart, $vols->numero_ligne, $vols->id_avion));
						
						if($testPilote != 0 && $testCoPilote != 0 && $testAvion != 0){
							$tabVol[$ligne->numero_ligne]['error'] = false;
						}
						else{
							$tabVol[$ligne->numero_ligne]['error'] = true;
						}
						
						$tabVol[$ligne->numero_ligne]['options'] = 'Modifier';
					}
				}
				
				//Sélection des vols à la carte pour les vols correspondants à la date;
				
				$subReqPeriodicite = $TablePeriodicite->getReqIdVolPeriodique($NumJour);
				$reqVolsCarte = $TableVol->select()
										->setIntegrityCheck(false)
										->from(array('v' => 'vol'))
										->join(array('l' => 'ligne'), 'v.numero_ligne = l.numero_ligne')
										->where('v.date_depart = \''.$formatDate.'\' AND (id_avion IS NULL OR v.numero_ligne NOT IN ('.$subReqPeriodicite.'))');
				
				$volsCarte = $TableVol->fetchAll($reqVolsCarte);
	
				foreach($volsCarte as $volCarte){
						
					$aeroport_origine = $TableAeroport->find($volCarte->id_aeroport_depart)->current();
					$aeroport_arrivee = $TableAeroport->find($volCarte->id_aeroport_arrivee)->current();
					
					$tabVol[$volCarte['numero_ligne']] = array(
							'numero_ligne' => $volCarte->numero_ligne,
							'aeroport_origine' => $aeroport_origine->id_aeroport,
							'nom_aeroport_origine' =>$aeroport_origine->nom,
							'heure_depart' => $volCarte->heure_depart,
							'aeroport_arrivee' => $aeroport_arrivee->id_aeroport,
							'nom_aeroport_arrivee' =>$aeroport_origine->nom,
							'heure_arrivee' => $volCarte->heure_arrivee,
							'date_depart' => $formatDate,
							'numero_ligne' => $volCarte->numero_ligne);
					
					if($volCarte['id_avion'] == null){
						$tabVol[$volCarte['numero_ligne']]['options'] = 'Planifier';
						$tabVol[$volCarte['numero_ligne']]['error'] = false;
					}	
					else{
						$tabVol[$volCarte['numero_ligne']]['options'] = 'Modifier';
						
						$testPilote = count($TablePilote->checkPiloteDispo($volCarte->date_depart, $volCarte->numero_ligne, $volCarte->id_pilote));
						$testCoPilote = count($TablePilote->checkPiloteDispo($volCarte->date_depart, $volCarte->numero_ligne, $volCarte->id_copilote));
						$testAvion = count($TableAvion->checkAvionDispo($volCarte->date_depart, $volCarte->numero_ligne, $volCarte->id_avion));
						
						if($testPilote != 0 && $testCoPilote != 0 && $testAvion != 0){
							$tabVol[$volCarte['numero_ligne']]['error'] = false;
						}
						else{
							$tabVol[$volCarte['numero_ligne']]['error'] = true;
						}
					}
					
				}
				
				//Triage du tableau pour remonter les vols à planifier + envoie sur la vue;
				$tabVol = Aeroport_Fonctions::array_arsort($tabVol,'options');
				$this->view->assign('tabLigne', $tabVol);
				
				//Prévoir un équipage d'astreinte pour un aéroport;
				$listeAeroportVol = array();
				foreach($tabVol as $vol){
					if(array_key_exists($vol['aeroport_origine'], $listeAeroportVol)){
						$listeAeroportVol[$vol['aeroport_origine']] += 1;
					}
					else{
						$listeAeroportVol[$vol['aeroport_origine']] = 1;
					}
				}
				
				$listeAeroportAstreinte = array();
				foreach($listeAeroportVol as $aeroport => $value){
					if($value >= 5){
						$reqAstreinte = $TableAstreinte->select()->from($TableAstreinte)->where('DATE(date_astreinte) = ?', $formatDate)->where('id_aeroport = ?', $aeroport);
						$volAstreinte = $TableAstreinte->fetchAll($reqAstreinte);
						
						$reqAeroport = $TableAeroport->select()->from($TableAeroport)->where('id_aeroport = ?', $aeroport);
						$aeroportNom = $TableAeroport->fetchRow($reqAeroport);
						
						if(count($volAstreinte) == 0){
							$listeAeroportAstreinte[$aeroport]['option'] = 'Planifier';
							$listeAeroportAstreinte[$aeroport]['nom'] = $aeroportNom->nom;
							$listeAeroportAstreinte[$aeroport]['date'] = $formatDate;
							$listeAeroportAstreinte[$aeroport]['nbvol'] = $value;
						}
						else{
							$listeAeroportAstreinte[$aeroport]['option'] = 'Modifier';
							$listeAeroportAstreinte[$aeroport]['nom'] = $aeroportNom->nom;
							$listeAeroportAstreinte[$aeroport]['date'] = $formatDate;
							$listeAeroportAstreinte[$aeroport]['nbvol'] = $value;
						}
					}
				}
				
				$this->view->assign('tabAeroportAstreinte', $listeAeroportAstreinte);
			}
			else{
				
				$reqVol = $TableVol->select()->from($TableVol)->where('date_depart = ?', $formatDate);
				$vols = $TableVol->fetchAll($reqVol);
		
				foreach($vols as $vol){
					$ligne = $vol->findParentLigne();
					
					$aeroport_origine = $TableAeroport->find($vol->id_aeroport_depart_effectif)->current();
					$aeroport_arrivee = $TableAeroport->find($vol->id_aeroport_arrivee_effectif)->current();
					
					$tabVol[$vol->numero_ligne] = array(
							'idVol' => $vol->id_vol,
							'numero_ligne' => $ligne->numero_ligne,
							'aeroport_origine' => $aeroport_origine->id_aeroport,
							'nom_aeroport_origine' =>$aeroport_origine->nom,
							'heure_depart' => $ligne->heure_depart,
							'aeroport_arrivee' => $aeroport_arrivee->id_aeroport,
							'nom_aeroport_arrivee' =>$aeroport_origine->nom,
							'heure_arrivee' => $vol->heure_arrivee_effective,
							'date_depart' => $formatDate,
							'error' => false,
							'options' => 'Consulter');
				}
				
				//$tabVol = Aeroport_Fonctions::array_asort($tabVol,'numero_ligne');
				$this->view->assign('tabLigne', $tabVol);
			}
		}
		
		if($this->getRequest()->getParam('page'))
			$page = $this->getRequest()->getParam('page');
		else
			$page = 1;
		
		$paginator = Zend_Paginator::factory($tabVol);
		$paginator->setItemCountPerPage(25);
		$paginator->setCurrentPageNumber($page);
		$this->view->paginator = $paginator;
	}
	
	public function recherchepiloteAction(){
		$this->_helper->layout->disableLayout();
		
		$heureArrivee = $this->_getParam('heureArrivee');
		$heureDepart = $this->_getParam('heureDepart');
		$dateDepart = $this->_getParam('dateDepart');
		$idTypeAvion = $this->_getParam('idTypeAvion');
		$action = $this->_getParam('action');
		$numeroLigne = $this->_getParam('numeroligne');
		
		if($this->_hasParam('idPilote')){
			$idPilote = $this->_getParam('idPilote');
			$idCoPilote = $this->_getParam('idCoPilote');
		}
		
		$TablePilote = new Pilote();
		
		if($action == 'Planifier')
			$infosPilote = $TablePilote->getPiloteByTypeAvion($heureDepart, $heureArrivee, $dateDepart, $idTypeAvion);
		else
			$infosPilote = $TablePilote->getPiloteByTypeAvionUpdate($heureDepart, $heureArrivee, $dateDepart, $idTypeAvion, $numeroLigne, $idPilote, $idCoPilote);

		foreach($infosPilote as $pilote){
			echo '<option class="pilote-'.$pilote->id_pilote.'" value="'.$pilote->id_pilote.'">'.$pilote->nom.' '.$pilote->prenom.'</option>';
		}
	}
	
	public function consultervolAction(){
		$numeroLigne = $this->getParam('numeroligne');
		$idVol = $this->getParam('idvol');
		
		$TableVol = new Vol;
		$TableAeroport = new Aeroport;
		$TableLigne = new Ligne;
		$TablePilote = new Pilote;
		$TableAvion = new Avion;
		$TableTypeAvion = new TypeAvion;
		
		//Sort toutes les informations concernant le vol;
		$reqVol = $TableVol->select()->from($TableVol)->where('id_vol = ?', $idVol)->where('numero_ligne = ?', $numeroLigne);
		$infosVol = $TableVol->fetchRow($reqVol);
		
		$aeroportDepart = $TableAeroport->find($infosVol->id_aeroport_depart_effectif)->current();
		$aeroportArrivee = $TableAeroport->find($infosVol->id_aeroport_arrivee_effectif)->current();
		$pilote = $TablePilote->find($infosVol->id_pilote)->current();
		$coPilote = $TablePilote->find($infosVol->id_copilote)->current();
		$avion = $TableAvion->find($infosVol->id_avion)->current();
		$typeAvion = $TableTypeAvion->find($avion->id_type_avion)->current();
		$ligne = $TableLigne->find($infosVol->numero_ligne)->current();
		
		//Toutes les informations sont dans un tableau;
		$tabInfosVol = array(
							'idVol' => $infosVol->id_vol, 
							'numeroLigne' => $infosVol->numero_ligne,
							'dateDepart' => $infosVol->date_depart,
							'dateArrivee' => $infosVol->date_arrivee,
							'heureDepart' => $ligne->heure_depart,
							'heureArrivee' => $infosVol->heure_arrivee_effective,
							'aeroportDepart' => $aeroportDepart->nom,
							'aeroportArrivee' => $aeroportArrivee->nom,
							'nomPilote' => $pilote->nom.' '.$pilote->prenom,
							'nomCoPilote' => $coPilote->nom.' '.$coPilote->prenom,
							'nomAvion' => $typeAvion->libelle,
							'tarif' => $infosVol->tarif
							);
		
		$this->view->assign('infosVol', $tabInfosVol);
		
		$this->view->timestamp = strtotime($infosVol->date_depart);
	}
	
	public function planificationastreinteAction(){
		$params = $this->getRequest()->getParams();
		
		$tableAeroport = new Aeroport;
		$tableAstreinte = new Astreinte;
		
		$idAeroport = $params['idaeroport'];
		$aeroport = $tableAeroport->find($idAeroport)->current();
		
		$dateExplode = explode('-', $params['date']);
		
		$this->view->laDate = $dateExplode[2].'/'.$dateExplode[1].'/'.$dateExplode[0];
		$this->view->timestamp = $params['date'];
		$this->view->nomAeroportOrigine = $aeroport->nom;
		
		$form = new PlanificationAstreinte();
		
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();

			if($form->isValid($data)){
				
				$nbPilote = floor($params['nbvol'] / 5) * 2;

				$lAstreinte = $tableAstreinte->getInfosAstreinte($params['date'], $idAeroport);
				$nbAstreinte = count($lAstreinte);
				
				if($nbAstreinte == 0){
					
					for($i=0;$i<=$nbPilote - 1 ;$i++){
						$Astreinte = $tableAstreinte->createRow();
						$Astreinte->id_aeroport = $idAeroport;
						$Astreinte->id_pilote = $form->getValue('pilote'.$i);
						$Astreinte->date_astreinte = $params['date'];
						$Astreinte->save();
					}
				}
				else{
					$index = 0;
					foreach($lAstreinte as $info){
						
						$info->id_aeroport = $idAeroport;
						$info->id_pilote = $form->getValue('pilote'.$index);
						$info->date_astreinte = $params['date'];
						$info->save();
						
						$index++;
					}
					
					if($nbPilote > $nbAstreinte){
						$nbRestant = $nbPilote  - $nbAstreinte;
						$tourPlus = ($index + $nbRestant) - 1;
						
						for($j=$index;$j<= $tourPlus;$j++){
							$Astreinte = $tableAstreinte->createRow();
							$Astreinte->id_aeroport = $idAeroport;
							$Astreinte->id_pilote = $form->getValue('pilote'.$j);
							$Astreinte->date_astreinte = $params['date'];
							$Astreinte->save();
						}
					}
				}

				$redirector = $this->_helper->getHelper('Redirector');
				$redirector->gotoUrl('/planning/listevol/date/'.$params['date']);
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
	
	public function init(){
		$this->view->headLink()->appendStylesheet('/css/calendar.jquery.css');
		$this->view->headScript()->appendFile('/js/PlanningFonction.js');
		$this->view->headLink()->appendStylesheet('/css/PlanningCSS.css');
		
		parent::init();
	}
}