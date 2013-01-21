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
	
	public function getAvionDispoByTypeByVol($numeroLigne, $dateDepart, $idTypeAvion, $update = false){
		$TableVol = new Vol();
		$TableLigne = new Ligne();
		
		$infosVol = $TableVol->getInfosVol($numeroLigne, $dateDepart);
		$infosLigne = $TableLigne->find($numeroLigne)->current();
		$aeroportArrivee = $infosLigne->findParentRow('Aeroport', 'aeroport_arrivee');
		
		if($update == false)
			$subReqAvion = $TableVol->getIdAvionNoDispoByVol($numeroLigne, $dateDepart);
		else
			$subReqAvion = $TableVol->getIdAvionNoDispoByVol($numeroLigne, $dateDepart, true);
			
		if($update == false){
			$reqAvion = $this->select()
							->setIntegrityCheck(false)
							->from(array('avi' => 'avion'), array('avi.id_avion'))
							->joinLeft(array('tyav' => 'type_avion'), 'avi.id_type_avion = tyav.id_type_avion')
							->where('avi.id_type_avion = ?', $idTypeAvion)
							->where('id_avion NOT IN ?', $subReqAvion)
							->where('disponibilite_avion = 1')
							->where('rayon_action > ?', $infosLigne->distance)
							->where('longueur_atterrissage < ?', $aeroportArrivee->longueur_piste)
							->order('tyav.id_type_avion');
		}
		else{
			
			$subReqAvionType = $this->getReqIdAvionByType($idTypeAvion);
			
			$reqAvion = $this->select()
							->setIntegrityCheck(false)
							->from(array('avi' => 'avion'), array('avi.id_avion'))
							->joinLeft(array('tyav' => 'type_avion'), 'avi.id_type_avion = tyav.id_type_avion')
							->where('avi.id_type_avion = ?', $idTypeAvion)
							->where('id_avion NOT IN ?', $subReqAvion)
							->where('disponibilite_avion = 1')
							->where('rayon_action > ?', $infosLigne->distance)
							->where('longueur_atterrissage < ?', $aeroportArrivee->longueur_piste)
							->orWhere('id_avion IN ('.$subReqAvionType.') AND id_avion = '.$infosVol['id_avion'])
							->order('tyav.id_type_avion');
		}
		
		
		return $this->fetchRow($reqAvion);
	}
	
	public function getReqIdAvionByType($idTypeAvion){
		$req = $this->select()
					->setIntegrityCheck(false)
					->from(array('av' => 'avion'), array('id_avion'))
					->where('id_type_avion = ?', $idTypeAvion);
		
		return $req;
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
	
	public function getTypeAvionDispoByVol($numeroLigne, $dateDepart, $update = false){
		$TableVol = new Vol;
		$TableLigne = new Ligne;
		
		$infosLigne = $TableLigne->find($numeroLigne)->current();
		$aeroportArrivee = $infosLigne->findParentRow('Aeroport','aeroport_arrivee');
		
		$distance = $infosLigne->distance;
		$longueurPiste = $aeroportArrivee->longueur_piste;
		
		if($update == false)
			$subReqAvion = $TableVol->getIdAvionNoDispoByVol($numeroLigne, $dateDepart);
		else
			$subReqAvion = $TableVol->getIdAvionNoDispoByVol($numeroLigne, $dateDepart, true);
		
		if($update == false){
			$reqAvion = $this->select()
							->setIntegrityCheck(false)
							->from(array('avi' => 'avion'))
							->joinLeft(array('tyav' => 'type_avion'), 'avi.id_type_avion = tyav.id_type_avion', array('tyav.libelle', 'tyav.id_type_avion'))
							->where('id_avion NOT IN ?', $subReqAvion)
							->where('disponibilite_avion = 1')
							->where('rayon_action > ?', $distance)
							->where('longueur_atterrissage < ?', $longueurPiste)
							->group('tyav.libelle')
							->order('tyav.id_type_avion');
		}
		else{
			$infosVol = $TableVol->getInfosVol($numeroLigne, $dateDepart);
			
			$reqAvion = $this->select()
							->setIntegrityCheck(false)
							->from(array('avi' => 'avion'))
							->joinLeft(array('tyav' => 'type_avion'), 'avi.id_type_avion = tyav.id_type_avion', array('tyav.libelle', 'tyav.id_type_avion'))
							->where('id_avion NOT IN ?', $subReqAvion)
							->where('disponibilite_avion = 1')
							->where('rayon_action > ?', $distance)
							->where('longueur_atterrissage < ?', $longueurPiste)
							->orWhere('id_avion = ?', $infosVol->id_avion)
							->group('tyav.libelle')
							->order('tyav.id_type_avion');
		}
		
		return $this->fetchAll($reqAvion);
		
	}
}

