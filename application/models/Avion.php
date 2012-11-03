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
							->from(array('avi' => 'Avion'), array('avi.id_avion'))
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
							->from(array('avi' => 'Avion'), array('avi.id_avion'))
							->joinLeft(array('tyav' => 'Type_Avion'), 'avi.id_type_avion = tyav.id_type_avion')
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
}

