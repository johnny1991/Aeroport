<?php
class PlanificationVol extends Zend_Form
{
	protected $TableAvion;
	protected $TableLigne;
	protected $TableVol;
	protected $_dateDepart;
	protected $_numeroLigne;
	protected $_action;

	public function __construct($dateDepart, $numeroLigne, $action, $options = NULL){
		$this->TableAvion= new Avion();
		$this->TableLigne = new Ligne();
		$this->TableVol = new Vol();
		$this->_dateDepart = $dateDepart;
		$this->_numeroLigne = $numeroLigne;
		$this->_action = $action;
		
		parent::__construct($options);
	}

	public function init(){
		
		//Récupére les infos du vol si c'est une modification
		if($this->_action == 'Modifier'){
			$reqVol = $this->TableVol->select()->setIntegrityCheck(false)
											->from('Vol')
											->where('numero_ligne = ?', $this->_numeroLigne)
											->where('date_depart = ?', $this->_dateDepart);
			
			$infosVol = $this->TableVol->fetchRow($reqVol);
		}
		else
			$infosVol = false;

		//Récupère les infos de la ligne
		$infosLigne = $this->TableLigne->getAeroportByAeroportArrivee($this->_numeroLigne);
		
		$distance = $infosLigne->distance;
		$longueurPiste = $infosLigne->longueur_piste;
		$heureDepart = $infosLigne->heure_depart;
		
		if($this->_action == 'Planifier'){
			$subReqAvion =  $this->TableVol->select()
										->setIntegrityCheck(false)
										->from(array('v' => 'Vol'), 'v.id_avion')
										->where('date_arrivee = ?', $this->_dateDepart)
										->where('heure_arrivee_effective > ?', $heureDepart);
		}
		else{
			$subReqAvion =  $this->TableVol->select()
										->setIntegrityCheck(false)
										->from(array('v' => 'Vol'), 'v.id_avion')
										->where('numero_ligne != ?', $this->_numeroLigne)
										->where('date_arrivee = ?', $this->_dateDepart)
										->where('heure_arrivee_effective > ?', $heureDepart);
		}
		
		$reqAvion = $this->TableAvion->select()
									->setIntegrityCheck(false)
									->from(array('avi' => 'Avion'))
									->joinLeft(array('tyav' => 'Type_Avion'), 'avi.id_type_avion = tyav.id_type_avion', array('tyav.libelle', 'tyav.id_type_avion'))
									->where('id_avion NOT IN ?', $subReqAvion)
									->where('disponibilite_avion = 1')
									->where('rayon_action > ?', $distance)
									->where('longueur_atterissage < ?', $longueurPiste)
									->group('tyav.libelle')
									->order('tyav.id_type_avion');
		
		$listeAvions = $this->TableAvion->fetchAll($reqAvion);
		
		$EAvion = new Zend_Form_Element_Select('avion');
		foreach($listeAvions as $avion){
			$EAvion->addMultiOption($avion->id_type_avion, $avion->libelle);
		}
		
		$EAvion->setLabel('Liste des avions');
		$EAvion->setAttrib('onchange', 'recherchePilote(\''.$this->_numeroLigne.'\', \''.$heureDepart.'\', \''.$this->_dateDepart.'\', this.value, \''.$this->_action.'\')');
		$EAvion->setRequired(true);
		
		if($infosVol != false){
			$EAvion->setValue($infosVol->id_avion);
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
			$infosPilote = $TablePilote->getPiloteByTypeAvionUpdate($heureDepart, $this->_dateDepart, $infosVol->id_avion, $this->_numeroLigne);
		}	
		else{
			$infosPilote = $TablePilote->getPiloteByTypeAvion($heureDepart, $this->_dateDepart, $listeAvions[0]['id_type_avion']);
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
		$this->setAction('/planning/planificationvol/date/'.$this->_dateDepart.'/numeroligne/'.$this->_numeroLigne.'/action/'.$this->_action);
		
		$this->addElement($EAvion);
		$this->addElement($EPilote);
		$this->addElement($ECoPilote);
		
		$this->addElement($ESubmit);


	}
}