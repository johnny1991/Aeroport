<?php
class Vol extends Zend_Db_Table_Abstract
{
	protected $_name='vol';
	protected $_primary='numero_vol';
	protected $_referenceMap=array(
			'pilote'=>array(
					'columns'=>'id_copilote',  /* A voir selon modif bdd*/
					'refTableClass'=>'Pilote'),
			'aeroport'=>array(
					'columns'=>'id_aeroport',
					'refTableClass'=>'Aeroport'),
			'aeroport'=>array(
					'columns'=>'id_aeroport_arriver',
					'refTableClass'=>'Aeroport'),
			'avion'=>array(
					'columns'=>'immatriculation',
					'refTableClass'=>'Avion'),
			'periodicite'=>array(
					'columns'=>'id_periodicite',
					'refTableClass'=>'Periodicite'),
			'pilote'=>array(
					'columns'=>'id_pilote',  /* A voir selon modif bdd*/
					'refTableClass'=>'Pilote'),
			'aeroport'=>array(
					'columns'=>'id_aeroport_partir',
					'refTableClass'=>'Aeroport')
	);
}