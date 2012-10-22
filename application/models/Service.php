<?php
class Service extends Zend_Db_Table_Abstract
{
	protected $_name='service';
	protected $_primary='id_service';
	protected $referenceMap=array(
			'id_remarque'=>array(
					'columns'=>'id_remarque',
					'refTableClass'=>'Remarque')
	);
}