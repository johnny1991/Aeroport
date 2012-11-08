<?php
class Vol extends Zend_Db_Table_Abstract
{
	protected $_name='vol';
	protected $_primary=array('id_vol','numero_ligne');
	protected $_referenceMap=array(
			'Ligne'=>array(
					'columns'=>'numero_ligne',
					'refTableClass'=>'Ligne',
					'refColumns'=>'numero_ligne'),
			'Avion'=>array(
					'columns'=>'id_avion',
					'refTableClass'=>'Avion',
					'refColumns'=>'id_avion'),
			'Pilote'=>array(
					'columns'=>'id_pilote',
					'refTableClass'=>'Pilote',
					'refColumns'=>'id_pilote'),
			'Copilote'=>array(
					'columns'=>'id_copilote',
					'refTableClass'=>'Pilote',
					'refColumns'=>'id_pilote'),
			'id_aeroport_depart_effectif'=>array(
					'columns'=>'id_aeroport_depart_effectif',
					'refTableClass'=>'Aeroport',
					'refColumns'=>'id_aeroport'),
			'id_aeroport_arrivee_effectif'=>array(
					'columns'=>'id_aeroport_arrivee_effectif',
					'refTableClass'=>'Aeroport',
					'refColumns'=>'id_aeroport')
	);

	public function getLastId($ligne){
		$requete=$this->select()->from($this)->where('numero_ligne=?',$ligne)->order("id_vol Desc");
		$row=$this->getAdapter()->fetchOne($requete);
		return $row;
	}
	
	public function getInfosVol($numeroLigne, $dateDepart){
		$reqVol = $this->select()
					->setIntegrityCheck(false)
					->from(array('v' => 'Vol'))
					->where('numero_ligne = ?', $numeroLigne)
					->where('date_depart = ?', $dateDepart);
			
		return $this->fetchRow($reqVol);
	}
	
	public function getInfosVolWithAvion($numeroLigne, $dateDepart){
		$reqVol = $this->select()
					->setIntegrityCheck(false)
					->from(array('v' => 'Vol'))
					->join(array('avi' => 'Avion'), 'v.id_avion = avi.id_avion')
					->join(array('tavi' => 'Type_Avion'), 'avi.id_type_avion = tavi.id_type_avion')
					->where('numero_ligne = ?', $numeroLigne)
					->where('date_depart = ?', $dateDepart);
			
		return $this->fetchRow($reqVol);
	}
	
	public function getIdAvionNoDispo($dateDepart, $heureArrivee, $heureDepart, $numeroLigne = null){
		if($numeroLigne == null){
			$req = $this->select()
						->setIntegrityCheck(false)
						->from(array('v' => 'Vol'), 'v.id_avion')
						->where('v.date_depart = ?', $dateDepart)
						->where('v.heure_arrivee_effective BETWEEN \''.$heureDepart.'\' AND \''.$heureArrivee.'\'');
		}
		else{
			$req = $this->select()
						->setIntegrityCheck(false)
						->from(array('v' => 'Vol'), 'v.id_avion')
						->where('v.numero_ligne != ?', $numeroLigne)
						->where('v.date_depart = ?', $dateDepart)
						->where('v.heure_arrivee_effective BETWEEN \''.$heureDepart.'\' AND \''.$heureArrivee.'\'');
		}
		
		return($req);
	}

}