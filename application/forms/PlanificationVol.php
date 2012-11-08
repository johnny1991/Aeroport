<?php
class PlanificationVol extends Zend_Form
{
	protected $TableAvion;
	protected $TableLigne;
	protected $TableVol;
	protected $_dateDepart;
	protected $_numeroLigne;
	protected $_action;

	public function __construct($options = NULL){
		
		$params = Zend_Controller_Front::getInstance()->getRequest()->getParams();
		
		$this->TableAvion= new Avion();
		$this->TableLigne = new Ligne();
		$this->TableVol = new Vol();
		$this->_dateDepart = $params['date'];
		$this->_numeroLigne = $params['numeroligne'];
		$this->_action = $params['actions'];

		parent::__construct($options);
	}

	public function init(){
		
		//Récupére les infos du vol si c'est une modification
		$infosVol = ($this->_action == 'Modifier') ? $this->TableVol->getInfosVolWithAvion($this->_numeroLigne, $this->_dateDepart) : false;
		
		//Récupère les infos de la ligne
		$infosAeroportArrivee = $this->TableLigne->getAeroportByAeroportArrivee($this->_numeroLigne);
		
		$distance = $infosAeroportArrivee->distance;
		$longueurPiste = $infosAeroportArrivee->longueur_piste;
		$heureDepart = $infosAeroportArrivee->heure_depart;
		$heureArrivee = $infosAeroportArrivee->heure_arrivee;

		$subReqAvion = ($this->_action == 'Planifier') ? $this->TableVol->getIdAvionNoDispo($this->_dateDepart, $heureArrivee, $heureDepart) : $this->TableVol->getIdAvionNoDispo($this->_dateDepart, $heureArrivee, $heureDepart, $this->_numeroLigne);

		if($infosVol == false){
			$reqAvion = $this->TableAvion->select()
										->setIntegrityCheck(false)
										->from(array('avi' => 'avion'))
										->joinLeft(array('tyav' => 'type_avion'), 'avi.id_type_avion = tyav.id_type_avion', array('tyav.libelle', 'tyav.id_type_avion'))
										->where('id_avion NOT IN ?', $subReqAvion)
										->where('disponibilite_avion = 1')
										->where('rayon_action > ?', $distance)
										->where('longueur_atterissage < ?', $longueurPiste)
										->group('tyav.libelle')
										->order('tyav.id_type_avion');
		}
		else{
			$reqAvion = $this->TableAvion->select()
										->setIntegrityCheck(false)
										->from(array('avi' => 'avion'))
										->joinLeft(array('tyav' => 'type_avion'), 'avi.id_type_avion = tyav.id_type_avion', array('tyav.libelle', 'tyav.id_type_avion'))
										->where('id_avion NOT IN ?', $subReqAvion)
										->where('disponibilite_avion = 1')
										->where('rayon_action > ?', $distance)
										->where('longueur_atterissage < ?', $longueurPiste)
										->orWhere('id_avion = ?', $infosVol->id_avion)
										->group('tyav.libelle')
										->order('tyav.id_type_avion');
		}
		$listeAvions = $this->TableAvion->fetchAll($reqAvion);
		
		if(count($listeAvions) != 0){
			$EAvion = new Zend_Form_Element_Select('avion');
			foreach($listeAvions as $avion){
				$EAvion->addMultiOption($avion->id_type_avion, $avion->libelle);
			}
			
			$EAvion->setLabel('Liste des avions');
			if($infosVol != false){
				$EAvion->setAttrib('onchange', 'recherchePiloteModifier('.$this->_numeroLigne.', \''.$heureArrivee.'\', \''.$heureDepart.'\', \''.$this->_dateDepart.'\', this.value, \''.$this->_action.'\', '.$infosVol->id_pilote.', '.$infosVol->id_copilote.')');
			}
			else{
				$EAvion->setAttrib('onchange', 'recherchePilote('.$this->_numeroLigne.', \''.$heureArrivee.'\', \''.$heureDepart.'\', \''.$this->_dateDepart.'\', this.value, \''.$this->_action.'\')');
			}
			$EAvion->setRequired(true);
			
			if($infosVol != false){
				$EAvion->setValue($infosVol->id_type_avion);
			}
		
			$EPilote = new Zend_Form_Element_Select('pilote');
			$EPilote->setLabel('Liste des pilotes');
			$EPilote->setAttrib('id', 'selectPilote');
			$EPilote->setAttrib('onchange', 'MaJCoPilote()');
			$EPilote->setRequired(true);
			
			$ECoPilote = new Zend_Form_Element_Select('co_pilote');
			$ECoPilote->setLabel('Liste des co-pilotes');
			$ECoPilote->setAttrib('id', 'selectCoPilote');
			$ECoPilote->setRequired(true);
			
			$TablePilote = new Pilote();
					
			if($infosVol != false){
				$infosPilote = $TablePilote->getPiloteByTypeAvionUpdate($heureDepart, $heureArrivee, $this->_dateDepart, $infosVol->id_type_avion, $this->_numeroLigne, $infosVol->id_pilote, $infosVol->id_copilote);
		
			}	
			else{
				$infosPilote = $TablePilote->getPiloteByTypeAvion($heureDepart, $heureArrivee, $this->_dateDepart, $listeAvions[0]['id_type_avion']);
			}

			foreach($infosPilote as $pilote){
				$EPilote->addMultiOption($pilote->id_pilote, $pilote->nom.' '.$pilote->prenom);
				$ECoPilote->addMultiOption($pilote->id_pilote, $pilote->nom.' '.$pilote->prenom);
			}
			
			if($infosVol != false){
				$EPilote->setValue($infosVol->id_pilote);
				$ECoPilote->setValue($infosVol->id_copilote);
				$ESubmit = new Zend_Form_Element_Submit('Modifier');
			}
			else
				$ESubmit = new Zend_Form_Element_Submit('Planifier');
			
			$ECoPilote->setRegisterInArrayValidator(false);
			$EPilote->setRegisterInArrayValidator(false);

			$this->setMethod('post');
			$this->setAction('/planning/planificationvol/date/'.$this->_dateDepart.'/numeroligne/'.$this->_numeroLigne.'/actions/'.$this->_action);
			
			$this->addElement($EAvion);
			$this->addElement($EPilote);
			$this->addElement($ECoPilote);
			
			$this->addElement($ESubmit);

		}
		else{
			echo 'Aucun avions n\'est disponible.';
		}
	}
}