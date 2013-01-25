<?php
class VolController extends Zend_Controller_Action
{
	public function indexAction(){ //OK
		$this->view->title = "Gestion des lignes";
	}

	public function ajouterLigneAction() //OK
	{
		$this->view->title = "Ajouter une ligne";
		$form = new FormulaireLigne();
		$form->setAction($this->getRequest()->getActionName());
		$TableLigne = new Ligne;

		if($this->getRequest()->isPost())
		{
			$data=$this->getRequest()->getPost();
			$dateDepart = null;
			$dateArrivee = null;
			$hDepart = null;
			$hArrivee = null;

			if ((isset($data["periodicite"]))&&(!($data["periodicite"])))
			{
				if($data["dateDepart"]!='')
					$dateDepart = new Zend_Date($data["dateDepart"], 'dd-MM-yy');
				if($data["dateArrivee"]!='')
					$dateArrivee = new Zend_Date($data["dateArrivee"], 'dd-MM-yy');
				if($data["heureDepart"]!='')
					$hDepart = new Zend_Date($data["heureDepart"], 'HH:mm:ss');
				if($data["heureArrivee"]!='')
					$hArrivee = new Zend_Date($data["heureArrivee"], 'HH:mm:ss');
			}

			if(($form->isValid($data))&&(($dateDepart<$dateArrivee)||(($dateDepart==$dateArrivee)&&($hDepart<$hArrivee))||(($data["periodicite"]))))
			{
				$Ligne = $TableLigne->createRow();
				$Ligne->numero_ligne = $form->getValue('Numero');
				$Ligne->id_aeroport_origine = $form->getValue('aeroportOrigine');
				$Ligne->id_aeroport_depart = $form->getValue('aeroportDepart');
				$Ligne->id_aeroport_arrivee = $form->getValue('aeroportArrivee');
				$Ligne->heure_depart = $form->getValue('heureDepart');
				$Ligne->heure_arrivee = $form->getValue('heureArrivee');
				$Ligne->tarif = $form->getValue('tarif');
				$Ligne->distance = $form->getValue('distance');
				$Id = $Ligne->save();
				$message = "<div class='insertion-ok'><label>Insertion réussi</label></div>";
				$this->_helper->FlashMessenger($message);
				if($this->getRequest()->getPost('periodicite'))  // Périodique
				{
					$TablePeriodicite = new Periodicite;
					foreach ($form->getValue("jours") as $jour)
					{
						$Periode = $TablePeriodicite->createRow();
						$Periode->numero_ligne = $Id;
						$Periode->numero_jour = $jour;
						$Periode->save();
					}
					$this->_redirector->gotoUrl('/vol/consulter-vol/ligne/'.$Id);
				}
				else // Vol à la carte
				{
					$TableVol = new Vol;
					$Vol = $TableVol->createRow();
					$Vol->id_vol = $TableVol->getLastId($Id)+1;
					$Vol->numero_ligne = $Id;
					$Vol->id_aeroport_depart_effectif = $form->getValue('aeroportDepart');
					$Vol->id_aeroport_arrivee_effectif = $form->getValue('aeroportArrivee');
					$Vol->date_depart = $dateDepart->get('yyyy-MM-dd');
					$Vol->date_arrivee = $dateArrivee->get('yyyy-MM-dd');
					$Vol->tarif_effectif = $form->getValue('tarif_effectif');
					$Vol->save();
					$this->_redirector->gotoUrl('/vol/fiche-vol/ligne/'.$Vol->numero_ligne.'/vol/'.$Vol->id_vol);
				}
			}
			else
			{
				if ((isset($data["periodicite"]))&&(!($data["periodicite"])))
				{
					if($dateDepart > $dateArrivee)
						$form->getElement("dateArrivee")->addError("La date de départ doit être inférieur à la date d'arrivée");
					else if (($dateDepart == $dateArrivee)&&($hDepart > $hArrivee))
						$form->getElement("heureArrivee")->addError("L'heure de départ doit être inférieur à
								l'heure d'arrivée si la date de départ et la date d'arrivée sont à la même date");

				}
				$form->populate($data);
				$this->remplissageAeroport($form, $data);
				$this->view->Form = $form;
			}
		}
		else
		{
			$form->getElement("Origine")->setValue("250");
			$form->getElement("Depart")->setValue("250");
			$form->getElement("Arrivee")->setValue("250");
			$form->getElement("Numero")->setValue($TableLigne->getLastId()+1);
			$this->view->Form = $form;
		}
	}

	public function modifierLigneAction()  // OK
	{
		$this->view->title = "Modifier une ligne";
		$form = new FormulaireLigne();
		$TableLigne = new Ligne;
		$numero_ligne = $this->_getParam('ligne');
		$ligne = $TableLigne->find($numero_ligne)->current();

		if( ($numero_ligne != NULL) && ($ligne != NULL) )
		{
			if($this->getRequest()->isPost())
			{
				$data = $this->getRequest()->getPost();
				$data['Numero'] = $numero_ligne;
				$dateDepart = null;
				$dateArrivee = null;
				$hDepart = null;
				$hArrivee = null;

				if ((isset($data["periodicite"])) && (!($data["periodicite"])))
				{
					if($data["dateDepart"] != '')
						$dateDepart = new Zend_Date($data["dateDepart"], 'dd-MM-yy');
					if($data["dateArrivee"] != '')
						$dateArrivee = new Zend_Date($data["dateArrivee"], 'dd-MM-yy');
					if($data["heureDepart"] != '')
						$hDepart = new Zend_Date($data["heureDepart"], 'HH:mm:ss');
					if($data["heureArrivee"] != '')
						$hArrivee = new Zend_Date($data["heureArrivee"], 'HH:mm:ss');
				}

				if(($form->isValid($data)) && (($dateDepart<$dateArrivee) || (($dateDepart==$dateArrivee) && ($hDepart<$hArrivee)) || (($data["periodicite"]))))
				{
					$Ligne = $TableLigne->find($form->getValue('Numero'))->current();
					$Ligne->numero_ligne = $numero_ligne;
					$Ligne->id_aeroport_origine = $form->getValue('aeroportOrigine');
					$Ligne->id_aeroport_depart = $form->getValue('aeroportDepart');
					$Ligne->id_aeroport_arrivee = $form->getValue('aeroportArrivee');
					$Ligne->heure_depart = $form->getValue('heureDepart');
					$Ligne->heure_arrivee = $form->getValue('heureArrivee');
					$Ligne->tarif = $form->getValue('tarif');
					$Ligne->distance = $form->getValue('distance');
					$Id = $Ligne->save();
					$message = "<div class='insertion-ok'><label>Modification réussi</label></div>";
					$this->_helper->FlashMessenger($message);
					$TableVol = new Vol;
					$TablePeriodicite = new Periodicite;

					if($this->getRequest()->getPost('periodicite')) // Périodique
					{
						foreach ($form->getValue("jours") as $jour)
						{
							if($TablePeriodicite->find($Id,$jour)->current() == NULL)
								$Periode = $TablePeriodicite->createRow();
							else
								$Periode = $TablePeriodicite->find($Id,$jour)->current();
							$Periode->numero_ligne = $Id;
							$Periode->numero_jour = $jour;
							$Periode->save();
						}
						$Vols = $TableVol->fetchAll($TableVol->select()->where('numero_ligne=?',$Id));
						foreach ($Vols as $Vol)
							$Vol->delete();
					}
					else // Vol à la carte
					{
						if(($TableVol->find($TableVol->getLastId($Id),$Id)->current()) == NULL)
						{
							$Vol = $TableVol->createRow();
							$Vol->id_vol = $TableVol->getLastId($Id)+1;
							$Vol->numero_ligne = $Id;
							$Vol->id_aeroport_depart_effectif = $form->getValue('aeroportDepart');
							$Vol->id_aeroport_arrivee_effectif = $form->getValue('aeroportArrivee');
							$Vol->date_depart = $dateDepart->get('yyyy-MM-dd');
							$Vol->date_arrivee = $dateArrivee->get('yyyy-MM-dd');
							$Vol->tarif_effectif = $form->getValue('tarif_effectif');
							$Vol->save();
						}
						$Periodes = $TablePeriodicite->fetchAll($TablePeriodicite->select()->where('numero_ligne=?',$Id));
						foreach ($Periodes as $Periode)
							$Periode->delete();
					}
					$this->_redirector->gotoUrl('/vol/consulter-vol/ligne/'.$Id);
				}
				else
				{
					if ((isset($data["periodicite"])) && (!($data["periodicite"])))
					{
						if($dateDepart > $dateArrivee)
							$form->getElement("dateArrivee")->addError("La date de départ doit être inférieur à la date d'arrivée");
						else if (($dateDepart == $dateArrivee) && ($hDepart > $hArrivee))
							$form->getElement("heureArrivee")->addError("L'heure de départ doit être inférieur à
									l'heure d'arrivée si la date de départ et la date d'arrivée sont à la même date");

					}
					$form->populate($data);
					$this->remplissageAeroport($form, $data);
					$this->view->form = $form;
					$form->getElement('Numero')->setAttrib("disabled","disabled");
				}
			}
			else
			{
				$donnees = array(
						'Numero' => $ligne->numero_ligne,
						'Origine' => $ligne->findParentRow('Aeroport','aeroport_origine')->findParentRow('Ville')->code_pays,
						'aeroportOrigine' => $ligne->id_aeroport_origine,
						'Depart' => $ligne->findParentRow('Aeroport','aeroport_depart')->findParentRow('Ville')->code_pays,
						'aeroportDepart' => $ligne->id_aeroport_depart,
						'Arrivee' => $ligne->findParentRow('Aeroport','aeroport_arrivee')->findParentRow('Ville')->code_pays,
						'aeroportArrivee' => $ligne->id_aeroport_arrivee,
						'heureDepart' => $ligne->heure_depart,
						'heureArrivee' => $ligne->heure_arrivee,
						'tarif' => number_format($ligne->tarif, 2, ',','')
				);

				if($ligne->findDependentRowset('Periodicite')->count() == 0){
					$dateDepart = new Zend_Date($ligne->findDependentRowset('Vol')->current()->date_depart, 'dd-MM-yy');
					$dateArrivee = new Zend_Date($ligne->findDependentRowset('Vol')->current()->date_arrivee, 'dd-MM-yy');
					$donnees["periodicite"] = 0;
					$donnees["dateDepart"] = $dateDepart->get('dd-MM-yyyy');
					$donnees["dateArrivee"] = $dateArrivee->get('dd-MM-yyyy');
					$donnees["tarif_effectif"] = number_format($ligne->findDependentRowset('Vol')->current()->tarif_effectif, 2, ',','');
				}
				else {
					$Tablejours = $ligne->findDependentRowset('Periodicite')->toArray();
					$jours = array();
					$numero_jour = 0;
					foreach ($Tablejours as $jour)
						$jours[$numero_jour++] = $jour["numero_jour"];
					$donnees["periodicite"] = 1;
					$donnees["jours"] = $jours;
				}
				$form->populate($donnees);
				$tableAeroport = new Aeroport;
				$this->remplissageAeroport($form, $donnees);
				$form->getElement('Numero')->setAttrib("disabled","disabled");
				$this->view->form = $form;
			}
		}
		else
		{
			$insertion = "<div class='no-exist'><label>Cette ligne n'existe pas</label></div>";
			$this->_helper->FlashMessenger($insertion);
			$this->_redirector->gotoUrl($_SERVER["HTTP_REFERER"]);
		}
	}

	public function supprimerLigneAction() //OK
	{
		$this->view->title = "Supprimer une ligne";
		$numero_ligne = $this->_getParam('ligne');
		$TableLigne = new Ligne;
		$Ligne = $TableLigne->find($numero_ligne)->current();
		if( ($numero_ligne!=NULL) && ($Ligne!=NULL) )
		{
			if($Ligne->findDependentRowset('Periodicite')->toArray() != NULL)
				foreach($Ligne->findDependentRowset('Periodicite') as $jour)
				$jour->delete();
			if($Ligne->findDependentRowset('Vol')->toArray() != NULL)
				foreach($Ligne->findDependentRowset('Vol') as $vol)
				$vol->delete();
			$Ligne->delete();
			$message = "<div class='insertion-ok'><label>Suppression réussi</label></div>";
		}
		else
			$message = "<div class='no-exist'><label>Cette ligne n'existe pas</label></div>";
		$this->_helper->FlashMessenger($message);
		$this->_redirector->gotoUrl($_SERVER["HTTP_REFERER"]);

	}

	public function rechercherAdresseAction(){ //OK
		$this->_helper->layout->disableLayout();
		$id_aeroport = $this->_getParam('id_aeroport');
		$TableAeroport = new Aeroport;
		$Aeroport = $TableAeroport->find($id_aeroport)->current();
		$Ville = $Aeroport->findParentRow('Ville');
		echo $Aeroport->adresse.", ".$Ville->nom.", ".$Ville->findParentRow('Pays')->nom;
	}

	public function rechercherAeroportAction()
	{
		$this->_helper->layout->disableLayout();
		$tableAeroport = new Aeroport;
		$requete = $tableAeroport
		->select()
		->setIntegrityCheck(false)
		->from(array('ae'=>'aeroport'),array('ae.nom','ae.code_ville','ae.id_aeroport'))
		->join(array('v'=>'ville'),'v.code_ville = ae.code_ville',array('v.code_pays'))
		->where('code_pays=?',$this->_getParam('pays'));
		$isValid = $this->_getParam('isValid');
		$aeroports = $tableAeroport->fetchAll($requete);
		if(!($isValid))
			echo '<option value="0" disabled="disabled" selected="selected">Choisissez l\'aéroport</option>';
		else
			echo "<option value='0' disabled='disabled'>Choisissez l'aéroport</option>";
		foreach ($aeroports as $aeroport)
			echo '<option value="'.$aeroport->id_aeroport.'">'.$aeroport->nom.'</option>';
	}

	public function consulterLigneAction(){ //OK

		$data = $this->getRequest()->getParams();

		$form = new RechercheLigne();
		$form->Reset->setAttrib('onclick',"javascript:location.href='".$this->view->url()."'");
		$form->populate($data);
		$form->setAction("/vol/consulter-ligne");

		$tableAeroport = new Aeroport;
		$TableLigne = new Ligne;

		if((isset($data["aeroportOrigine"])))
		{
			$AeroportOrigine = $form->getElement('aeroportOrigine');
			$requete = $tableAeroport->select()
			->setIntegrityCheck(false)
			->from(array('ae'=>'aeroport'),array('ae.nom','ae.code_ville','ae.id_aeroport'))
			->join(array('v'=>'ville'),'v.code_ville = ae.code_ville',array('v.code_pays'))
			->where('code_pays=?',$data["Origine"]);
			$aeroports = $tableAeroport->fetchAll($requete);
			$AeroportOrigine->addMultiOption("0","Choisissez l'aéroport");
			$AeroportOrigine->setAttrib("disable",array("0"));
			foreach($aeroports as $aeroport)
				$AeroportOrigine->addMultiOption($aeroport->id_aeroport,$aeroport->nom);
			$AeroportOrigine->setValue($data["aeroportOrigine"]);
		}
		if((isset($data["aeroportDepart"])))
		{
			$AeroportDepart = $form->getElement('aeroportDepart');
			$requete = $tableAeroport->select()
			->setIntegrityCheck(false)
			->from(array('ae'=>'aeroport'),array('ae.nom','ae.code_ville','ae.id_aeroport'))
			->join(array('v'=>'ville'),'v.code_ville = ae.code_ville',array('v.code_pays'))
			->where('code_pays=?',$data["Depart"]);
			$aeroports = $tableAeroport->fetchAll($requete);
			$AeroportDepart->addMultiOption("0","Choisissez l'aéroport");
			$AeroportDepart->setAttrib("disable",array("0"));
			foreach($aeroports as $aeroport)
				$AeroportDepart->addMultiOption($aeroport->id_aeroport,$aeroport->nom);
			$AeroportDepart->setValue($data["aeroportDepart"]);
		}
		if((isset($data["aeroportArrivee"])))
		{
			$AeroportArrivee = $form->getElement('aeroportArrivee');
			$requete = $tableAeroport->select()
			->setIntegrityCheck(false)
			->from(array('ae'=>'aeroport'),array('ae.nom','ae.code_ville','ae.id_aeroport'))
			->join(array('v'=>'ville'),'v.code_ville = ae.code_ville',array('v.code_pays'))
			->where('code_pays=?',$data["Arrivee"]);
			$aeroports = $tableAeroport->fetchAll($requete);
			$AeroportArrivee->addMultiOption("0","Choisissez l'aéroport");
			$AeroportArrivee->setAttrib("disable",array("0"));
			foreach($aeroports as $aeroport)
				$AeroportArrivee->addMultiOption($aeroport->id_aeroport,$aeroport->nom);
			$AeroportArrivee->setValue($data["aeroportArrivee"]);
		}

		$this->view->form = $form;
		$this->view->title = "Consultation des lignes";
		$nbLigne = 25; //Nombre de lignes par pages

		if($this->getRequest()->getParam('page'))
			$page=$this->getRequest()->getParam('page');
		else
			$page = 1;

		if($this->getRequest()->getParam('orderBy'))
			$orderBy = $this->getRequest()->getParam('orderBy');
		else
			$orderBy = "Numero_Asc";

		$requete = $TableLigne
		->select()
		->setIntegrityCheck(false)
		->from(array('l'=>'ligne'))
		->joinLeft(array('vol'=>'vol'),'vol.numero_ligne=l.numero_ligne',array('vol.date_arrivee','vol.date_depart'))
		->join(array('a1'=>'aeroport'),'l.id_aeroport_depart=a1.id_aeroport',array('a1.nom as nom_aeroport_depart','a1.*'))
		->join(array('a2'=>'aeroport'),'l.id_aeroport_arrivee=a2.id_aeroport',array('a2.nom as nom_aeroport_arrivee','a2.*'))
		->join(array('a3'=>'aeroport'),'l.id_aeroport_origine=a3.id_aeroport','a3.*')
		->join(array('v1'=>'ville'),'a1.code_ville=v1.code_ville',array('v1.code_pays','v1.nom'))
		->join(array('v2'=>'ville'),'a2.code_ville=v2.code_ville',array('v2.code_pays','v2.nom'))
		->join(array('v3'=>'ville'),'a3.code_ville=v3.code_ville',array('v3.code_pays','v3.nom'))
		->join(array('p1'=>'pays'),'p1.code_pays=v1.code_pays','p1.nom')
		->join(array('p2'=>'pays'),'p2.code_pays=v2.code_pays','p2.nom')
		->join(array('p3'=>'pays'),'p3.code_pays=v3.code_pays','p3.nom');

		if($this->getRequest()->getParam('Origine')&&(!($this->getRequest()->getParam('aeroportOrigine'))))
			$requete->where("v3.code_pays=?",$this->getRequest()->getParam('Origine'));
		else if($this->getRequest()->getParam('Origine')&&($this->getRequest()->getParam('aeroportOrigine')))
			$requete->where("l.id_aeroport_origine=?",$this->getRequest()->getParam('aeroportOrigine'));

		if($this->getRequest()->getParam('Depart')&&(!($this->getRequest()->getParam('aeroportDepart'))))
			$requete->where("v1.code_pays=?",$this->getRequest()->getParam('Depart'));
		else if($this->getRequest()->getParam('Depart')&&($this->getRequest()->getParam('aeroportDepart')))
			$requete->where("l.id_aeroport_depart=?",$this->getRequest()->getParam('aeroportDepart'));

		if($this->getRequest()->getParam('Arrivee')&&(!($this->getRequest()->getParam('aeroportArrivee'))))
			$requete->where("v2.code_pays=?",$this->getRequest()->getParam('Arrivee'));
		else if($this->getRequest()->getParam('Arrivee')&&($this->getRequest()->getParam('aeroportArrivee')))
			$requete->where("l.id_aeroport_arrivee=?",$this->getRequest()->getParam('aeroportArrivee'));

		if($this->getRequest()->getParam('heureDepartMin')&&(!($this->getRequest()->getParam('heureDepartMax'))))
			$requete->where("l.heure_depart >=?",$this->getRequest()->getParam('heureDepartMin').":00");
		else if($this->getRequest()->getParam('heureDepartMax')&&(!($this->getRequest()->getParam('heureDepartMin'))))
			$requete->where("l.heure_depart <=?",$this->getRequest()->getParam('heureDepartMax').":59");
		else if($this->getRequest()->getParam('heureDepartMin')&&($this->getRequest()->getParam('heureDepartMax')))
			$requete->where("l.heure_depart >=?",$this->getRequest()->getParam('heureDepartMin').":00")
			->where("l.heure_depart =<?",$this->getRequest()->getParam('heureDepartMax').":59");

		if($this->getRequest()->getParam('heureArriveeMin')&&(!($this->getRequest()->getParam('heureArriveeMax'))))
			$requete->where("l.heure_arrivee >=?",$this->getRequest()->getParam('heureArriveeMin').":00");
		else if($this->getRequest()->getParam('heureArriveeMax')&&(!($this->getRequest()->getParam('heureArriveeMin'))))
			$requete->where("l.heure_arrivee <?",$this->getRequest()->getParam('heureArriveeMax').":59");
		else if($this->getRequest()->getParam('heureArriveeMin')&&($this->getRequest()->getParam('heureArriveeMax')))
			$requete->where("l.heure_arrivee >=?",$this->getRequest()->getParam('heureArriveeMin').":00")
			->where("l.heure_arrivee <?",$this->getRequest()->getParam('heureArriveeMax').":59");

		if($this->getRequest()->getParam('tarifMin')&&(!($this->getRequest()->getParam('tarifMax'))))
			$requete->where("l.tarif >=?",$this->getRequest()->getParam('tarifMin'));
		else if($this->getRequest()->getParam('tarifMax')&&(!($this->getRequest()->getParam('tarifMin'))))
			$requete->where("l.tarif <=?",$this->getRequest()->getParam('tarifMax'));
		else if($this->getRequest()->getParam('tarifMin')&&($this->getRequest()->getParam('tarifMax')))
			$requete->where("l.tarif >=?",$this->getRequest()->getParam('tarifMin'))
			->where("l.tarif <=?",$this->getRequest()->getParam('tarifMax'));

		if($this->getRequest()->getParam('dateDepart'))
		{
			$dateDepart = new Zend_Date($this->getRequest()->getParam('dateDepart'), 'dd-MM-yy');
			$requete->where("vol.date_depart=?",$dateDepart->get('yyyy-MM-dd'));
		}
		if($this->getRequest()->getParam('dateArrivee'))
		{
			$dateArrivee = new Zend_Date($this->getRequest()->getParam('dateArrivee'), 'dd-MM-yy');
			$requete->where("vol.date_arrivee=?",$dateArrivee->get('yyyy-MM-dd'));
		}
		if($this->getRequest()->getParam('periodicite'))
		{
			$periodique = $this->getRequest()->getParam('periodicite');
			if($periodique == 1)
				$requete->joinleft(array('p'=>'periodicite'),'p.numero_ligne=l.numero_ligne',array('p.numero_jour'))
				->where('p.numero_jour IS NULL');
			else if($periodique == 2)
				$requete->joinleft(array('p'=>'periodicite'),'p.numero_ligne=l.numero_ligne',array('p.numero_jour'))
				->where('p.numero_jour IS NOT NULL');

		}
		if($this->getRequest()->getParam('mot'))
		{
			$mot = $this->getRequest()->getParam('mot');
			$db = $TableLigne->getAdapter();
			$requete->where('
					(' . $db->quoteInto("l.numero_ligne =?",$mot) . 'OR
					' . $db->quoteInto("p1.nom LIKE ?","%".$mot."%") . 'OR
					' . $db->quoteInto("p2.nom LIKE ?","%".$mot."%") . 'OR
					' . $db->quoteInto("p3.nom LIKE ?","%".$mot."%") . 'OR
					' . $db->quoteInto("v1.nom LIKE ?","%".$mot."%") . 'OR
					' . $db->quoteInto("v2.nom LIKE ?","%".$mot."%") . 'OR
					' . $db->quoteInto("a1.nom LIKE ?","%".$mot."%") . 'OR
					' . $db->quoteInto("a2.nom LIKE ?","%".$mot."%") . ')');
		}

		switch ($orderBy)
		{
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
		}

		$requete->group('l.numero_ligne');
		$lignes = $TableLigne->fetchAll($requete);
		$this->view->order = $orderBy;
		$this->view->Numero = Aeroport_Tableau_OrderColumn::orderColumns($this, "Numero",$orderBy,null,"Numéro");
		$this->view->AeDepart = Aeroport_Tableau_OrderColumn::orderColumns($this, "AeDepart",$orderBy,null,"Aéroport de départ");
		$this->view->AeArrive = Aeroport_Tableau_OrderColumn::orderColumns($this, "AeArrive",$orderBy,null,"Aéroport d'arrivée");
		$this->view->HeDepart = Aeroport_Tableau_OrderColumn::orderColumns($this, "HeDepart",$orderBy,null,"Heure de départ");
		$this->view->HeArrivee = Aeroport_Tableau_OrderColumn::orderColumns($this, "HeArrivee",$orderBy,null,"Heure d'arrivée");

		$paginator = Zend_Paginator::factory($lignes);
		$paginator->setItemCountPerPage($nbLigne);
		$paginator->setCurrentPageNumber($page);
		$this->view->param=$this->getAllParams();
		$this->view->paginator = $paginator;
	}

	public function consulterVolAction(){  //OK

		$this->view->title = "Consultation des vols";
		$nbLigne = 25;

		$numero_ligne = $this->getRequest()->getParam('ligne');
		$TableLigne = new Ligne;
		$ligne = $TableLigne->find($numero_ligne)->current();
		if( ($numero_ligne != NULL) && ($ligne != NULL) )
		{
			$this->view->ligne = $ligne;
			$this->view->aeroport_origine = $ligne->findParentAeroportByaeroport_origine();
			$this->view->aeroport_depart = $ligne->findParentAeroportByaeroport_depart();
			$this->view->aeroport_arrivee = $ligne->findParentAeroportByaeroport_arrivee();
			$this->view->jours = $ligne->findJourSemaineViaPeriodicite();
			$this->view->nbJours = count($ligne->findJourSemaineViaPeriodicite());
			if($this->view->nbJours == 0)
				$this->_redirector->gotoUrl('/vol/fiche-vol/ligne/'.$numero_ligne.'/vol/1');

			$this->view->villeOrigine = $ligne->findParentRow('Aeroport','aeroport_origine')->findParentRow('Ville');
			$this->view->paysOrigine = $this->view->villeOrigine->findParentRow('Pays');
			$this->view->villeDepart = $ligne->findParentRow('Aeroport','aeroport_depart')->findParentRow('Ville');
			$this->view->paysDepart = $this->view->villeDepart->findParentRow('Pays');
			$this->view->villeArrivee = $ligne->findParentRow('Aeroport','aeroport_arrivee')->findParentRow('Ville');
			$this->view->paysArrive = $this->view->villeArrivee->findParentRow('Pays');

			if($this->getRequest()->getParam('orderBy'))
				$orderBy = $this->getRequest()->getParam('orderBy');
			else
				$orderBy = "Id_Asc";

			if($this->getRequest()->getParam('page'))
				$page = $this->getRequest()->getParam('page');
			else
				$page = 1;

			$TableVol = new Vol;
			$requete = $TableVol
			->select()
			->from(array('v'=>'vol'))
			->setIntegrityCheck(false)
			->joinLeft(array('a'=>'avion'),'a.id_avion=v.id_avion')
			->joinLeft(array('ta'=>'type_avion'),'a.id_type_avion=ta.id_type_avion',array('ta.libelle'))
			->joinLeft(array('p'=>'pilote'),'p.id_pilote=v.id_pilote',array('p.nom'))
			->joinLeft(array('c'=>'pilote'),'c.id_pilote=v.id_copilote',array('c.nom as copilote'))
			->where("numero_ligne=?",$numero_ligne);

			switch ($orderBy)
			{
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

			$vols = $TableVol->fetchAll($requete);
			$paginator = Zend_Paginator::factory($vols);
			$paginator->setItemCountPerPage($nbLigne);
			$paginator->setCurrentPageNumber($page);
			$this->view->count = $paginator->getAdapter()->count();
			$this->view->paginator = $paginator;

			$this->view->Id = Aeroport_Tableau_OrderColumn::orderColumns($this, "Id",$orderBy,null,"Numéro du vol");
			$this->view->DaDepart = Aeroport_Tableau_OrderColumn::orderColumns($this, "DeDepart",$orderBy,null,"Date de départ");
			$this->view->DaArrive = Aeroport_Tableau_OrderColumn::orderColumns($this, "DaArrive",$orderBy,null,"Date d'arrivée");
			$this->view->Avion = Aeroport_Tableau_OrderColumn::orderColumns($this, "Avion",$orderBy,null,"Type d'Avion");
			$this->view->Pilote = Aeroport_Tableau_OrderColumn::orderColumns($this, "Pilote",$orderBy,null,"Nom du pilote");
			$this->view->Copilote = Aeroport_Tableau_OrderColumn::orderColumns($this, "Copilote",$orderBy,null,"Nom du copilote");
		}
		else
		{
			$insertion = "<div class='no-exist'><label>Cette ligne n'existe pas</label></div>";
			$this->_helper->FlashMessenger($insertion);
			$this->_redirector->gotoUrl($_SERVER["HTTP_REFERER"]);
		}
	}

	public function ficheVolAction(){ //OK

		$this->view->title = "Fiche du vol";
		$numero_ligne = $this->getRequest()->getParam('ligne');
		//$container = $this->view->navigation()->findOneBy("id","consulterVol");
		//$container->set("params", array( 'ligne' => $numero_ligne));
		//$container->set("title", "Consulter les vols de la ligne ".$numero_ligne);
		$tableLigne = new Ligne;
		$ligne = $tableLigne->find($numero_ligne)->current();
		if( ($numero_ligne!=NULL) && ($ligne!=NULL) )
		{
			$tableVol = new Vol;
			$id_vol = $this->getRequest()->getParam('vol');
			$vol = $tableVol->find($id_vol,$numero_ligne)->current();
			if( ($id_vol!=NULL) && ($vol!=NULL) )
			{
				$this->view->ligne = $ligne;
				$this->view->vol = $vol;
				$this->view->jours = $ligne->findJourSemaineViaPeriodicite();
				$this->view->nbJours = count($ligne->findJourSemaineViaPeriodicite());
				$this->view->aeroport_origine = $ligne->findParentRow('Aeroport','aeroport_origine');
				$this->view->aeroport_depart = $ligne->findParentRow('Aeroport','aeroport_depart');
				$this->view->aeroport_arrivee = $ligne->findParentRow('Aeroport','aeroport_arrivee');
				if ($vol->id_avion!=NULL)
					$this->view->typeAvion = $vol->findParentRow("Avion")->findParentRow("TypeAvion");
				$this->view->aeroport_depart_effectif = $vol->findParentRow('Aeroport','id_aeroport_depart_effectif');
				$this->view->aeroport_arrivee_effectif = $vol->findParentRow('Aeroport','id_aeroport_arrivee_effectif');
				if ($vol->id_pilote != NULL)
					$this->view->pilote=$vol->findParentRow('Pilote','Pilote');
				if ($vol->id_copilote != NULL)
					$this->view->copilote = $vol->findParentRow('Pilote','Copilote');
				$this->view->villeOrigine = $ligne->findParentRow('Aeroport','aeroport_origine')->findParentRow('Ville');
				$this->view->paysOrigine = $this->view->villeOrigine->findParentRow('Pays');
				$this->view->villeDepart = $ligne->findParentRow('Aeroport','aeroport_depart')->findParentRow('Ville');
				$this->view->paysDepart = $this->view->villeDepart->findParentRow('Pays');
				$this->view->villeArrivee = $ligne->findParentRow('Aeroport','aeroport_arrivee')->findParentRow('Ville');
				$this->view->paysArrive = $this->view->villeArrivee->findParentRow('Pays');
			}
			else
			{
				$message = "<div class='no-exist'><label>Ce vol n'existe pas</label></div>";
				$this->_helper->FlashMessenger($message);
				$this->_redirector->gotoUrl('/vol/consulter-vol/ligne/'.$ligne->numero_ligne);
			}
		}
		else
		{
			$message = "<div class='no-exist'><label>Cette ligne n'existe pas</label></div>";
			$this->_helper->FlashMessenger($message);
			$this->_redirector->gotoUrl($_SERVER["HTTP_REFERER"]);
		}
	}

	public function remplissageAeroport($form,$data) //OK
	{
		$tableAeroport = new Aeroport;
		$AeroportOrigine = $form->getElement('aeroportOrigine');
		$form->getElement('PopulateOrigine')->setValue("1");
		$requete = $tableAeroport->select()
		->setIntegrityCheck(false)
		->from(array('ae'=>'aeroport'),array('ae.nom','ae.code_ville','ae.id_aeroport'))
		->join(array('v'=>'ville'),'v.code_ville = ae.code_ville',array('v.code_pays'))
		->where('code_pays=?',$data["Origine"]);
		$aeroports = $tableAeroport->fetchAll($requete);
		$AeroportOrigine->addMultiOption(0,"Choisissez l'aéroport");
		$AeroportOrigine->setAttrib("disable",array("0"));
		foreach($aeroports as $aeroport)
			$AeroportOrigine->addMultiOption($aeroport->id_aeroport,$aeroport->nom);
		if(!(isset($data["aeroportOrigine"])))
			$AeroportOrigine->setValue(0);
		$AeroportDepart = $form->getElement('aeroportDepart');
		$form->getElement('PopulateDepart')->setValue("1");
		$requete = $tableAeroport->select()
		->setIntegrityCheck(false)
		->from(array('ae'=>'aeroport'),array('ae.nom','ae.code_ville','ae.id_aeroport'))
		->join(array('v'=>'ville'),'v.code_ville = ae.code_ville',array('v.code_pays'))
		->where('code_pays=?',$data["Depart"]);
		$aeroports = $tableAeroport->fetchAll($requete);
		$AeroportDepart->addMultiOption("0","Choisissez l'aéroport");
		$AeroportDepart->setAttrib("disable",array("0"));
		foreach($aeroports as $aeroport)
			$AeroportDepart->addMultiOption($aeroport->id_aeroport,$aeroport->nom);
		if(!(isset($data["aeroportDepart"])))
			$AeroportDepart->setValue(0);

		$AeroportArrivee = $form->getElement('aeroportArrivee');
		$form->getElement('PopulateArrivee')->setValue("1");
		$requete = $tableAeroport->select()
		->setIntegrityCheck(false)
		->from(array('ae'=>'aeroport'),array('ae.nom','ae.code_ville','ae.id_aeroport'))
		->join(array('v'=>'ville'),'v.code_ville = ae.code_ville',array('v.code_pays'))
		->where('code_pays=?',$data["Arrivee"]);
		$aeroports = $tableAeroport->fetchAll($requete);
		$AeroportArrivee->addMultiOption("0","Choisissez l'aéroport");
		$AeroportArrivee->setAttrib("disable",array("0"));
		foreach($aeroports as $aeroport)
			$AeroportArrivee->addMultiOption($aeroport->id_aeroport,$aeroport->nom);
		if(!(isset($data["aeroportArrivee"])))
			$AeroportArrivee->setValue(0);
	}

	public function init(){ //OK
		$this->view->messages = $this->_helper->FlashMessenger->getMessages();
		$this->_redirector = $this->_helper->getHelper('Redirector');
		$this->view->headScript()->appendFile('/js/jquery-ui-sliderAccess.js');
		$this->view->headScript()->appendFile('/js/jquery-ui-timepicker-addon.js');
		$this->view->headLink()->appendStylesheet('/css/jquery-ui-timepicker-addon.css');
		$this->view->headLink()->appendStylesheet('/css/jquery-ui-1.8.23.css');
		$this->view->headScript()->appendFile('/js/VolFonction.js');
		$this->view->headScript()->appendFile('http://maps.googleapis.com/maps/api/js?sensor=false&libraries=geometry');

		$acl = new Aeroport_LibraryAcl();
		$SRole = new Zend_Session_Namespace('Role');
		if(!$acl->isAllowed($SRole->id_service, $this->getRequest()->getControllerName(), $this->getRequest()->getActionName()))
		{
			$this->_redirector->gotoUrl('/');
		}
		
		parent::init();
	}
}