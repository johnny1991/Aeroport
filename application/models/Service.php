<?php
class Service extends Zend_Db_Table_Abstract{
	protected $_name='service';
	protected $_primary='id_service';
	
	public function checkLibelleService($libelle){
		$req = $this->select()
					->from($this)
					->where('libelle_service = ?', $libelle);
		
		return $this->fetchAll($req);
	}
	
	public function getIdByLib($libelle){
		$req = $this->select()
					->from(array('ser' => $this->_name), array('id_service'))
					->where('libelle_service = ?', $libelle);
		
		return $this->fetchRow($req);
	}
	
	public function getServices($orderBy){
		$req = $this->select()
					->from($this)
					->order($orderBy);
		
		return $this->fetchAll($req);
	}
}