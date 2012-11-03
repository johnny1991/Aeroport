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
		
		$subReqPiloteVol = $TableVol->select()
								->setIntegrityCheck(false)
								->from(array('v' => 'Vol'), array('v.id_pilote'))
								->where('date_depart = ?', $dateDepart)
								->where('v.heure_arrivee_effective BETWEEN \''.$heureDepart.'\' AND \''.$heureArrivee.'\'');
		
		$subReqPiloteCoVol = $TableVol->select()
									->setIntegrityCheck(false)
									->from(array('v' => 'Vol'), array('v.id_copilote'))
									->where('date_depart = ?', $dateDepart)
									->where('v.heure_arrivee_effective BETWEEN \''.$heureDepart.'\' AND \''.$heureArrivee.'\'');
		
		$subReqPiloteBreveter = $TableBreveter->select()
									->setIntegrityCheck(false)
									->from($TableBreveter, array('id_pilote'))
									->where('id_type_avion = ?', $idTypeAvion)
									->where('ADDDATE(date, INTERVAL 1 YEAR) > ?', date('Y-m-d H:i:s'));
		
		$reqPilote = $this->select()
								->setIntegrityCheck(false)
								->from($this)
								->where('disponibilite = 1')
								->where('id_pilote IN ?', $subReqPiloteBreveter)
								->where('id_pilote NOT IN ?', $subReqPiloteVol)
								->where('id_pilote NOT IN ?', $subReqPiloteCoVol)
								->order('id_pilote');
		
		$listePilotes = $this->fetchAll($reqPilote);
		
		return $listePilotes;
	}
	
	public function getPiloteByTypeAvionUpdate($heureDepart, $heureArrivee, $dateDepart, $idTypeAvion, $numeroLigne, $idPilote, $idCoPilote){
		$TableVol = new Vol();
		$TableBreveter = new EtreBreveter();
		
		$subReqPiloteVol = $TableVol->select()
									->setIntegrityCheck(false)
									->from(array('v' => 'Vol'), array('v.id_pilote'))
									->where('v.numero_ligne != ?', $numeroLigne)
									->where('date_depart = ?', $dateDepart)
									->where('v.heure_arrivee_effective BETWEEN \''.$heureDepart.'\' AND \''.$heureArrivee.'\'');
		
		
		$subReqPiloteCoVol = $TableVol->select()
									->setIntegrityCheck(false)
									->from(array('v' => 'Vol'), array('v.id_copilote'))
									->where('v.numero_ligne != ?', $numeroLigne)
									->where('date_depart = ?', $dateDepart)
									->where('v.heure_arrivee_effective BETWEEN \''.$heureDepart.'\' AND \''.$heureArrivee.'\'');
		
		$subReqPiloteBreveter = $TableBreveter->select()
											->setIntegrityCheck(false)
											->from($TableBreveter, array('id_pilote'))
											->where('id_type_avion = ?', $idTypeAvion)
											->where('ADDDATE(date, INTERVAL 1 YEAR) > ?', date('Y-m-d H:i:s'));
		
		$subReqPiloteVolLigne = $TableVol->select()
										->setIntegrityCheck(false)
										->from(array('v' => 'Vol'), array('id_pilote'))
										->where('numero_ligne = ?', $numeroLigne)
										->where('date_depart = ?', $dateDepart);
		
		$subReqCoPiloteVolLigne = $TableVol->select()
										->setIntegrityCheck(false)
										->from(array('v' => 'Vol'), array('id_copilote'))
										->where('numero_ligne = ?', $numeroLigne)
										->where('date_depart = ?', $dateDepart);
	
		$reqPilote = $this->select()
						->setIntegrityCheck(false)
						->from($this)
						->where('disponibilite = 1 AND id_pilote IN ('.$subReqPiloteBreveter.') AND ((id_pilote NOT IN ('.$subReqPiloteVol.') AND id_pilote NOT IN ('.$subReqPiloteCoVol.')) OR (id_pilote IN ('.$subReqPiloteVolLigne.') OR id_pilote IN ('.$subReqCoPiloteVolLigne.')))')
						->order('id_pilote');
		
	
		$listePilotes = $this->fetchAll($reqPilote);

		return $listePilotes;
	}
}
