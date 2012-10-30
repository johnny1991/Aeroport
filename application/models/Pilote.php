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
	
	public function getPiloteByTypeAvion($heureDepart, $dateDepart, $idTypeAvion){
		$TableVol = new Vol();
		$TableBreveter = new EtreBreveter();
		
		$subReqPiloteVol = $TableVol->select()
								->setIntegrityCheck(false)
								->from(array('v' => 'Vol'), array('v.id_pilote'))
								->where('date_arrivee = ?', $dateDepart)
								->where('heure_arrivee_effective > ?', $heureDepart);
		
		$subReqPiloteCoVol = $TableVol->select()
									->setIntegrityCheck(false)
									->from(array('v' => 'Vol'), array('v.id_copilote'))
									->where('date_arrivee = ?', $dateDepart)
									->where('heure_arrivee_effective > ?', $heureDepart);
		
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
	
	public function getPiloteByTypeAvionUpdate($heureDepart, $dateDepart, $idTypeAvion, $numeroLigne){
		$TableVol = new Vol();
		$TableBreveter = new EtreBreveter();
		
		$subReqPiloteVol = $TableVol->select()
									->setIntegrityCheck(false)
									->from(array('v' => 'Vol'), array('v.id_pilote'))
									->where('numero_ligne != ?', $numeroLigne)
									->where('date_arrivee = ?', $dateDepart)
									->where('heure_arrivee_effective > ?', $heureDepart);
		
		
		$subReqPiloteCoVol = $TableVol->select()
									->setIntegrityCheck(false)
									->from(array('v' => 'Vol'), array('v.id_copilote'))
									->where('numero_ligne != ?', $numeroLigne)
									->where('date_arrivee = ?', $dateDepart)
									->where('heure_arrivee_effective > ?', $heureDepart);
		
		
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
}