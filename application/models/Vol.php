<?php
class Vol extends Zend_Db_Table_Abstract
{
	protected $_name='vol';
	protected $_primary=array('id_vol','numero_ligne');
	protected $_referenceMap=array(
			'id_pilote'=>array(
					'columns'=>'id_pilote',  /* A voir selon modif bdd*/
					'refTableClass'=>'Pilote'),
			'id_aeroport_arrivee_effectif'=>array(
					'columns'=>'id_aeroport',
					'refTableClass'=>'Aeroport'),
			'id_avion'=>array(
					'columns'=>'id_avion',
					'refTableClass'=>'Avion'),
			'numero_ligne'=>array(
					'columns'=>'numero_ligne',
					'refTableClass'=>'Ligne'),
			'id_copilote'=>array(
					'columns'=>'id_pilote',  /* A voir selon modif bdd*/
					'refTableClass'=>'Pilote'),
			'id_aeroport_depart_effectif'=>array(
					'columns'=>'id_aeroport',
					'refTableClass'=>'Aeroport')
	);

	public function getLastId($ligne){
		$requete=$this->select()->from($this)->where('numero_ligne=?',$ligne);
		$row=$this->getAdapter()->fetchOne($requete);
		return $row;
	}
	
}