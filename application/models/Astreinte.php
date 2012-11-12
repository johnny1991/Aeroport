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
	
}