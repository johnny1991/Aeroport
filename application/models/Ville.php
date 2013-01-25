<?php
class Ville extends Zend_Db_Table_Abstract
{
	protected $_name='ville';
	protected $_primary='code_ville';
	protected $_referenceMap=array(
			'code_pays'=>array(
					'columns'=>'code_pays',
					'refTableClass'=>'Pays')
	);
	
	public function getVillesByIdPays($idPays){
		$req = $this->select()
					->from($this)
					->where('code_pays = ?', $idPays)
					->order('nom asc');
		
		return $this->fetchAll($req);
	}
	
	public function getVilleWithPaysOrder($orderby){
		$req = $this->select()
					->setIntegrityCheck(false)
					->from(array('ville' => 'ville'))
					->join(array('pays' => 'pays'), 'ville.code_pays = pays.code_pays', array('nom as nomPays'))
					->order($orderby);
		
		return $this->fetchAll($req);
	}
	
	public function getInfosById($codeVille){
		$req = $this->select()
					->from($this)
					->where('code_ville = ?', $codeVille);
	
		return $this->fetchRow($req);
	}
	
	public function getLastId(){
		$req = $this->select()
					->from(array('user' => $this->_name), array('MAX(CONVERT(code_ville, UNSIGNED INTEGER)) as lastId'));
	
		return $this->fetchRow($req);
	}
}