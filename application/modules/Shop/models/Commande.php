<?php
class Shop_Model_Commande extends Zend_Db_Table
{
	protected $_name='Commande';
	protected $_primary='id_commande';
	protected $_referenceMap=array(
			'Client'=>array(
					'columns'=>'id_client',
					'refTableClass'=>'Client'
			)
	);
	
}