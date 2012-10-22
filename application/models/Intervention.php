<?php
class Intervention extends Zend_Db_Table_Abstract
{
	protected $_name='intervention';
	protected $_primary='id_intervention';
	protected $_referenceMap=array(
			'immatriculation'=>array(
					'columns'=>'immatriculation',
					'refTableClass'=>'Avion')
	);
}