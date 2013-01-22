<?php
class Pays extends Zend_Db_Table_Abstract
{
	protected $_name='pays';
	protected $_primary='code_pays';
	
	public function getPaysWithOrder($orderBy){
		$req = $this->select()
					->from($this)
					->order($orderBy);
	
		return $this->fetchAll($req);
	}
	
	public function getInfosById($codePays){
		$req = $this->select()
					->from($this)
					->where('code_pays = ?', $codePays);
		
		return $this->fetchRow($req);
	}
}