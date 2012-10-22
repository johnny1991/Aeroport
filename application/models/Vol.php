<?php
class Vol extends Zend_Db_Table_Abstract
{
	protected $_name='vol';
	protected $_primary='numero_vol';
	protected $_referenceMap=array(
			'id_pilote'=>array(
					'columns'=>'id_pilote',  /* A voir selon modif bdd*/
					'refTableClass'=>'Pilote'),
			'id_aeroport_arrive'=>array(
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
			'id_aeroport_arrivee'=>array(
					'columns'=>'id_aeroport',
					'refTableClass'=>'Aeroport')
	);
}