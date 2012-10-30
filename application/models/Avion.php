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
	
	public function getAvionDispoByType($infosVol, $actions){
		$TableVol = new Vol();
		
		$dateDepart = $infosVol['dateDepart'];
		$heureDepart = $infosVol['heureDepart'];
		$numeroLigne = $infosVol['numeroLigne'];
		$distance = $infosVol['distance'];
		$longueurPiste = $infosVol['longueurPiste'];
		$idTypeAvion = $infosVol['idTypeAvion'];
		
		if($actions == 'Planifier'){
			$subReqAvion =  $TableVol->select()
									->setIntegrityCheck(false)
									->from(array('v' => 'Vol'), 'v.id_avion')
									->where('date_arrivee = ?', $dateDepart)
									->where('heure_arrivee_effective > ?', $heureDepart);
		}
		else{
			$subReqAvion =  $TableVol->select()
									->setIntegrityCheck(false)
									->from(array('v' => 'Vol'), 'v.id_avion')
									->where('numero_ligne != ?', $numeroLigne)
									->where('date_arrivee = ?', $dateDepart)
									->where('heure_arrivee_effective > ?', $heureDepart);
		}
		
		$reqAvion = $this->select()
						->setIntegrityCheck(false)
						->from(array('avi' => 'Avion'), array('avi.id_avion'))
						->joinLeft(array('tyav' => 'Type_Avion'), 'avi.id_type_avion = tyav.id_type_avion')
						->where('avi.id_type_avion = ?', $idTypeAvion)
						->where('id_avion NOT IN ?', $subReqAvion)
						->where('disponibilite_avion = 1')
						->where('rayon_action > ?', $distance)
						->where('longueur_atterissage < ?', $longueurPiste)
						->group('tyav.libelle')
						->order('tyav.id_type_avion');
		
		return $this->fetchRow($reqAvion);
	}
}

