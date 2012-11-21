<?php
class Ligne extends Zend_Db_Table_Abstract
{
	protected $_name='ligne';
	protected $_primary='numero_ligne';
	protected $_dependentTables = array('Periodicite');
	protected $_referenceMap=array(
			'aeroport_origine'=>array(
					'columns'=>'id_aeroport_origine',
					'refTableClass'=>'Aeroport',
					'refColumns'=>'id_aeroport'),
			'aeroport_depart'=>array(
					'columns'=>'id_aeroport_depart',
					'refTableClass'=>'Aeroport',
					'refColumns'=>'id_aeroport'),
			'aeroport_arrivee'=>array(
					'columns'=>'id_aeroport_arrivee',
					'refTableClass'=>'Aeroport',
					'refColumns'=>'id_aeroport')
	);
	
	public function getLastId(){
		$requete=$this->select()->from($this)->order("numero_ligne Desc");
		$row=$this->getAdapter()->fetchOne($requete);
		return $row;
	}
	
	public function getInfosAeroportArrivee($numeroLigne){
		$req = $this->select()
					->setIntegrityCheck(false)
					->from(array('li' => 'ligne'))
					->join(array('ae' => 'aeroport'), 'li.id_aeroport_arrivee = ae.id_aeroport')
					->where('li.numero_ligne = ?', $numeroLigne);
		
		return $this->fetchRow($req);
	}
}
