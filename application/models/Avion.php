<?php
class Avion extends Zend_Db_Table_Abstract
{
	protected $_name='avion';
	protected $_primary='id_avion';
	protected $_referenceMap=array(
			'id_type_avion'=>array(
					'columns'=>'id_type_avion',
					'refTableClass'=>'TypeAvion')
	);
}