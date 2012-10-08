<?php
class Avion extends Zend_Db_Table_Abstract
{
	protected $_name='avion';
	protected $_primary='immatriculation';
	protected $_referenceMap=array(
			'type_avion'=>array(
					'columns'=>'id_type_avion',
					'refTableClass'=>'Type_avion')
	);
}