<?php
class Vol extends Zend_Db_Table_Abstract
{
	protected $_name='vol';
	protected $_primary=array('id_vol','numero_ligne');
	protected $_referenceMap=array(
			'Ligne'=>array(
					'columns'=>'numero_ligne',
					'refTableClass'=>'Ligne',
					'refColumns'=>'numero_ligne'),
			'Avion'=>array(
					'columns'=>'id_avion',
					'refTableClass'=>'Avion',
					'refColumns'=>'id_avion'),
			'Pilote'=>array(
					'columns'=>'id_pilote',
					'refTableClass'=>'Pilote',
					'refColumns'=>'id_pilote'),
			'Copilote'=>array(
					'columns'=>'id_copilote',
					'refTableClass'=>'Pilote',
					'refColumns'=>'id_pilote'),
			'id_aeroport_depart_effectif'=>array(
					'columns'=>'id_aeroport_depart_effectif',
					'refTableClass'=>'Aeroport',
					'refColumns'=>'id_aeroport'),
			'id_aeroport_arrivee_effectif'=>array(
					'columns'=>'id_aeroport_arrivee_effectif',
					'refTableClass'=>'Aeroport',
					'refColumns'=>'id_aeroport')
	);

	public function getLastId($ligne){
		$requete=$this->select()->from($this)->where('numero_ligne=?',$ligne)->order("id_vol Desc");
		$row=$this->getAdapter()->fetchOne($requete);
		return $row;
	}
	
	public function getInfosVol($numeroLigne, $dateDepart){
		$reqVol = $this->select()
					->setIntegrityCheck(false)
					->from(array('v' => 'vol'))
					->where('numero_ligne = ?', $numeroLigne)
					->where('date_depart = ?', $dateDepart);
			
		return $this->fetchRow($reqVol);
	}
	
	public function getInfosVolWithAvion($numeroLigne, $dateDepart){
		$reqVol = $this->select()
					->setIntegrityCheck(false)
					->from(array('v' => 'vol'))
					->join(array('avi' => 'avion'), 'v.id_avion = avi.id_avion')
					->join(array('tavi' => 'type_avion'), 'avi.id_type_avion = tavi.id_type_avion')
					->where('numero_ligne = ?', $numeroLigne)
					->where('date_depart = ?', $dateDepart);
			
		return $this->fetchRow($reqVol);
	}
	
	public function getIdAvionNoDispoByVol($numeroLigne, $dateDepart, $update = false){
		$TableLigne = new Ligne;
		
		$infosLigne = $TableLigne->find($numeroLigne)->current();
		
		$heureDepart = $infosLigne->heure_depart;
		$heureArrivee = $infosLigne->heure_arrivee;
		$timestampDepart = strtotime($dateDepart);
		
		$infosVol = $this->getInfosVolWithAvion($numeroLigne, $dateDepart);
		$idAvion = $infosVol['id_avion'];
		
		if($heureArrivee < $heureDepart)
			$dateArrivee = date(‘Y-m-d’, strtotime('+1 days', $timestampDepart));
		else
			$dateArrivee = $dateDepart;
		
		if($update == false){
			$req = $this->select()
						->setIntegrityCheck(false)
						->from(array('v' => 'vol'), 'v.id_avion')
						->where('v.date_depart = ?', $dateDepart)
						->where('UNIX_TIMESTAMP(CONCAT(v.date_arrivee," ",v.heure_arrivee_effective)) BETWEEN UNIX_TIMESTAMP(CONCAT("'.$dateDepart.'"," ","'.$heureDepart.'")) AND UNIX_TIMESTAMP(CONCAT("'.$dateArrivee.'"," ","'.$heureArrivee.'"))');
		}
		else{
			$req = $this->select()
						->setIntegrityCheck(false)
						->from(array('v' => 'vol'), 'v.id_avion')
						->where('v.numero_ligne != ?', $numeroLigne)
						->where('v.date_depart = ?', $dateDepart)
						->where('UNIX_TIMESTAMP(CONCAT(v.date_arrivee," ",v.heure_arrivee_effective)) BETWEEN UNIX_TIMESTAMP(CONCAT("'.$dateDepart.'"," ","'.$heureDepart.'")) AND UNIX_TIMESTAMP(CONCAT("'.$dateArrivee.'"," ","'.$heureArrivee.'"))')
						->orwhere('id_avion = ?', $idAvion);
		}
		
		return($req);
	}
	
	public function getReqIdPiloteNoDispoByVol($numeroLigne, $dateDepart, $update = false){
	
		$TableLigne = new Ligne;
	
		$infosLigne = $TableLigne->find($numeroLigne)->current();
	
		$heureDepart = $infosLigne->heure_depart;
		$heureArrivee = $infosLigne->heure_arrivee;
		$timestampDepart = strtotime($dateDepart);
	
		if($heureArrivee < $heureDepart)
			$dateArrivee = date(‘Y-m-d’, strtotime('+1 days', $timestampDepart));
		else
			$dateArrivee = $dateDepart;
	
		if($update == false){
			$req = $this->select()
						->setIntegrityCheck(false)
						->from(array('v' => 'vol'), array('v.id_pilote'))
						->where('date_depart = ?', $dateDepart)
						->where('UNIX_TIMESTAMP(CONCAT(v.date_arrivee," ",v.heure_arrivee_effective)) BETWEEN UNIX_TIMESTAMP(CONCAT("'.$dateDepart.'"," ","'.$heureDepart.'")) AND UNIX_TIMESTAMP(CONCAT("'.$dateArrivee.'"," ","'.$heureArrivee.'"))');
		}
		else{
			$req = $this->select()
						->setIntegrityCheck(false)
						->from(array('v' => 'vol'), array('v.id_pilote'))
						->where('date_depart = ?', $dateDepart)
						->where('v.numero_ligne != ?', $numeroLigne)
						->where('UNIX_TIMESTAMP(CONCAT(v.date_arrivee," ",v.heure_arrivee_effective)) BETWEEN UNIX_TIMESTAMP(CONCAT("'.$dateDepart.'"," ","'.$heureDepart.'")) AND UNIX_TIMESTAMP(CONCAT("'.$dateArrivee.'"," ","'.$heureArrivee.'"))');
		}
	
		return $req;
	}
	
	public function getReqIdCoPiloteNoDispoByVol($numeroLigne, $dateDepart, $update = false){
	
		$TableLigne = new Ligne;
	
		$infosLigne = $TableLigne->find($numeroLigne)->current();
	
		$heureDepart = $infosLigne->heure_depart;
		$heureArrivee = $infosLigne->heure_arrivee;
		$timestampDepart = strtotime($dateDepart);
	
		if($heureArrivee < $heureDepart)
			$dateArrivee = date(‘Y-m-d’, strtotime('+1 days', $timestampDepart));
		else
			$dateArrivee = $dateDepart;
	
		if($update == false){
			$req = $this->select()
						->setIntegrityCheck(false)
						->from(array('v' => 'vol'), array('v.id_copilote'))
						->where('date_depart = ?', $dateDepart)
						->where('UNIX_TIMESTAMP(CONCAT(v.date_arrivee," ",v.heure_arrivee_effective)) BETWEEN UNIX_TIMESTAMP(CONCAT("'.$dateDepart.'"," ","'.$heureDepart.'")) AND UNIX_TIMESTAMP(CONCAT("'.$dateArrivee.'"," ","'.$heureArrivee.'"))');
		}
		else{
			$req = $this->select()
						->setIntegrityCheck(false)
						->from(array('v' => 'vol'), array('v.id_copilote'))
						->where('date_depart = ?', $dateDepart)
						->where('v.numero_ligne != ?', $numeroLigne)
						->where('UNIX_TIMESTAMP(CONCAT(v.date_arrivee," ",v.heure_arrivee_effective)) BETWEEN UNIX_TIMESTAMP(CONCAT("'.$dateDepart.'"," ","'.$heureDepart.'")) AND UNIX_TIMESTAMP(CONCAT("'.$dateArrivee.'"," ","'.$heureArrivee.'"))');
		}
	
		return $req;
	}
	
	public function getVolByPiloteByDate($idPilote, $timeLun, $timeDim){
		$req = $this->select()
					->setIntegrityCheck(false)
					->from(array('vol' => $this->_name))
					->join(array('lig' => 'ligne'), 'vol.numero_ligne = lig.numero_ligne')
					->where('(id_pilote = '.$idPilote.' OR id_copilote = '.$idPilote.') AND (UNIX_TIMESTAMP(date_arrivee) BETWEEN '.$timeLun.' AND '.$timeDim.' OR UNIX_TIMESTAMP(date_depart) BETWEEN '.$timeLun.' AND '.$timeDim.')');
				
		return $this->fetchAll($req);
	}
	
	public function checkDispoAvion($idAvion, $dateDepart){
		$req = $this->select()
					->from($this)
					->where('id_avion = ?', $idAvion)
					->where('date_depart = ?', $dateDepart);
	
		return $this->fetchAll($req);
	}
	
	public function getVolByDate($date, $orderby){
		$req = $this->select()
					->setIntegrityCheck(false)
					->from(array('vol' => 'vol'))
					->join(array('ligne' => 'ligne'), 'vol.numero_ligne = ligne.numero_ligne', array('heure_depart'))
					->join(array('aerdep' => 'aeroport'), 'vol.id_aeroport_depart_effectif = aerdep.id_aeroport', array('nom as aeroport_depart'))
					->join(array('aerarr' => 'aeroport'), 'vol.id_aeroport_arrivee_effectif = aerarr.id_aeroport', array('nom as aeroport_arrivee'))					
					->where('heure_arrivee_effective IS NOT null')
					->where('date_depart = "'.$date.'" OR date_arrivee = "'.$date.'"')
					->order($orderby);
		
		return $this->fetchAll($req);
	}
	
	public function getAllInfoVol($numeroLigne, $idVol){
		$req = $this->select()
					->setIntegrityCheck(false)
					->from(array('vol' => 'vol'))
					->join(array('ligne' => 'ligne'), 'vol.numero_ligne = ligne.numero_ligne', array('heure_depart as heureDepart', 'heure_arrivee as heureArrivee', 'tarif as tarif', 'distance as distance'))
					->join(array('aerOri' => 'aeroport'), 'ligne.id_aeroport_origine = aerOri.id_aeroport', array('nom as AeroportOrigine'))
					->join(array('villeOri' => 'ville'), 'villeOri.code_ville = aerOri.code_ville', array('nom as VilleOrigine'))
					->join(array('paysOri' => 'pays'), 'paysOri.code_pays = villeOri.code_pays', array('nom as PaysOrigine'))
					->join(array('aerDep' => 'aeroport'), 'ligne.id_aeroport_depart = aerDep.id_aeroport', array('nom as AeroportDepart'))
					->join(array('villeDep' => 'ville'), 'villeDep.code_ville = aerDep.code_ville', array('nom as VilleDepart'))
					->join(array('paysDep' => 'pays'), 'paysDep.code_pays = villeDep.code_pays', array('nom as PaysDepart'))
					->join(array('aerArr' => 'aeroport'), 'ligne.id_aeroport_arrivee = aerArr.id_aeroport', array('nom as AeroportArrivee'))
					->join(array('villeArr' => 'ville'), 'villeArr.code_ville = aerArr.code_ville', array('nom as VilleArrivee'))
					->join(array('paysArr' => 'pays'), 'paysArr.code_pays = villeArr.code_pays', array('nom as PaysArrivee'))
					->join(array('aerDepEff' => 'aeroport'), 'vol.id_aeroport_depart_effectif = aerDepEff.id_aeroport', array('nom as AeroportDepartEffectif'))
					->join(array('aerArrEff' => 'aeroport'), 'vol.id_aeroport_arrivee_effectif = aerArrEff.id_aeroport', array('nom as AeroportArriveeEffectif'))
					->join(array('avion' => 'avion'), 'avion.id_avion = vol.id_avion', null)
					->join(array('type' => 'type_avion'), 'avion.id_type_avion = type.id_type_avion', array('libelle as TypeAvion'))
					->join(array('pilote' => 'pilote'), 'pilote.id_pilote = vol.id_pilote', array('nom as NomPilote', 'prenom as PrenomPilote'))
					->join(array('copilote' => 'pilote'), 'copilote.id_pilote = vol.id_copilote', array('nom as NomCoPilote', 'prenom as PrenomCoPilote'))
					->where('vol.numero_ligne = ?', $numeroLigne)
					->where('id_vol = ?', $idVol);
		
		return $this->fetchRow($req);
	}
	
	public function getInfosEffectives($numeroLigne, $idVol){
		$req = $this->select()
					->setIntegrityCheck(false)
					->from(array('vol' => $this->_name))
					->join(array('aerDep' => 'aeroport'), 'aerDep.id_aeroport = vol.id_aeroport_depart_effectif', array('code_ville as codeVilleDep'))
					->join(array('aerArr' => 'aeroport'), 'aerArr.id_aeroport = vol.id_aeroport_arrivee_effectif', array('code_ville as codeVilleArr'))
					->join(array('villeDep' => 'ville'), 'villeDep.code_ville = aerDep.code_ville', array('code_pays as codePaysDep'))
					->join(array('villeArr' => 'ville'), 'villeArr.code_ville = aerArr.code_ville', array('code_pays as codePaysArr'))
					->where('numero_ligne = ?', $numeroLigne)
					->where('id_vol = ?', $idVol);
		
		return $this->fetchRow($req);
	}
	
	public function getInfosVolById($numeroLigne, $idVol){
		$req = $this->select()
					->from($this)
					->where('numero_ligne = ?', $numeroLigne)
					->where('id_vol = ?', $idVol);
		
		return $this->fetchRow($req);
	}

}