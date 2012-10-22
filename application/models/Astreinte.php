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
}