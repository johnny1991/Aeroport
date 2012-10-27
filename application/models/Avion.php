<?php
class Avion extends Zend_Db_Table_Abstract
{
	protected $_name='avion';
	protected $_dependentTables = array('Vol');
	protected $_primary='id_avion';
	protected $_referenceMap=array(
			'Type'=>array(
					'columns'=>'id_type_avion',
					'refTableClass'=>'TypeAvion',
					'refColumns'=>'id_type_avion')
			);
}
