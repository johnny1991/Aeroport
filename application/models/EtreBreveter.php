<?php
class EtreBreveter extends Zend_Db_Table_Abstract
{
	protected $_name='etre_breveter';
	protected $_primary=array('id_pilote','id_type_avion');
	protected $_referenceMap=array(
			'id_pilote'=>array(
					'columns'=>'id_pilote',
					'refTableClass'=>'Pilote'),
			'id_type_avion'=>array(
					'columns'=>'id_type_avion',
					'refTableClass'=>'TypeAvion')
	);
	
	public function getReqPiloteBreveter($idTypeAvion, $dateDepart){
		$req = $this->select()
					->setIntegrityCheck(false)
					->from(array('bre' => 'etre_breveter'), array('id_pilote'))
					->where('id_type_avion = ?', $idTypeAvion)
					->where('ADDDATE(DATE(date), INTERVAL 1 YEAR) > ?', $dateDepart);
		
		return $req;
	}
	
	public function getBrevetByIdPilote($idPilote){
		$req = $this->select()
					->from($this)
					->where('id_pilote = ?', $idPilote);
		
		return $this->fetchAll($req);
	}
	
	public function getBrevetByIdPiloteWithTypeAvion($idPilote){
		$req = $this->select()
					->setIntegrityCheck(false)
					->from(array('bre' => $this->_name))
					->join(array('type' => 'type_avion'), 'bre.id_type_avion = type.id_type_avion')
					->where('id_pilote = ?', $idPilote);
		
		return $this->fetchAll($req);
	}
	
	public function getBrevetByPiloteAndType($idPilote, $idTypeAvion){
		$req = $this->select()
					->from($this)
					->where('id_pilote = ?', $idPilote)
					->where('id_type_avion = ?', $idTypeAvion);
		
		return $this->fetchRow($req);
	}

}