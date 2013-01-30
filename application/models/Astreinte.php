<?php
class Astreinte extends Zend_Db_Table_Abstract
{
	protected $_name='astreinte';
	protected $_primary=array('id_aeroport','id_pilote','date_astreinte');
	protected $_referenceMap=array(
			'id_aeroport'=>array(
					'columns'=>'id_aeroport',
					'refTableClass'=>'Aeroport'),
			'id_pilote'=>array(
					'columns'=>'id_pilote',
					'refTableClass'=>'Pilote')
	);
	
	public function getInfosAstreinte($date, $idAeroport){
		$reqAstreinte = $this->select()
							->from($this)
							->where('DATE(date_astreinte) = ?', $date)
							->where('id_aeroport = ?', $idAeroport);
		
		return $this->fetchAll($reqAstreinte);
	}
	
	public function getIdAeroportByDate($date){
		$req = $this->select()
					->from(array('ast' => $this->_name), array('id_aeroport'))
					->where('DATE(date_astreinte) = ?', $date)
					->order('id_aeroport');
		
		return $this->fetchAll($req);
	}
	
	public function getReqPiloteAstreinte($date, $idAeroport){
		$req = $this->select()
					->setIntegrityCheck(false)
					->from(array('ast' => 'astreinte'), array('id_pilote'))
					->where('DATE(date_astreinte) = ?', $date)
					->where('id_aeroport = ?', $idAeroport);
		
		return $req;
	}
	
	public function getReqIdPiloteByDate($dateDepart){
		$req = $this->select()
					->setIntegrityCheck(false)
					->from(array('ab' => 'astreinte'), array('id_pilote'))
					->where('DATE(date_astreinte) = ?', $dateDepart);
		
		return $req;
	}
	
	public function getAstreintebyDatebyPilote($idPilote, $dateDebut, $dateFin, $order){
		$req = $this->select()
					->setIntegrityCheck(false)
					->from(array('ast' => $this->_name))
					->join(array('aer' => 'aeroport'), 'aer.id_aeroport = ast.id_aeroport', array('nom as nomAeroport'))
					->where('id_pilote = ?', $idPilote)
					->where('UNIX_TIMESTAMP(date_astreinte) BETWEEN '.$dateDebut.' AND '.$dateFin)
					->order($order);
		
		return $this->fetchAll($req);
	}
	
	public function getPilotebyAeroportbyDate($date, $idAeroport, $order){
		$req = $this->select()
					->setIntegrityCheck(false)
					->from(array('ast' => 'astreinte'), array('id_pilote'))
					->join(array('pil' => 'pilote'), 'ast.id_pilote = pil.id_pilote')
					->where('DATE(date_astreinte) = ?', $date)
					->where('id_aeroport = ?', $idAeroport)
					->order($order);
	
		return $this->fetchAll($req);
	}
}