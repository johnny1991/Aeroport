<?php
class Shop_Model_CategorieProduit extends Zend_Db_Table
{
	protected $_name="CategorieProduit";
	protected $_primary=array('id_categorie','id_produit');
	protected $_referenceMap=array(
			'Categorie'=>array(
					'columns'=>'id_categorie',
					'refTableClass'=>'Shop_Model_Categorie'),
			'Produit'=>array(
					'columns'=>'id_produit',
					'refTableClass'=>'Shop_Model_Produit')
	);
}