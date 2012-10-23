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
}