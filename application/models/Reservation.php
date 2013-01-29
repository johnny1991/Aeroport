<?php
class Reservation extends Zend_Db_Table_Abstract
{
	protected $_name='reservation';
	protected $_primary=array('id_reservation');
	protected $_referenceMap=array(
			'id_client'=>array(
					'columns'=>'id_client',
					'refTableClass'=>'Shop_Model_Client'),
			'id_vol'=>array(
					'columns'=>'id_vol',
					'refTableClass'=>'Vol'),
			'numero_ligne'=>array(
					'columns'=>'numero_ligne',
					'refTableClass'=>'Ligne'),
			'id_paiement'=>array(
					'columns'=>'id_paiement',
					'refTableClass'=>'Shop_Model_Paiement'),
			'id_adresse'=>array(
					'columns'=>'id_adresse',
					'refTableClass'=>'Shop_Model_Adresse')
	);
}