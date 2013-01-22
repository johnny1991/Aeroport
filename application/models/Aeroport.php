<?php
class Aeroport extends Zend_Db_Table_Abstract
{
	protected $_name='aeroport';
	protected $_primary='id_aeroport';
	protected $_dependentTables = array('Vol','Ligne');
	protected $_referenceMap=array(
			'Ville'=>array(
					'columns'=>'code_ville',
					'refTableClass'=>'Ville')
	);
	
	public function getAeroportWithVilleAndPays($orderby){
		$req = $this->select()
					->setIntegrityCheck(false)
					->from(array('aer' => 'aeroport'))
					->join(array('ville' => 'ville'), 'aer.code_ville = ville.code_ville', array('nom as nomVille'))
					->join(array('pays' => 'pays'), 'pays.code_pays = ville.code_pays', array('nom as nomPays'))
					->order($orderby);
		
		return $this->fetchAll($req);
	}
	
	public function getInfosById($idAeroport){
		$req = $this->select()
					->setIntegrityCheck(false)
					->from(array('aer' => 'aeroport'))
					->join(array('ville' => 'ville'), 'ville.code_ville = aer.code_ville', array('code_pays as pays'))
					->where('id_aeroport = ?', $idAeroport);
	
		return $this->fetchRow($req);
	}
}