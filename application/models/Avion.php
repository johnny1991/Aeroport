<?php
class Avion extends Zend_Db_Table_Abstract
{
	protected $_name='avion';
	protected $_dependentTables = array('Vol');
	protected $_primary='id_avion';
	protected $_referenceMap=array(
			'Type'=>array(
					'columns'=>'id_type_avion',
					'refTableClass'=>'TypeAvion',
					'refColumns'=>'id_type_avion')
			);
	
	public function getAvionDispoByTypeByLigne($infosVol, $actions){
		$TableVol = new Vol();
		
		$subReqAvion = ($actions == 'Planifier') ? $TableVol->getIdAvionNoDispo($infosVol['dateDepart'], $infosVol['heureArrivee'], $infosVol['heureDepart']) : $TableVol->getIdAvionNoDispo($infosVol['dateDepart'], $infosVol['heureArrivee'], $infosVol['heureDepart'], $infosVol['numeroLigne']); 
		
		if($actions == 'Planifier'){
			$reqAvion = $this->select()
							->setIntegrityCheck(false)
							->from(array('avi' => 'avion'), array('avi.id_avion'))
							->joinLeft(array('tyav' => 'Type_Avion'), 'avi.id_type_avion = tyav.id_type_avion')
							->where('avi.id_type_avion = ?', $infosVol['idTypeAvion'])
							->where('id_avion NOT IN ?', $subReqAvion)
							->where('disponibilite_avion = 1')
							->where('rayon_action > ?', $infosVol['distance'])
							->where('longueur_atterissage < ?', $infosVol['longueurPiste'])
							->group('tyav.libelle')
							->order('tyav.id_type_avion');
		}
		else{
			$reqAvion = $this->select()
							->setIntegrityCheck(false)
							->from(array('avi' => 'avion'), array('avi.id_avion'))
							->joinLeft(array('tyav' => 'type_avion'), 'avi.id_type_avion = tyav.id_type_avion')
							->where('avi.id_type_avion = ?', $infosVol['idTypeAvion'])
							->where('id_avion NOT IN ?', $subReqAvion)
							->where('disponibilite_avion = 1')
							->where('rayon_action > ?', $infosVol['distance'])
							->where('longueur_atterissage < ?', $infosVol['longueurPiste'])
							->orWhere('id_avion = ?', $infosVol['idAvion'])
							->group('tyav.libelle')
							->order('tyav.id_type_avion');
		}
		
		
		return $this->fetchRow($reqAvion);
	}
	
	public function checkAvionDispo($dateDepart, $numeroLigne, $idAvion){
		$TableVol = new Vol;
		$TableAstreinte = new Astreinte;
		$TableAvion = new Avion;
		$TableBreveter = new EtreBreveter;
		$TableLigne = new Ligne;
	
		$InfosVol = $TableVol->getInfosVol($numeroLigne, $dateDepart);
		$dateArrivee = $InfosVol->date_arrivee;
		$idAeroport = $InfosVol->id_aeroport_depart_effectif;
		$heureArrivee = $InfosVol->heure_arrivee_effective;
	
		$Ligne = $TableLigne->find($numeroLigne)->current();
		$heureDepart = $Ligne->heure_depart;
	
		$subReqAvion = $TableVol->select()
								->setIntegrityCheck(false)
								->from(array('v' => 'vol'),  array('id_avion'))
								->join(array('l' => 'ligne'), 'v.numero_ligne = l.numero_ligne', null)
								->where('date_depart = ?', $dateDepart)
								->where('v.numero_ligne != ?', $numeroLigne)
								->where('UNIX_TIMESTAMP(CONCAT(v.date_depart," ",l.heure_depart)) BETWEEN UNIX_TIMESTAMP(CONCAT("'.$dateDepart.'"," ","'.$heureDepart.'")) AND UNIX_TIMESTAMP(CONCAT("'.$dateArrivee.'"," ","'.$heureArrivee.'")) OR UNIX_TIMESTAMP(CONCAT(v.date_arrivee," ",v.heure_arrivee_effective)) BETWEEN UNIX_TIMESTAMP(CONCAT("'.$dateDepart.'"," ","'.$heureDepart.'")) AND UNIX_TIMESTAMP(CONCAT("'.$dateArrivee.'"," ","'.$heureArrivee.'"))');
		
		$req = $this->select()
					->setIntegrityCheck(false)
					->from(array('av' => 'avion'))
					->where('id_avion = ?', $idAvion)
					->where('id_avion NOT IN (?)', $subReqAvion);
	
		return $this->fetchRow($req);
	}
}

