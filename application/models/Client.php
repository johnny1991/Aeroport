<?php
class Client extends Zend_Db_Table_Abstract
{
	protected $_name='client';
	protected $_primary='id_client';
	protected $_referenceMap=array(
			'code_ville'=>array(
					'columns'=>'code_ville',
					'refTableClass'=>'Ville')
	);
}