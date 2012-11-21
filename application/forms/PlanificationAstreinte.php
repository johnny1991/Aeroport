<?php
class PlanificationAstreinte extends Zend_Form
{
	protected $TablePilote;
	protected $TableAstreinte;
	protected $_date;
	protected $_idAeroport;
	protected $_action;
	protected $_nbPilote;
	protected $_nbVol;

	public function __construct($options = NULL){
		
		$params = Zend_Controller_Front::getInstance()->getRequest()->getParams();

		$this->TablePilote = new Pilote();
		$this->TableAstreinte = new Astreinte();
		
		$this->_date = $params['date'];
		$this->_idAeroport = $params['idaeroport'];
		$this->_nbPilote = floor($params['nbvol'] / 5) * 2;
		$this->_nbVol = $params['nbvol'];

		parent::__construct($options);
	}

	public function init(){
		
		//Récupére les infos de l'astreinte;
		$infosAstreinte = $this->TableAstreinte->getInfosAstreinte($this->_date, $this->_idAeroport);
		$nbAstreinte = count($infosAstreinte);
		
		//Récupère la liste des pilotes dispo;
		if($nbAstreinte == 0)
			$infosPilote = $this->TablePilote->getPiloteDispoAstreinte($this->_date, $this->_idAeroport);
		else
			$infosPilote = $this->TablePilote->getPiloteDispoAstreinte($this->_date, $this->_idAeroport, true);
		
		if($nbAstreinte == 0){
			for($i=0;$i<=$this->_nbPilote - 1 ;$i++){
				$eltsSelect = new Zend_Form_Element_Select('pilote'.$i);
				$eltsSelect->setLabel('Liste des pilotes');
				$eltsSelect->setAttrib('onchange', 'MaJSelect('.$i.')');
				$eltsSelect->setRequired(true);
					
				foreach($infosPilote as $pilote){
					$eltsSelect->addMultiOption($pilote->id_pilote, $pilote->nom.' '.$pilote->prenom);
				}
					
				$eltsSelect->setRegisterInArrayValidator(false);
				$this->addElement($eltsSelect);
			}
		}
		else{
			$index = 0;
			foreach($infosAstreinte as $info){
				$eltsSelect = new Zend_Form_Element_Select('pilote'.$index);
				$eltsSelect->setLabel('Liste des pilotes');
				$eltsSelect->setAttrib('onchange', 'MaJSelect('.$index.')');
				$eltsSelect->setRequired(true);
				
				foreach($infosPilote as $pilote){
					$eltsSelect->addMultiOption($pilote->id_pilote, $pilote->nom.' '.$pilote->prenom);
				}
				
				$eltsSelect->setValue($info->id_pilote);
				
				$eltsSelect->setRegisterInArrayValidator(false);
				$this->addElement($eltsSelect);
				
				$index++;
			}
			
			if($this->_nbPilote  > $nbAstreinte){
				$nbRestant = $this->_nbPilote  - $nbAstreinte;
				$tourPlus = ($index + $nbRestant) - 1;
				
				for($j=$index;$j<= $tourPlus;$j++){
					$eltsSelect = new Zend_Form_Element_Select('pilote'.$j);
					$eltsSelect->setLabel('Liste des pilotes');
					$eltsSelect->setAttrib('onchange', 'MaJSelect('.$j.')');
					$eltsSelect->setRequired(true);
					
					foreach($infosPilote as $pilote){
						$eltsSelect->addMultiOption($pilote->id_pilote, $pilote->nom.' '.$pilote->prenom);
					}
					
					$eltsSelect->setRegisterInArrayValidator(false);
					$this->addElement($eltsSelect);
				}
			}
		}
		
	
		if($nbAstreinte != 0){
			$ESubmit = new Zend_Form_Element_Submit('Modifier');
		}
		else
			$ESubmit = new Zend_Form_Element_Submit('Planifier');
		
		$this->setMethod('post');
		$this->setAction('/planning/planifier-astreinte/idaeroport/'.$this->_idAeroport.'/date/'.$this->_date.'/nbvol/'.$this->_nbVol.'/');
	
		$this->addElement($ESubmit);

	}
}