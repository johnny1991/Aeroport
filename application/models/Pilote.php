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
	
	public function getPiloteByTypeAvion($numeroLigne, $dateDepart, $idTypeAvion, $update = false){
		$TableVol = new Vol();
		$TableBreveter = new EtreBreveter();
		$TableAstreinte = new Astreinte;
		
		$subReqPiloteAstreinte = $TableAstreinte->getReqIdPiloteByDate($dateDepart);
		$subReqPiloteBreveter = $TableBreveter->getReqPiloteBreveter($idTypeAvion, $dateDepart);
		
		if($update == false){
			$subReqPiloteVol = $TableVol->getReqIdPiloteNoDispoByVol($numeroLigne, $dateDepart);
			$subReqCoPiloteVol = $TableVol->getReqIdCoPiloteNoDispoByVol($numeroLigne, $dateDepart);
			
			$reqPilote = $this->select()
							->setIntegrityCheck(false)
							->from($this)
							->where('disponibilite = 1')
							->where('id_pilote IN ?', $subReqPiloteBreveter)
							->where('id_pilote NOT IN ?', $subReqPiloteVol)
							->where('id_pilote NOT IN ?', $subReqCoPiloteVol)
							->where('id_pilote NOT IN ?', $subReqPiloteAstreinte)
							->order('id_pilote');
		}
		else{
			$subReqPiloteVol = $TableVol->getReqIdPiloteNoDispoByVol($numeroLigne, $dateDepart, true);
			$subReqCoPiloteVol = $TableVol->getReqIdCoPiloteNoDispoByVol($numeroLigne, $dateDepart, true);

			$infosVol = $TableVol->getInfosVol($numeroLigne, $dateDepart);

			$idPilote = $infosVol['id_pilote'];
			$idCoPilote = $infosVol['id_copilote'];
			
			$reqPilote = $this->select()
							->setIntegrityCheck(false)
							->from($this)
							->where('disponibilite = 1 AND id_pilote IN ('.$subReqPiloteBreveter.') AND id_pilote NOT IN ('.$subReqPiloteAstreinte.') AND ((id_pilote NOT IN ('.$subReqPiloteVol.') AND id_pilote NOT IN ('.$subReqCoPiloteVol.')) OR (id_pilote = '.$idPilote.' OR id_pilote = '.$idCoPilote.'))')
							->order('id_pilote');
		}

		return $this->fetchAll($reqPilote);
	}
	
	public function getPiloteDispoAstreinte($date, $idAeroport, $update = false){
		$TableAstreinte = new Astreinte;
		
		$subReqAstreinte = $TableAstreinte->select()
										->setIntegrityCheck(false)
										->from($TableAstreinte, array('id_pilote'))
										->where('DATE(date_astreinte) = ?', $date);
		
		if($update == false){
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
