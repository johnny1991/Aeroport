<?php
class PlanningController extends Zend_Controller_Action
{
	public function indexAction(){
		
	}
	
	public function planifierVolAction(){
		
		if(!($this->hasParam('date')) || !($this->hasParam('numeroligne'))){
			Aeroport_Fonctions::redirector('/planning');
		}else{
			$dateParam = htmlentities($this->getParam('date'), ENT_QUOTES, 'UTF-8');
			$numeroligneParam = htmlentities($this->getParam('numeroligne'), ENT_QUOTES, 'UTF-8');
			
			$params = array($dateParam => 'date', $numeroligneParam => 'int');
			if(!Aeroport_Fonctions::validParam($params)){
				Aeroport_Fonctions::redirector('/planning');
			}
		}
		
		if($this->hasParam('error')){
			$erreur = htmlentities($this->getParam('error'), ENT_QUOTES, 'UTF-8');
			$params = array($erreur => 'error');
			
			if(Aeroport_Fonctions::validParam($params)){
				$this->view->error = $this->getParam('error');
			}
			else{
				Aeroport_Fonctions::redirector('/planning');
			}
		}

		$laDateArray = explode('-', $dateParam);
		
		$this->view->timestamp = $dateParam;

		$tableLigne = new Ligne;
		$tableAeroport = new Aeroport;
		$tableVol = new Vol;
		$tableAvion = new Avion;
		
		$numeroLigne = $numeroligneParam;
		$laLigne = $tableLigne->find($numeroLigne)->current();
		if(count($laLigne) != 0){
			$form = new PlanificationVol();
			
			$dateArrivee = ($laLigne->heure_arrivee < $laLigne->heure_depart) ? $laDateArray[0].'-'.$laDateArray[1].'-'.(intval($laDateArray[2]) + 1) : $dateParam;
			$aeroport_origine = $tableAeroport->find($laLigne->id_aeroport_depart)->current();
			$aeroport_arrivee = $tableAeroport->find($laLigne->id_aeroport_arrivee)->current();
			
			$this->view->nomAeroportOrigine = $aeroport_origine->nom;
			$this->view->nomAeroportArrivee = $aeroport_arrivee->nom;
			$this->view->numeroLigne = $laLigne->numero_ligne;
			$this->view->laDate = $laDateArray[2].'/'.$laDateArray[1].'/'.$laDateArray[0];
			
			if(($numeroligneParam != NULL) && ($laLigne != NULL)){
				
				$vol = $tableVol->getInfosVol($numeroLigne, $dateParam);
				
				$this->view->ligne = $laLigne;
				$this->view->jours = $laLigne->findJourSemaineViaPeriodicite();
				$this->view->nbJours = count($laLigne->findJourSemaineViaPeriodicite());
				$this->view->aeroport_origine = $laLigne->findParentRow('Aeroport','aeroport_origine');
				$this->view->aeroport_depart = $laLigne->findParentRow('Aeroport','aeroport_depart');
				$this->view->aeroport_arrivee = $laLigne->findParentRow('Aeroport','aeroport_arrivee');
				$this->view->villeOrigine = $laLigne->findParentRow('Aeroport','aeroport_origine')->findParentRow('Ville');
				$this->view->paysOrigine = $this->view->villeOrigine->findParentRow('Pays');
				$this->view->villeDepart = $laLigne->findParentRow('Aeroport','aeroport_depart')->findParentRow('Ville');
				$this->view->paysDepart = $this->view->villeDepart->findParentRow('Pays');
				$this->view->villeArrivee = $laLigne->findParentRow('Aeroport','aeroport_arrivee')->findParentRow('Ville');
				$this->view->paysArrive = $this->view->villeArrivee->findParentRow('Pays');
				
				if(($dateParam != NULL) && ($vol != NULL)){
	
					$this->view->vol = $vol;
					$this->view->aeroport_depart_effectif = $vol->findParentRow('Aeroport','id_aeroport_depart_effectif');
					$this->view->aeroport_arrivee_effectif = $vol->findParentRow('Aeroport','id_aeroport_arrivee_effectif');
					
				}
				else{
					$this->view->nextDate = (intVal($laDateArray[2]) + 1).'/'.$laDateArray[1].'/'.$laDateArray[0];
					$this->view->idVol = $tableVol->getLastId($numeroLigne) + 1;
				}
			}
			
			if($this->getRequest()->isPost()){
				$data = $this->getRequest()->getPost();
	
				if($form->isValid($data)){
					$leVol = $tableVol->getInfosVol($numeroLigne, $dateParam);
					$nbVol = count($leVol);
					
					if($nbVol == 0){
						$idAvion = $tableAvion->getAvionDispoByTypeByVol($numeroLigne, $dateParam, $form->getValue('avion'));
						$idVol = $tableVol->getLastId($numeroLigne) + 1;
						
						$idAvionDispo = '';
						foreach($idAvion as $avion){
							$req = $tableVol->checkDispoAvion($avion->id_avion, $dateParam);
							
							if(count($req) == 0){
								$idAvionDispo = $avion->id_avion;
								break;
							}
						}
						
						$Vol = $tableVol->createRow();
						$Vol->id_vol = $idVol;
						$Vol->numero_ligne = $numeroLigne;
						$Vol->id_aeroport_depart_effectif = $laLigne->id_aeroport_depart;
						$Vol->id_aeroport_arrivee_effectif = $laLigne->id_aeroport_arrivee;
						$Vol->date_depart = $dateParam;
						$Vol->date_arrivee = $dateArrivee;
						$Vol->id_avion = $idAvionDispo;
						$Vol->id_pilote = $form->getValue('pilote0');
						$Vol->id_copilote = $form->getValue('pilote1');
						$Vol->heure_arrivee_effective = $laLigne->heure_arrivee;
						$Vol->tarif_effectif = $laLigne->tarif;
						$Vol->save();
					}
					else{
						$idAvion = $tableAvion->getAvionDispoByTypeByVol($numeroLigne, $dateParam, $form->getValue('avion'), true);
						
						$idAvionDispo = '';
						foreach($idAvion as $avion){
							$req = $tableVol->checkDispoAvion($avion->id_avion, $dateParam);
								
							if(count($req) == 0){
								$idAvionDispo = $avion->id_avion;
								break;
							}
						}
						
						if($idAvionDispo == ''){
							$idAvionDispo = $leVol->id_avion;
						}
						
						$leVol->id_avion = $idAvionDispo;
						$leVol->id_pilote = $form->getValue('pilote0');
						$leVol->id_copilote = $form->getValue('pilote1');
						$leVol->heure_arrivee_effective = $laLigne->heure_arrivee;
						$leVol->save();
					}
					
					Aeroport_Fonctions::redirector('/planning/liste-vol/date/'.$dateParam);
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
		else{
			Aeroport_Fonctions::redirector('/planning/liste-vol/date/'.$dateParam);
		}
	}
	
	public function listeVolAction(){
		
		if($this->hasParam('date')){
			$date = htmlentities($this->getParam('date'), ENT_QUOTES, 'UTF-8');
			$params = array($date => 'date');
			
			if(Aeroport_Fonctions::validParam($params))
				$timestamp = strtotime($date);
			else{
				Aeroport_Fonctions::redirector('/planning');
			}
		}	
		else
			$timestamp = time();
		
		if($timestamp < 1325379600)
			$timestamp = 1325379600;
		
		$this->view->timestampNext = date('Y-m-d', $timestamp + 86400);
		$this->view->timestampPrec = date('Y-m-d', $timestamp - 86400);
		
		$NumJour = date('N', $timestamp);
		$NumMois = date('m', $timestamp);
		
		$formatDate = date('Y-m-d', $timestamp);

		$PlanningDate = new Aeroport_Planning();
		$timestampPremierLundi = $PlanningDate->getTimestampFirstMonday();
		$timestampDernierDimanche = $PlanningDate->getTimestampLastSunday(4);

		$leJour = $PlanningDate->getTranslateDay($NumJour);
		$leMois = $PlanningDate->getTranslateMonth($NumMois);
		
		$tabVol = array();
		$TablePeriodicite = new Periodicite;
		$TableAeroport = new Aeroport;
		$TableVol = new Vol;
		$TableAstreinte = new Astreinte;
		$TablePilote = new Pilote;
		$TableAvion = new Avion;
		
		$this->view->laDate = $leJour.' '.date('d', $timestamp).' '.$leMois.' '.date('Y', $timestamp);
		
		if($timestamp <= $timestampDernierDimanche){
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
							if($testPilote == 0){
								$tabVol[$ligne->numero_ligne]['error-pilote'] = true;
							}
							
							if($testCoPilote == 0){
								$tabVol[$ligne->numero_ligne]['error-copilote'] = true;
							}
							
							if($testAvion == 0){
								$tabVol[$ligne->numero_ligne]['error-avion'] = true;
							}
							
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
							if($testPilote == 0){
								$tabVol[$volCarte['numero_ligne']]['error-pilote'] = true;
							}
							
							if($testCoPilote == 0){
								$tabVol[$volCarte['numero_ligne']]['error-copilote'] = true;
							}
							
							if($testAvion == 0){
								$tabVol[$volCarte['numero_ligne']]['error-avion'] = true;
							}
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
				//Récupére tous les vols ayant a la date correspondante;
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
				
				$idAeroports = $TableAstreinte->getIdAeroportByDate($formatDate);
				$listeAeroportAstreinte = array();
				foreach($idAeroports as $aeroport){
					$nomAeroport = $TableAeroport->find($aeroport->id_aeroport)->current();
					$listeAeroportAstreinte[$aeroport->id_aeroport]['nom'] = $nomAeroport->nom;
					$listeAeroportAstreinte[$aeroport->id_aeroport]['date'] = $formatDate;
					$listeAeroportAstreinte[$aeroport->id_aeroport]['option'] = 'Consulter';
				}
				
				$this->view->assign('tabLigne', $tabVol);
			}
			
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
			
			$paginator = Zend_Paginator::factory($tabVol);
			$paginator->setItemCountPerPage(25);
			$paginator->setCurrentPageNumber($page);
			$this->view->paginator = $paginator;
			
			$paginatorAst = Zend_Paginator::factory($listeAeroportAstreinte);
			$paginatorAst->setItemCountPerPage(25);
			$paginatorAst->setCurrentPageNumber($page);
			$this->view->paginatorAstreinte = $paginatorAst;
			
		}else{
			Aeroport_Fonctions::redirector('/planning/liste-vol/date/'.date('Y-m-d', $timestampDernierDimanche));
		}
		
	}
	
	public function recherchepiloteAction(){
		$this->_helper->layout->disableLayout();
		
		$TableVol = new Vol;
		$TablePilote = new Pilote();
		
		$numeroLigne = htmlentities($this->getParam('numeroligne'), ENT_QUOTES, 'UTF-8');
		$dateDepart = htmlentities($this->getParam('dateDepart'), ENT_QUOTES, 'UTF-8');
		$idTypeAvion = htmlentities($this->getParam('idTypeAvion'), ENT_QUOTES, 'UTF-8');
		$update = htmlentities($this->getParam('update'), ENT_QUOTES, 'UTF-8');
		
		$params = array($numeroLigne => 'int', $dateDepart => 'date', $idTypeAvion => 'int', $update => 'bool');
		if(Aeroport_Fonctions::validParam($params)){
			if($update == 'false')
				$infosPilote = $TablePilote->getPiloteByTypeAvion($numeroLigne, $dateDepart, $idTypeAvion);
			else
				$infosPilote = $TablePilote->getPiloteByTypeAvion($numeroLigne, $dateDepart, $idTypeAvion, true);
			
			foreach($infosPilote as $pilote){
				echo '<option class="pilote-'.$pilote->id_pilote.'" value="'.$pilote->id_pilote.'">'.$pilote->nom.' '.$pilote->prenom.'</option>';
			}
		}
	}
	
	public function planifierAstreinteAction(){

		if(!($this->hasParam('idaeroport')) || !($this->hasParam('date')) || !($this->hasParam('nbvol'))){
			Aeroport_Fonctions::redirector('/planning');
		}
		else{
			$idaeroportParam = htmlentities($this->getParam('idaeroport'), ENT_QUOTES, 'UTF-8');
			$dateParam = htmlentities($this->getParam('date'), ENT_QUOTES, 'UTF-8');
			$nbvolParam = htmlentities($this->getParam('nbvol'), ENT_QUOTES, 'UTF-8');
				
			$params = array($idaeroportParam => 'idaeroport', $dateParam => 'date', $nbvolParam => 'int');
			if(!Aeroport_Fonctions::validParam($params)){
				Aeroport_Fonctions::redirector('/planning');
			}
		}
		
		$tableAeroport = new Aeroport;
		$tableAstreinte = new Astreinte;
		
		$aeroport = $tableAeroport->find($idaeroportParam)->current();
		
		$dateExplode = explode('-', $dateParam);
		
		$this->view->laDate = $dateExplode[2].'/'.$dateExplode[1].'/'.$dateExplode[0];
		$this->view->timestamp = $dateParam;
		$this->view->nomAeroportOrigine = $aeroport->nom;
		
		$form = new PlanificationAstreinte();
		
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();

			if($form->isValid($data)){
				
				$nbPilote = floor($nbvolParam / 5) * 2;

				$lAstreinte = $tableAstreinte->getInfosAstreinte($dateParam, $idaeroportParam);
				$nbAstreinte = count($lAstreinte);
				
				if($nbAstreinte == 0){
					
					for($i=0;$i<=$nbPilote - 1 ;$i++){
						$Astreinte = $tableAstreinte->createRow();
						$Astreinte->id_aeroport = $idaeroportParam;
						$Astreinte->id_pilote = $form->getValue('pilote'.$i);
						$Astreinte->date_astreinte = $dateParam;
						$Astreinte->save();
					}
				}
				else{
					$index = 0;
					foreach($lAstreinte as $info){
						
						$info->id_aeroport = $idaeroportParam;
						$info->id_pilote = $form->getValue('pilote'.$index);
						$info->date_astreinte = $dateParam;
						$info->save();
						
						$index++;
					}
					
					if($nbPilote > $nbAstreinte){
						$nbRestant = $nbPilote  - $nbAstreinte;
						$tourPlus = ($index + $nbRestant) - 1;
						
						for($j=$index;$j<= $tourPlus;$j++){
							$Astreinte = $tableAstreinte->createRow();
							$Astreinte->id_aeroport = $idaeroportParam;
							$Astreinte->id_pilote = $form->getValue('pilote'.$j);
							$Astreinte->date_astreinte = $dateParam;
							$Astreinte->save();
						}
					}
				}

				Aeroport_Fonctions::redirector('/planning/liste-vol/date/'.$dateParam);
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
	
	public function planningListeAction(){
		$dateToday = time();
		
		$PlanningDate = new Aeroport_Planning();
		$timestampPremierLundi = $PlanningDate->getTimestampFirstMonday();
		$timestampDernierDimanche = $PlanningDate->getTimestampLastSunday(4);
		
		$firstLundi = date('d', $timestampPremierLundi);
		$lastDimanche = date('d', $timestampDernierDimanche);
		
		$myMonth = date('m');
		$myYear = date('Y');
		$numDayMonth = date('t');
		
		$tabVol = array();
		$listeAeroportAstreinte = array();
		
		$tabVol = array();
		$TablePeriodicite = new Periodicite;
		$TableAeroport = new Aeroport;
		$TableVol = new Vol;
		$TableAstreinte = new Astreinte;
		$TablePilote = new Pilote;
		$TableAvion = new Avion;
		
		$listeHTML = '';
		for($i = 0; $i < 28; $i++){

			$nextDay = $firstLundi + $i;
			
			$formatDate = date('Y-m-d', mktime(0, 0 , 0, $myMonth, $nextDay, $myYear));
			$NumJour = date('N', mktime(0, 0 , 0, $myMonth, $nextDay, $myYear));
			
			
			$periodiciteReq = $TablePeriodicite->select()->from($TablePeriodicite)->where('numero_jour = ?', $NumJour);
			$periodicites = $TablePeriodicite->fetchAll($periodiciteReq);
			
			foreach($periodicites as $periodicite){
				$ligne = $periodicite->findParentLigne();
					
				$aeroport_origine = $TableAeroport->find($ligne->id_aeroport_depart)->current();
				$aeroport_arrivee = $TableAeroport->find($ligne->id_aeroport_arrivee)->current();
					
				$tabVol[$formatDate.' - '.$ligne->numero_ligne] = array(
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
					$tabVol[$formatDate.' - '.$ligne->numero_ligne]['options'] = 'Planifier';
					$tabVol[$formatDate.' - '.$ligne->numero_ligne]['error'] = false;
				}
				else{
					$testPilote = count($TablePilote->checkPiloteDispo($vols->date_depart, $vols->numero_ligne, $vols->id_pilote));
					$testCoPilote = count($TablePilote->checkPiloteDispo($vols->date_depart, $vols->numero_ligne, $vols->id_copilote));
					$testAvion = count($TableAvion->checkAvionDispo($vols->date_depart, $vols->numero_ligne, $vols->id_avion));
			
					if($testPilote != 0 && $testCoPilote != 0 && $testAvion != 0){
						$tabVol[$formatDate.' - '.$ligne->numero_ligne]['error'] = false;
					}
					else{
						if($testPilote == 0){
							$tabVol[$formatDate.' - '.$ligne->numero_ligne]['error-pilote'] = true;
						}
						
						if($testCoPilote == 0){
							$tabVol[$formatDate.' - '.$ligne->numero_ligne]['error-copilote'] = true;
						}
						
						if($testAvion == 0){
							$tabVol[$formatDate.' - '.$ligne->numero_ligne]['error-avion'] = true;
						}
					}
			
					$tabVol[$formatDate.' - '.$ligne->numero_ligne]['options'] = 'Modifier';
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
					
				$tabVol[$formatDate.' - '.$volCarte['numero_ligne']] = array(
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
					$tabVol[$formatDate.' - '.$volCarte['numero_ligne']]['options'] = 'Planifier';
					$tabVol[$formatDate.' - '.$volCarte['numero_ligne']]['error'] = false;
				}
				else{
					$tabVol[$formatDate.' - '.$volCarte['numero_ligne']]['options'] = 'Modifier';
			
					$testPilote = count($TablePilote->checkPiloteDispo($volCarte->date_depart, $volCarte->numero_ligne, $volCarte->id_pilote));
					$testCoPilote = count($TablePilote->checkPiloteDispo($volCarte->date_depart, $volCarte->numero_ligne, $volCarte->id_copilote));
					$testAvion = count($TableAvion->checkAvionDispo($volCarte->date_depart, $volCarte->numero_ligne, $volCarte->id_avion));
			
					if($testPilote != 0 && $testCoPilote != 0 && $testAvion != 0){
						$tabVol[$formatDate.' - '.$volCarte['numero_ligne']]['error'] = false;
					}
					else{
						if($testPilote == 0){
							$tabVol[$formatDate.' - '.$volCarte['numero_ligne']]['error-pilote'] = true;
						}
						
						if($testCoPilote == 0){
							$tabVol[$formatDate.' - '.$volCarte['numero_ligne']]['error-copilote'] = true;
						}
						
						if($testAvion == 0){
							$tabVol[$formatDate.' - '.$volCarte['numero_ligne']]['error-avion'] = true;
						}
					}
				}
					
			}
		
		}
		
		$listeAeroportVol = array();
		foreach($tabVol as $dateLigne => $vol){
		$explodeDate = explode('-', $dateLigne);
		$date = $explodeDate[0].'-'.$explodeDate[1].'-'.$explodeDate[2];
		$date = str_replace(' ', '', $date);
			if(!array_key_exists($date, $listeAeroportVol)){
				$listeAeroportVol[$date] = array();
			}
			
			if(array_key_exists($vol['aeroport_origine'], $listeAeroportVol[$date])){
				$listeAeroportVol[$date][$vol['aeroport_origine']] += 1;
			}
			else{
				$listeAeroportVol[$date][$vol['aeroport_origine']] = 1;
			}
			
		}
		
		foreach($listeAeroportVol as $date => $aeroport){
			foreach($aeroport as $key => $value){
				if($value >= 5){
					$reqAstreinte = $TableAstreinte->select()->from($TableAstreinte)->where('DATE(date_astreinte) = ?', $date)->where('id_aeroport = ?', $key);
					$volAstreinte = $TableAstreinte->fetchAll($reqAstreinte);
						
					$reqAeroport = $TableAeroport->select()->from($TableAeroport)->where('id_aeroport = ?', $key);
					$aeroportNom = $TableAeroport->fetchRow($reqAeroport);
						
					if(count($volAstreinte) == 0){
						$listeAeroportAstreinte[$date.'-'.$key]['option'] = 'Planifier';
						$listeAeroportAstreinte[$date.'-'.$key]['nom'] = $aeroportNom['nom'];
						$listeAeroportAstreinte[$date.'-'.$key]['date'] = $date;
						$listeAeroportAstreinte[$date.'-'.$key]['nbvol'] = $value;
					}
					else{
						$listeAeroportAstreinte[$date.'-'.$key]['option'] = 'Modifier';
						$listeAeroportAstreinte[$date.'-'.$key]['nom'] = $aeroportNom['nom'];
						$listeAeroportAstreinte[$date.'-'.$key]['date'] = $date;
						$listeAeroportAstreinte[$date.'-'.$key]['nbvol'] = $value;
					}
				}
			}
			
		}
		
		$this->view->assign('tabAeroportAstreinte', $listeAeroportAstreinte);
		$this->view->assign('tabLigne', $tabVol);
		
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
		
		$paginator = Zend_Paginator::factory($tabVol);
		$paginator->setItemCountPerPage(25);
		$paginator->setCurrentPageNumber($page);
		$this->view->paginator = $paginator;
		
		$paginatorAstreinte = Zend_Paginator::factory($listeAeroportAstreinte);
		$paginatorAstreinte->setItemCountPerPage(25);
		$paginatorAstreinte->setCurrentPageNumber($page);
		$this->view->paginatorAst = $paginatorAstreinte;
		

	}
	
	public function ficheAstreinteAction(){
		//Teste si les paramètres sont valides
		if($this->hasParam('idaeroport') && $this->hasParam('date')){
			
			$date = htmlentities($this->getParam('date'), ENT_QUOTES, 'UTF-8');
			$idAeroport = htmlentities($this->getParam('idaeroport'), ENT_QUOTES, 'UTF-8');
			
			$params = array($date => 'date', $idAeroport => 'idaeroport');
				
			if(Aeroport_Fonctions::validParam($params)){

				$date = htmlentities($this->getParam('date'), ENT_QUOTES, 'UTF-8');
				$idAeroport = htmlentities($this->getParam('idaeroport'), ENT_QUOTES, 'UTF-8');
				
				$dateExplode = explode('-', $date);
				$this->view->date = $dateExplode[2].'/'.$dateExplode[1].'/'.$dateExplode[0];
				
				$tableAeroport = new Aeroport();
				$tableAstreinte = new Astreinte();
				$tablePilote = new Pilote();
				
				$infosAeroport = $tableAeroport->find($idAeroport)->current();
				
				if(count($infosAeroport) != 0)
					$this->view->aeroport = $infosAeroport->nom;
				
				$ReqPilote = $tableAstreinte->getReqPiloteAstreinte($date, $idAeroport);
				$Pilotes = $tableAstreinte->fetchAll($ReqPilote);
				
				$piloteArray = array();
				foreach($Pilotes as $pilote){
					$piloteArray[] = $tablePilote->find($pilote['id_pilote'])->current();
				}
				
				$this->view->pilote = $piloteArray;

			}else{
				Aeroport_Fonctions::redirector('/planning');
			}
		}else{
			Aeroport_Fonctions::redirector('/planning');
		}
	}
	
	public function init(){
		$this->view->headLink()->appendStylesheet('/css/calendar.jquery.css');
		$this->view->headScript()->appendFile('/js/calendar.jquery.js');
		$this->view->headScript()->appendFile('/js/PlanningFonction.js');
		$this->view->headLink()->appendStylesheet('/css/PlanningCSS.css');
		
		$this->_redirector = $this->_helper->getHelper('Redirector');
		
		$acl = new Aeroport_LibraryAcl();
		$SRole = new Zend_Session_Namespace('Role');
		if(!$acl->isAllowed($SRole->id_service, $this->getRequest()->getControllerName(), $this->getRequest()->getActionName()))
		{
			$this->_redirector->gotoUrl('/');
		}
		parent::init();
	}
}
