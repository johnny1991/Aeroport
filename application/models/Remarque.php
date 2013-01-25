<?php
class Remarque extends Zend_Db_Table_Abstract
{
	protected $_name='remarque';
	protected $_primary='id_remarque';
	protected $referenceMap=array(
			'id_vol'=>array(
					'columns'=>'id_vol',
					'refTableClass'=>'Vol'),
			'id_type_remarque'=>array(
					'columns'=>'id_type_remarque',
					'refTableClass'=>'type_remarque'),
			'id_service'=>array(
					'columns'=>'id_service',
					'refTableClass'=>'Service')
			);
	
	public function getRemarqueByIdType($idTypeRemarque){
		$req = $this->select()
					->from($this)
					->where('id_type_remarque = ?', $idTypeRemarque);
		
		return $this->fetchAll($req);
	}
	
	public function getRemarqueByIdVolAndLigne($numeroLigne, $idVol){
		$req = $this->select()
					->setIntegrityCheck(false)
					->from(array('req' => 'remarque'))
					->join(array('typeReq' => 'type_remarque'), 'req.id_type_remarque = typeReq.id_type_remarque')
					->where('numero_ligne = ?', $numeroLigne)
					->where('id_vol = ?', $idVol);
		
		return $this->fetchAll($req);
	}
	
	public function getRemarqueByTypeByVol($idType, $numeroLigne, $idVol){
		$req = $this->select()
					->from($this)
					->where('numero_ligne = ?', $numeroLigne)
					->where('id_vol = ?', $idVol)
					->where('id_type_remarque = ?', $idType);
		
		return $this->fetchAll($req);
	}
	
	public function getRemarqueByIdByVol($idRemarque, $numeroLigne, $idVol){
		$req = $this->select()
		->from($this)
		->where('numero_ligne = ?', $numeroLigne)
		->where('id_vol = ?', $idVol)
		->where('id_remarque = ?', $idRemarque);
	
		return $this->fetchAll($req);
	}
}