<?php
class Utilisateur extends Zend_Db_Table_Abstract
{
	protected $_name='utilisateur';
	protected $_primary='login';
	protected $referenceMap=array(
			'code_ville'=>array(
					'columns'=>'code_ville',
					'refTableClass'=>'Ville'),
			'id_service'=>array(
					'columns'=>'id_service',
					'refTableClass'=>'Service')
	);
	
	public function getInfosUserWithService($orderBy){
		$req = $this->select()
					->setIntegrityCheck(false)
					->from(array('user' => $this->_name))
					->join(array('ser' => 'service'), 'user.id_service = ser.id_service')
					->order($orderBy);
		
		return $this->fetchAll($req);
	}
	
	public function getInfosById($idUser){
		$req = $this->select()
					->setIntegrityCheck(false)
					->from(array('user' => $this->_name))
					->join(array('vil' => 'ville'), 'user.code_ville = vil.code_ville', array('code_pays'))
					->where('id_user = ?', $idUser);
		
		return $this->fetchRow($req);
	}
	
	public function getLastId(){
		$req = $this->select()
					->from(array('user' => $this->_name), array('MAX(id_user) as lastId'));
		
		return $this->fetchRow($req);
	}
	
	public function getUserByIdService($idService){
		$req = $this->select()
					->from($this)
					->where('id_service = ?', $idService);
		
		return $this->fetchAll($req);
	}
}