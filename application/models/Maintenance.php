<?php
class Maintenance extends Zend_Db_Table_Abstract
{
	protected $_name='maintenance';
	protected $_primary=array('id_maintenance');
	protected $_referenceMap=array(
			'id_avion'=>array(
					'columns'=>'id_avion',
					'refTableClass'=>'Avion')
	);
	
	public function getReqIdAvionMaintenanceByDate($numeroLigne, $dateDepart, $update = false){
		
		if($update == false){
			$req = $this->select()
						->from(array('main' => $this->_name), array('id_avion'))
						->where('UNIX_TIMESTAMP("'.$dateDepart.'") BETWEEN UNIX_TIMESTAMP(date_prevue) AND UNIX_TIMESTAMP(fin_prevue)');
		}else{
			$tableVol = new Vol();
			$infosVol = $tableVol->getInfosVolWithAvion($numeroLigne, $dateDepart)->toArray();
			
			$idAvion = $infosVol['id_avion'];
			
			$req = $this->select()
						->from(array('main' => $this->_name), array('id_avion'))
						->where('UNIX_TIMESTAMP("'.$dateDepart.'") BETWEEN UNIX_TIMESTAMP(date_prevue) AND UNIX_TIMESTAMP(fin_prevue) OR id_avion = '.$idAvion);
		}
		
		return $req;
	}
}