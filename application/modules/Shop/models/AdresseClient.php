<?php
class Shop_Model_AdresseClient extends Zend_Db_Table
{
	protected $_name="AdresseClient";
	protected $_primary=array('id_adresse','id_client');
	protected $_referenceMap=array(
			'Client'=>array(
					'columns'=>'id_client',
					'refTableClass'=>'Shop_Model_Client')
	);

	public function getLastId($id_client){
		$requete=$this->select()->where('id_client=?',$id_client)->order('id_adresse desc');
		$row=$this->getAdapter()->fetchOne($requete);
		return $row;
	}

}