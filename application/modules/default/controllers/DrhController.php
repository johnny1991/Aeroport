<?php
class DrhController extends Zend_Controller_Action{
	
	public function init(){
		$this->view->headLink()->appendStylesheet('/css/DrhCSS.css');
		$this->view->headScript()->appendFile('/js/DrhFonction.js');
		$this->view->headScript()->appendFile('/js/jquery-ui-timepicker-addon.js');
		$this->view->headLink()->appendStylesheet('/css/jquery-ui-timepicker-addon.css');
		$this->view->headLink()->appendStylesheet('/css/jquery-ui-1.8.23.css');
		
		$this->_redirector = $this->_helper->getHelper('Redirector');
		
		$acl = new Aeroport_LibraryAcl();
		$SRole = new Zend_Session_Namespace('Role');
		if(!$acl->isAllowed($SRole->id_service, $this->getRequest()->getControllerName(), $this->getRequest()->getActionName()))
		{
			//echo $SRole->id_service . '<br />';
			//echo $this->getRequest()->getControllerName(). '<br />';
			//echo $this->getRequest()->getActionName();
			$this->_redirector->gotoUrl('/');
		}
		
		parent::init();
	}
	
	public function indexAction(){
		$tableUser = new Utilisateur();
		
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
			case 'nom_Asc': $employes = $tableUser->getInfosUserWithService('nom asc')->toArray(); break;
			case 'nom_Desc': $employes = $tableUser->getInfosUserWithService('nom desc')->toArray(); break;
			case 'service_Asc': $employes = $tableUser->getInfosUserWithService('libelle_service asc')->toArray(); break;
			case 'service_Desc': $employes = $tableUser->getInfosUserWithService('libelle_service desc')->toArray(); break;
			case 'date_Asc': $employes = $tableUser->getInfosUserWithService('date asc')->toArray(); break;
			case 'date_Desc': $employes = $tableUser->getInfosUserWithService('date desc')->toArray(); break;
		}

		$this->view->order = $orderBy;
		$this->view->nom = Aeroport_Tableau_OrderColumn::orderColumns($this, 'nom', $orderBy, null, 'Nom Prénom');
		$this->view->service = Aeroport_Tableau_OrderColumn::orderColumns($this, 'service', $orderBy, null, 'Service');
		$this->view->date = Aeroport_Tableau_OrderColumn::orderColumns($this, 'date', $orderBy, 'thDateEmbauche', 'Date d\'embauche');
		
		$paginator = Zend_Paginator::factory($employes);
		$paginator->setItemCountPerPage(25);
		$paginator->setCurrentPageNumber($page);
		$this->view->employes = $paginator;
		$this->view->param = $this->getAllParams();

	}
	
	public function consulterEmployeAction(){
		$idEmploye = htmlentities($this->getParam('id'), ENT_QUOTES, 'UTF-8');
		
		$tableUser = new Utilisateur();
		$tableVille = new Ville();
		$tablePays = new Pays();
		$tablePilote = new Pilote();
		$tableBrevet = new EtreBreveter();
		$tableVol = new Vol();
		
		$infosUser = $tableUser->getInfosById($idEmploye);
		$infosVille = $tableVille->find($infosUser->code_ville)->current();
		$infosPays = $tablePays->find($infosVille->code_pays)->current();
		
		if($infosUser->id_user_pilote != null){
			$infosPilote = $tablePilote->find($infosUser->id_user_pilote)->current();
			$this->view->dispo = $infosPilote->disponibilite;
			
			$infosBrevet = $tableBrevet->getBrevetByIdPiloteWithTypeAvion($infosUser->id_user_pilote);
			$this->view->brevets = $infosBrevet;
			
			$planning = new Aeroport_Planning();
			$heure = 0;
		
			$timestampLundi = $planning->getTimestampFirstMonday();
			$timestampDimanche = $planning->getTimestampLastSunday(1);
			
			$lesvols = $tableVol->getVolByPiloteByDate($infosUser->id_user_pilote, $timestampLundi, $timestampDimanche);
			foreach($lesvols as $vol){
				$timestampDepart = strtotime($vol->date_depart.' '.$vol->heure_depart);
				$timestampArrivee = strtotime($vol->date_arrivee.' '.$vol->heure_arrivee_effective);
				
				$heure += $timestampArrivee - $timestampDepart;
			}
			$temps = number_format(($heure / 60) / 60, 2);
			$this->view->temps = Aeroport_Fonctions::getTemps($temps);
		}
		
		$this->view->nom = $infosUser->nom;
		$this->view->prenom = $infosUser->prenom;
		$this->view->adresse = $infosUser->adresse;
		$this->view->telephone = $infosUser->telephone;
		$this->view->email = $infosUser->email;
		$this->view->nomVille = $infosVille->nom;
		$this->view->nomPays = $infosPays->nom;
		$this->view->date = $infosUser->date;
		$this->view->service = $infosUser->id_service;
		$this->view->idPilote = $infosUser->id_user_pilote;
	}
	
	public function changeDisponibiliteAction(){
		$this->_helper->layout->disableLayout();
		
		$value = htmlentities($this->getParam('dispo'), ENT_QUOTES, 'UTF-8');
		$idPilote = htmlentities($this->getparam('id'), ENT_QUOTES, 'UTF-8');
		
		$tablePilote = new Pilote();
		
		$thePilote = $tablePilote->find($idPilote)->current();
		$thePilote->disponibilite = $value;
		$thePilote->save();
	}
	
	public function prolongeBrevetAction(){
		$this->_helper->layout->disableLayout();
		
		$idPilote = htmlentities($this->getParam('idPilote'), ENT_QUOTES, 'UTf-8');
		$idTypeAvion = htmlentities($this->getParam('idTypeAvion'), ENT_QUOTES, 'UTF-8');
		$date = htmlentities($this->getParam('date'), ENT_QUOTES, 'UTF-8');
		
		$timestamp = strtotime($date);
		$newDate = mktime(0,0,0, date('m', $timestamp), date('d', $timestamp), date('Y', $timestamp) + 1);
		
		$tableBrevet = new EtreBreveter();
		$brevet = $tableBrevet->getBrevetByPiloteAndType($idPilote, $idTypeAvion);
		$brevet->date = date('Y-m-d H:i:s', $newDate);
		$brevet->save();
	}
	
	public function consulterServiceAction(){

		$tableService = new Service();
		
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
			case 'libelle_Asc': $services = $tableService->getServices('libelle_service asc')->toArray(); break;
			case 'libelle_Desc': $services = $tableService->getServices('libelle_service desc')->toArray(); break;
		}
		
		$this->view->order = $orderBy;
		$this->view->page = $page;
		$this->view->libelle = Aeroport_Tableau_OrderColumn::orderColumns($this, 'libelle', $orderBy, null, 'Libelle');
		
		$paginator = Zend_Paginator::factory($services);
		$paginator->setItemCountPerPage(25);
		$paginator->setCurrentPageNumber($page);
		$this->view->services = $paginator;
		$this->view->param = $this->getAllParams();
		
	}
	
	public function ajouterEmployeAction(){
		$formAjouterEmploye = new AjouterEmploye();
		
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
		
			if($formAjouterEmploye->isValid($data)){
				$nom = htmlentities($formAjouterEmploye->getValue('nom'), ENT_QUOTES, 'UTF-8');
				$prenom = htmlentities($formAjouterEmploye->getValue('prenom'), ENT_QUOTES, 'UTF-8');
				$mail = htmlentities($formAjouterEmploye->getValue('mail'), ENT_QUOTES, 'UTF-8');
				$adresse = htmlentities($formAjouterEmploye->getValue('adresse'), ENT_QUOTES, 'UTF-8');
				$ville = htmlentities($formAjouterEmploye->getValue('ville'), ENT_QUOTES, 'UTF-8');
				$telephone = htmlentities($formAjouterEmploye->getValue('telephone'), ENT_QUOTES, 'UTF-8');
				$service = htmlentities($formAjouterEmploye->getValue('service'), ENT_QUOTES, 'UTF-8');
				
				$login = $prenom;
				$password = md5(Aeroport_Fonctions::filter($prenom));
				$date = date('Y-m-d H-i-s'); 
				
				$tableUser = new Utilisateur();
				$tablePilote = new Pilote();
				$tableBreveter = new EtreBreveter();
				
				$idUser = $tableUser->getLastId()->lastId + 1;
			
				if($service == 8){
					$newPilote = $tablePilote->createRow();
					$newPilote->nom = $nom;
					$newPilote->prenom = $prenom;
					$newPilote->email = $mail;
					$newPilote->password = $password;
					$newPilote->code_ville = $ville;
					$newPilote->adresse = $adresse;
					$newPilote->telephone = $telephone;
					$newPilote->disponibilite = 1;
					$idPilote = $newPilote->save();
					
					$brevets = array();
					foreach($_POST as $key => $value){
						if($value != '0' && $value != null){
							if(strstr($key, 'brevet')){
								$brevets[$key] = $value;
							}
						}
					}
					
					$nbBrevets = (count($brevets) / 2);
					for($i=1;$i<=$nbBrevets;$i++){
						$newBrevet = $tableBreveter->createRow();
						$newBrevet->id_pilote = $idPilote;
						$newBrevet->id_type_avion = $brevets['brevet'.$i];
						$newBrevet->date = date('Y-m-d' , strtotime($brevets['datebrevet'.$i]));
						$newBrevet->save();
					}
					
					$newUser = $tableUser->createRow();
					$newUser->login = $login;
					$newUser->id_service = $service;
					$newUser->nom = $nom;
					$newUser->prenom = $prenom;
					$newUser->email = $mail;
					$newUser->password = $password;
					$newUser->code_ville = $ville;
					$newUser->adresse = $adresse;
					$newUser->telephone = $telephone;
					$newUser->date = $date;
					$newUser->id_user_pilote = $idPilote;
					$newUser->id_user = $idUser;
					$newUser->save();
				}else{
					
					$newUser = $tableUser->createRow();
					$newUser->login = $login;
					$newUser->id_service = $service;
					$newUser->nom = $nom;
					$newUser->prenom = $prenom;
					$newUser->email = $mail;
					$newUser->password = $password;
					$newUser->code_ville = $ville;
					$newUser->adresse = $adresse;
					$newUser->telephone = $telephone;
					$newUser->date = $date;
					$newUser->id_user = $idUser;
					$newUser->save();
				}
		
				Aeroport_Fonctions::redirector('/drh/');
				
			}else{
				$this->view->form = $formAjouterEmploye;
			}
		}else{
			$this->view->form = $formAjouterEmploye;
		}
	}
	
	public function modifierEmployeAction(){
		$formModifierEmploye = new AjouterEmploye();
		
		$tableUser = new Utilisateur();
		
		$idUser = htmlentities($this->getParam('id'), ENT_QUOTES, 'UTF-8');
		$infosUser = $tableUser->getInfosById($idUser);
		$ident = $infosUser->login;
		
		$this->view->nom = $infosUser->nom;
		$this->view->prenom = $infosUser->prenom;
		$this->view->ville = $infosUser->code_ville;
		$this->view->id = $infosUser->id_user;
		
		$idPilote = ($infosUser->id_user_pilote != null) ? $infosUser->id_user_pilote : null;
		
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
		
			if($formModifierEmploye->isValid($data)){
				$nom = $formModifierEmploye->getValue('nom');
				$prenom = $formModifierEmploye->getValue('prenom');
				$mail = htmlentities($formModifierEmploye->getValue('mail'), ENT_QUOTES, 'UTF-8');
				$adresse = $formModifierEmploye->getValue('adresse');
				$ville = htmlentities($formModifierEmploye->getValue('ville'), ENT_QUOTES, 'UTF-8');
				$telephone = htmlentities($formModifierEmploye->getValue('telephone'), ENT_QUOTES, 'UTF-8');
				$service = htmlentities($formModifierEmploye->getValue('service'), ENT_QUOTES, 'UTF-8');
				
				$login = $mail;
				$password = $prenom;
				$date = date('Y-m-d H-i-s');
		
				$tablePilote = new Pilote();
				$tableBreveter = new EtreBreveter();
			
				if($service == 8){
					
					if($idPilote == null){
						$newPilote = $tablePilote->createRow();
						$newPilote->nom = $nom;
						$newPilote->prenom = $prenom;
						$newPilote->email = $mail;
						$newPilote->code_ville = $ville;
						$newPilote->adresse = $adresse;
						$newPilote->telephone = $telephone;
						$idPilote = $newPilote->save();
							
						$brevets = array();
						foreach($_POST as $key => $value){
							if($value != '0' && $value != null){
								if(strstr($key, 'brevet')){
									$brevets[$key] = $value;
								}
							}
						}
							
						$nbBrevets = (count($brevets) / 2);
						for($i=1;$i<=$nbBrevets;$i++){
							$newBrevet = $tableBreveter->createRow();
							$newBrevet->id_pilote = $idPilote;
							$newBrevet->id_type_avion = $brevets['brevet'.$i];
							$newBrevet->date = date('Y-m-d' , strtotime($brevets['datebrevet'.$i]));
							$newBrevet->save();
						}
						
					}else{
						$newPilote = $tablePilote->find($idPilote)->current();
						$newPilote->nom = $nom;
						$newPilote->prenom = $prenom;
						$newPilote->email = $mail;
						$newPilote->code_ville = $ville;
						$newPilote->adresse = $adresse;
						$newPilote->telephone = $telephone;
						$newPilote->save();
						
						$tableBreveter->delete('id_pilote = '.$idPilote);
							
						$brevets = array();
						foreach($_POST as $key => $value){
							if($value != '0' && $value != null){
								if(strstr($key, 'brevet')){
									$brevets[$key] = $value;
								}
							}
						}
							
						$nbBrevets = (count($brevets) / 2);
						for($i=1;$i<=$nbBrevets;$i++){
							$newBrevet = $tableBreveter->createRow();
							$newBrevet->id_pilote = $idPilote;
							$newBrevet->id_type_avion = $brevets['brevet'.$i];
							$newBrevet->date = date('Y-m-d' , strtotime($brevets['datebrevet'.$i]));
							$newBrevet->save();
						}
					}
					
					$newUser = $tableUser->find($ident)->current();
					$newUser->id_service = $service;
					$newUser->nom = $nom;
					$newUser->prenom = $prenom;
					$newUser->email = $mail;
					$newUser->code_ville = $ville;
					$newUser->adresse = $adresse;
					$newUser->telephone = $telephone;
					$newUser->id_user_pilote = $idPilote;
					$newUser->save();
					
				}else{
					
					if($idPilote != null){
						$tableBreveter->delete('id_pilote = '.$idPilote);
						
						$newPilote = $tablePilote->find($idPilote)->current();
						$newPilote->disponibilite = 0;
						$newPilote->save();
					}
					
					$newUser = $tableUser->find($ident)->current();
					$newUser->id_service = $service;
					$newUser->nom = $nom;
					$newUser->prenom = $prenom;
					$newUser->email = $mail;
					$newUser->code_ville = $ville;
					$newUser->adresse = $adresse;
					$newUser->telephone = $telephone;
					$newUser->save();
				}
		
				Aeroport_Fonctions::redirector('/drh/');
		
			}else{
				$this->view->form = $formModifierEmploye;
			}
		}else{
			$this->view->form = $formModifierEmploye;
		}
	}
	
	public function supprimerEmployeAction(){
		$this->_helper->layout->disableLayout();
		
		$idUser = intval(htmlentities($this->getParam('id'), ENT_QUOTES, 'UTF-8'));
		
		$tableUser = new Utilisateur();
		$tablePilote = new Pilote();
		$tableBreveter = new EtreBreveter();

		$infos = $tableUser->getInfosById($idUser);

		if($infos->id_user_pilote != null){
			$tableBreveter->delete('id_pilote = '.$infos->id_user_pilote);
			$newPilote = $tablePilote->find($infos->id_user_pilote)->current();
			$newPilote->disponibilite = 0;
			$newPilote->save();
		}
		
		$tableUser->delete('id_user = '.$idUser);
	}
	
	public function changeVilleAction(){
		$this->_helper->layout->disableLayout();
		
		$idPays = htmlentities($this->getParam('idPays'), ENT_QUOTES, 'UTF-8');
		$idVille = htmlentities($this->getParam('idVille'), ENT_QUOTES, 'UTF-8');
		
		$tableVille = new Ville();
		$villes = $tableVille->getVillesByIdPays($idPays);
		foreach($villes as $ville){
			if($idVille == $ville->code_ville){
				echo '<option selected="selected" value="'.$ville->code_ville.'" label="'.$ville->nom.'">'.$ville->nom.'</option>';
			}else{
				echo '<option value="'.$ville->code_ville.'" label="'.$ville->nom.'">'.$ville->nom.'</option>';
			}
		}
	}
	
	public function getBrevetAction(){
		$this->_helper->layout->disableLayout();
		
		$tableBrevet = new EtreBreveter();
		$tableTypeAvion = new TypeAvion();
		$tableUser = new Utilisateur();
		
		$typeAvions = $tableTypeAvion->fetchAll()->toArray();
		$idEmploye = htmlentities($this->getParam('id'), ENT_QUOTES, 'UTF-8');
		
		$idPilote = $tableUser->getInfosById($idEmploye)->id_user_pilote;
		
		$idPilote = ($idPilote != null) ? $idPilote : false;
		
		$index = 0;
		$html = '';
		
		if($idPilote != false){
			$brevets = $tableBrevet->getBrevetByIdPilote($idPilote);
			foreach($brevets as $brevet){
				$index++;
				$html = '<div style="margin-bottom:10px;margin-top:10px;" id="brevet-'.$index.'" class="divBrevet">
				<div style="float:left;margin-right:10px;" onclick="removeBrevet('.$index.')" class="icone_delete_ligne"></div>
				<dt><label>Le brevet n° '.$index.'</label></dt>
				<dd><select name="brevet'.$index.'" onchange="addBrevet('.$index.')" class="brevetSelect"><option value="0">Brevet '.$index.'</option>';
				
				foreach($typeAvions as $types){
					$selected = ($types['id_type_avion'] == $brevet->id_type_avion) ? 'selected="selected"' : '';
					$html .= '<option '.$selected.' value="'.$types['id_type_avion'].'">'.$types['libelle'].'</option>';
				}
				$explode = explode(' ', $brevet->date);
				$html .= '</select></dd><dd><input type="text" name="datebrevet'.$index.'" value="'.$explode[0].'"/></dd></div>';
				
				echo $html;
			}
		}
		
	}
	
	public function ajouterServiceAction(){

		$formAjouterService = new AjouterService();
		
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
		
			if($formAjouterService->isValid($data)){
		
				$libelle = htmlentities($formAjouterService->getValue('service'), ENT_QUOTES, 'UTF-8');
		
				$tableService = new Service();
				$newService = $tableService->createRow();
				$newService->libelle_service = $libelle;
				$newService->save();
				
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
		
				Aeroport_Fonctions::redirector('/drh/consulter-service/'.$page);
		
			}else{
				$this->view->form = $formAjouterService;
				foreach ($formAjouterService->getMessages('service') as $value){
					$this->view->error = $value;
				}
			}
		}else{
			$this->view->form = $formAjouterService;
		}
	}
	
	public function modifierServiceAction(){
		$this->_helper->layout()->disableLayout();
		
		$libelleService = $this->getParam('lib');
		$idService = $this->getParam('id');
		
		$tableService = new Service();
		
		$checkLib = $tableService->checkLibelleService($libelleService);
		if(count($checkLib) == 0){
			$updateService = $tableService->find($idService)->current();
			$updateService->libelle_service = $libelleService;
			$updateService->save();
			
			if($this->getParam('orderBy'))
				echo 'page/'.$this->getParam('page').'/orderBy/'.$this->getParam('orderBy');
			else
				echo 'page/'.$this->getParam('page').'/';
		}else{
			echo 'errors';
		}
	}
	
	public function supprimerServiceAction(){
		$this->_helper->layout()->disableLayout();
		
		$idService = htmlentities($this->getParam('id'), ENT_QUOTES, 'UTF-8');
		
		$tableService = new Service();
		$tableUser = new Utilisateur();
		
		$users = $tableUser->getUserByIdService($idService);
		foreach($users as $user){
			$user->id_service = 9;
			$user->save();
		}
		
		$tableService->delete('id_service = '.$idService);
	}
	
	public function getTypeavionAction(){
		$tableTypeavion = new TypeAvion();
		
		$typeavions = $tableTypeavion->fetchAll(null, 'libelle')->toArray();
		foreach($typeavions as $types){
			echo '<option value="'.$types['id_type_avion'].'">'.$types['libelle'].'</option>';
		}
	}
	
}
