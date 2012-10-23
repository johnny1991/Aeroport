<?php
class Remarque extends Zend_Db_Table_Abstract
{
	protected $_name='remarque';
	protected $_primary='id_remarque';
	protected $referenceMap=array(
			'id_vol'=>array(
					'columns'=>'id_vol',
					'refTableClass'=>'Vol'),
			'id_type_remarque'=>array(
					'columns'=>'id_type_remarque',
					'refTableClass'=>'type_remarque'),
			'id_service'=>array(
					'columns'=>'id_service',
					'refTableClass'=>'Service')
			);
}