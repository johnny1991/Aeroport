<?php
class Intervention extends Zend_Db_Table_Abstract
{
	protected $_name='intervention';
	protected $_primary='id_intervention';
	protected $_referenceMap=array(
			'id_maintenance'=>array(
					'columns'=>'id_maintenance',
					'refTableClass'=>'Maintenance')
	);
}