<?php
class Pilote extends Zend_Db_Table_Abstract
{
	protected $_name='pilote';
	protected $_primary='id_pilote';
	protected $_dependentTables = array('Vol');
	protected $referenceMap=array(
			'Ville'=>array(
					'columns'=>'code_ville',
					'refTableClass'=>'Ville'));
	
	public function getPiloteByTypeAvion($heureDepart, $heureArrivee, $dateDepart, $idTypeAvion){
		$TableVol = new Vol();
		$TableBreveter = new EtreBreveter();
		$TableAstreinte = new Astreinte;
		
		$subReqPiloteVol = $TableVol->select()
								->setIntegrityCheck(false)
								->from(array('v' => 'vol'), array('v.id_pilote'))
								->where('date_depart = ?', $dateDepart)
								->where('v.heure_arrivee_effective BETWEEN \''.$heureDepart.'\' AND \''.$heureArrivee.'\'');
		
		$subReqPiloteAstreinte = $TableAstreinte->select()
												->setIntegrityCheck(false)
												->from(array('ab' => 'astreinte'), array('id_pilote'))
												->where('date_astreinte = ?', $dateDepart);
		
		$subReqPiloteCoVol = $TableVol->select()
									->setIntegrityCheck(false)
									->from(array('v' => 'vol'), array('v.id_copilote'))
									->where('date_depart = ?', $dateDepart)
									->where('v.heure_arrivee_effective BETWEEN \''.$heureDepart.'\' AND \''.$heureArrivee.'\'');
		
		$subReqPiloteBreveter = $TableBreveter->select()
									->setIntegrityCheck(false)
									->from($TableBreveter, array('id_pilote'))
									->where('id_type_avion = ?', $idTypeAvion)
									->where('ADDDATE(DATE(date), INTERVAL 1 YEAR) > ?', $dateDepart);
		
		$reqPilote = $this->select()
								->setIntegrityCheck(false)
								->from($this)
								->where('disponibilite = 1')
								->where('id_pilote IN ?', $subReqPiloteBreveter)
								->where('id_pilote NOT IN ?', $subReqPiloteVol)
								->where('id_pilote NOT IN ?', $subReqPiloteCoVol)
								->where('id_pilote NOT IN ?', $subReqPiloteAstreinte)
								->order('id_pilote');
		
		$listePilotes = $this->fetchAll($reqPilote);
		
		return $listePilotes;
	}
	
	public function getPiloteByTypeAvionUpdate($heureDepart, $heureArrivee, $dateDepart, $idTypeAvion, $numeroLigne, $idPilote, $idCoPilote){
		$TableVol = new Vol();
		$TableBreveter = new EtreBreveter();
		$TableAstreinte = new Astreinte;
		
		$subReqPiloteVol = $TableVol->select()
									->setIntegrityCheck(false)
									->from(array('v' => 'vol'), array('v.id_pilote'))
									->where('v.numero_ligne != ?', $numeroLigne)
									->where('date_depart = ?', $dateDepart)
									->where('v.heure_arrivee_effective BETWEEN \''.$heureDepart.'\' AND \''.$heureArrivee.'\'');
		
		$subReqPiloteAstreinte = $TableAstreinte->select()
												->setIntegrityCheck(false)
												->from(array('ab' => 'astreinte'), array('id_pilote'))
												->where('date_astreinte = ?', $dateDepart);
		
		$subReqPiloteCoVol = $TableVol->select()
									->setIntegrityCheck(false)
									->from(array('v' => 'vol'), array('v.id_copilote'))
									->where('v.numero_ligne != ?', $numeroLigne)
									->where('date_depart = ?', $dateDepart)
									->where('v.heure_arrivee_effective BETWEEN \''.$heureDepart.'\' AND \''.$heureArrivee.'\'');
		
		$subReqPiloteBreveter = $TableBreveter->select()
											->setIntegrityCheck(false)
											->from($TableBreveter, array('id_pilote'))
											->where('id_type_avion = ?', $idTypeAvion)
											->where('ADDDATE(DATE(date), INTERVAL 1 YEAR) > ?', $dateDepart);
		
		$subReqPiloteVolLigne = $TableVol->select()
										->setIntegrityCheck(false)
										->from(array('v' => 'vol'), array('id_pilote'))
										->where('numero_ligne = ?', $numeroLigne)
										->where('date_depart = ?', $dateDepart);
		
		$subReqCoPiloteVolLigne = $TableVol->select()
										->setIntegrityCheck(false)
										->from(array('v' => 'vol'), array('id_copilote'))
										->where('numero_ligne = ?', $numeroLigne)
										->where('date_depart = ?', $dateDepart);
	
		$reqPilote = $this->select()
						->setIntegrityCheck(false)
						->from($this)
						->where('disponibilite = 1 AND id_pilote IN ('.$subReqPiloteBreveter.') AND id_pilote NOT IN ('.$subReqPiloteAstreinte.') AND ((id_pilote NOT IN ('.$subReqPiloteVol.') AND id_pilote NOT IN ('.$subReqPiloteCoVol.')) OR (id_pilote IN ('.$subReqPiloteVolLigne.') OR id_pilote IN ('.$subReqCoPiloteVolLigne.')))')
						->order('id_pilote');
		
	
		$listePilotes = $this->fetchAll($reqPilote);

		return $listePilotes;
	}
	
	public function getPiloteDispoAstreinte($date, $idAeroport, $update = false){
		$TableAstreinte = new Astreinte;
		
		$subReqAstreinte = $TableAstreinte->select()
										->setIntegrityCheck(false)
										->from($TableAstreinte, array('id_pilote'))
										->where('DATE(date_astreinte) = ?', $date);
		
		if(!$update){
			$req = $this->select()
						->setIntegrityCheck(false)
						->from(array('pil' => 'pilote'))
						->where('id_pilote NOT IN (?)', $subReqAstreinte)
						->where('disponibilite = 1')
						->order('id_pilote ASC');
		}
		else{
			$subReqPilote = $TableAstreinte->getReqPiloteAstreinte($date, $idAeroport);
			
			$req = $this->select()
						->setIntegrityCheck(false)
						->from(array('pil' => 'pilote'))
						->where('disponibilite = 1 AND (id_pilote IN ('.$subReqPilote.') OR id_pilote NOT IN ('.$subReqAstreinte.'))')
						->order('id_pilote ASC');
		}
		
		
		return $this->fetchAll($req);
	}
	
	public function checkPiloteDispo($dateDepart, $numeroLigne, $idPilote){
		$TableVol = new Vol;
		$TableAstreinte = new Astreinte;
		$TableAvion = new Avion;
		$TableBreveter = new EtreBreveter;
		$TableLigne = new Ligne;
		
		$InfosVol = $TableVol->getInfosVol($numeroLigne, $dateDepart);
		$dateArrivee = $InfosVol->date_arrivee;
		$idAeroport = $InfosVol->id_aeroport_depart_effectif;
		$idAvion = $InfosVol->id_avion;
		$heureArrivee = $InfosVol->heure_arrivee_effective;
		
		$Avion = $TableAvion->find($idAvion)->current();
		$idTypeAvion = $Avion->id_type_avion;
		
		$Ligne = $TableLigne->find($numeroLigne)->current();
		$heureDepart = $Ligne->heure_depart;
		
		$subReqAstreinte = $TableAstreinte->getReqPiloteAstreinte($dateDepart, $idAeroport);
		$subReqBreveter = $TableBreveter->getReqPiloteBreveter($idTypeAvion, $dateDepart);
		
		$subReqPilote = $TableVol->select()
								->setIntegrityCheck(false)
								->from(array('v' => 'vol'),  array('id_pilote'))
								->join(array('l' => 'ligne'), 'v.numero_ligne = l.numero_ligne', null)
								->where('date_depart = ?', $dateDepart)
								->where('v.numero_ligne != ?', $numeroLigne)
								->where('UNIX_TIMESTAMP(CONCAT(v.date_depart," ",l.heure_depart)) BETWEEN UNIX_TIMESTAMP(CONCAT("'.$dateDepart.'"," ","'.$heureDepart.'")) AND UNIX_TIMESTAMP(CONCAT("'.$dateArrivee.'"," ","'.$heureArrivee.'")) OR UNIX_TIMESTAMP(CONCAT(v.date_arrivee," ",v.heure_arrivee_effective)) BETWEEN UNIX_TIMESTAMP(CONCAT("'.$dateDepart.'"," ","'.$heureDepart.'")) AND UNIX_TIMESTAMP(CONCAT("'.$dateArrivee.'"," ","'.$heureArrivee.'"))');
		
		$subReqCoPilote = $TableVol->select()
								->setIntegrityCheck(false)
								->from(array('v' => 'vol'), array('id_copilote'))
								->join(array('l' => 'ligne'), 'v.numero_ligne = l.numero_ligne', null)
								->where('date_depart = ?', $dateDepart)
								->where('v.numero_ligne != ?', $numeroLigne)
								->where('UNIX_TIMESTAMP(CONCAT(v.date_depart," ",l.heure_depart)) BETWEEN UNIX_TIMESTAMP(CONCAT("'.$dateDepart.'"," ","'.$heureDepart.'")) AND UNIX_TIMESTAMP(CONCAT("'.$dateArrivee.'"," ","'.$heureArrivee.'")) OR UNIX_TIMESTAMP(CONCAT(v.date_arrivee," ",v.heure_arrivee_effective)) BETWEEN UNIX_TIMESTAMP(CONCAT("'.$dateDepart.'"," ","'.$heureDepart.'")) AND UNIX_TIMESTAMP(CONCAT("'.$dateArrivee.'"," ","'.$heureArrivee.'"))');
		
		$req = $this->select()
					->setIntegrityCheck(false)
					->from(array('pil' => 'pilote'))
					->where('id_pilote = ?', $idPilote)
					->where('disponibilite = 1')
					->where('id_pilote NOT IN (?)', $subReqAstreinte)
					->where('id_pilote IN (?)', $subReqBreveter)
					->where('id_pilote NOT IN (?)', $subReqPilote)
					->where('id_pilote NOT IN (?)', $subReqCoPilote);
		
		return $this->fetchRow($req);
	}
}
