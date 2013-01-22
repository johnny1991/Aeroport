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

}