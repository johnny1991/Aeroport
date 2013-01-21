<?php
class Shop_Model_SousCategorieProduit extends Zend_Db_Table
{
	protected $_name="SousCategorieProduit";
	protected $_primary=array('id_souscategorie','id_produit');
	protected $_referenceMap=array(
			'SousCategorie'=>array(
					'columns'=>'id_souscategorie',
					'refTableClass'=>'Shop_Model_SousCategorie'),
			'Produit'=>array(
					'columns'=>'id_produit',
					'refTableClass'=>'Shop_Model_Produit')
			);
}