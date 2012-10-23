<?php
class Reservation extends Zend_Db_Table_Abstract
{
	protected $_name='reservation';
	protected $_primary=array('id_reservation');
	protected $_referenceMap=array(
			'id_client'=>array(
					'columns'=>'id_client',
					'refTableClass'=>'Client'),
			'id_vol'=>array(
					'columns'=>'id_vol',
					'refTableClass'=>'Vol')
	);
}