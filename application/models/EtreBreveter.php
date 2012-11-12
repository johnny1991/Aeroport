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

}