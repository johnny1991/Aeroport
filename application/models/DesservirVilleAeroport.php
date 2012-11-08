<?php
class DesservirVilleAeroport extends Zend_Db_Table_Abstract
{
	protected $_name='desservir_ville_aeroport';
	protected $_primary='code_ville,id_aeroport';
	protected $_referenceMap=array(
			'code_ville'=>array(
					'columns'=>'code_ville',
					'refTableClass'=>'Ville'),
			'id_aeroport'=>array(
					'columns'=>'id_aeroport',
					'refTableClass'=>'Aeroport')
	);
}