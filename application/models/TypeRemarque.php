<?php
class TypeRemarque extends Zend_Db_Table_Abstract
{
	protected $_name='type_remarque';
	protected $_primary='id_type_remarque';
	
	public function getRemarques($orderBy){
		$req = $this->select()
					->from($this)
					->order($orderBy);
	
		return $this->fetchAll($req);
	}
	
	public function checkLibelleRemarque($libelle){
		$req = $this->select()
					->from($this)
					->where('libelle_type_remarque = ?', $libelle);
	
		return $this->fetchAll($req);
	}
}