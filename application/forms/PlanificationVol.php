<?php
class PlanificationVol extends Zend_Form
{
	protected $_dateDepart;
	protected $_numeroLigne;
	protected $_action;

	public function __construct($options = NULL){
		
		$params = Zend_Controller_Front::getInstance()->getRequest()->getParams();
		
		$this->_dateDepart = $params['date'];
		$this->_numeroLigne = $params['numeroligne'];

		parent::__construct($options);
	}

	public function init(){
		
		$TableAvion = new Avion;
		$TableLigne = new Ligne;
		$TableVol = new Vol;
		
		//Récupére les infos du vol si c'est une modification
		$infosVol = $TableVol->getInfosVolWithAvion($this->_numeroLigne, $this->_dateDepart);
		$nbVol = count($infosVol);
		
		if($nbVol == 0)
			$listeAvions = $TableAvion->getTypeAvionDispoByVol($this->_numeroLigne, $this->_dateDepart);
		else
			$listeAvions = $TableAvion->getTypeAvionDispoByVol($this->_numeroLigne, $this->_dateDepart, true);
		
		if(count($listeAvions) != 0){
			
			$EAvion = new Zend_Form_Element_Select('avion');
			$EAvion->removeDecorator('label');
			$EAvion->removeDecorator('HtmlTag');
			$EAvion->setRequired(true);
			
			foreach($listeAvions as $avion){
				$EAvion->addMultiOption($avion->id_type_avion, $avion->libelle);
			}
			
			if($nbVol == 0)
				$EAvion->setAttrib('onchange', 'recherchePilote('.$this->_numeroLigne.', \''.$this->_dateDepart.'\', this.value, false)');
			else
				$EAvion->setAttrib('onchange', 'recherchePilote('.$this->_numeroLigne.', \''.$this->_dateDepart.'\', this.value, true) ');
			
			if($nbVol != 0)
				$EAvion->setValue($infosVol->id_type_avion);
		
			$EPilote = new Zend_Form_Element_Select('pilote0');
			$EPilote->setAttrib('id', 'selectPilote');
			$EPilote->setAttrib('onchange', 'MaJCoPilote(0)');
			$EPilote->removeDecorator('label');
			$EPilote->removeDecorator('HtmlTag');
			$EPilote->setRequired(true);
			
			$ECoPilote = new Zend_Form_Element_Select('pilote1');
			$ECoPilote->setAttrib('id', 'selectCoPilote');
			$ECoPilote->removeDecorator('label');
			$ECoPilote->removeDecorator('HtmlTag');
			$ECoPilote->setRequired(true);
			
			$TablePilote = new Pilote();
					
			if($nbVol != 0)
				$infosPilote = $TablePilote->getPiloteByTypeAvion($this->_numeroLigne, $this->_dateDepart, $infosVol->id_type_avion, true);
			else
				$infosPilote = $TablePilote->getPiloteByTypeAvion($this->_numeroLigne, $this->_dateDepart, $listeAvions[0]['id_type_avion']);

			foreach($infosPilote as $pilote){
				$EPilote->addMultiOption($pilote->id_pilote, $pilote->nom.' '.$pilote->prenom);
				$ECoPilote->addMultiOption($pilote->id_pilote, $pilote->nom.' '.$pilote->prenom);
			}
			
			$ESubmit = new Zend_Form_Element_Submit('bouton');
			
			if($nbVol != 0){
				$EPilote->setValue($infosVol['id_pilote']);
				$ECoPilote->setValue($infosVol['id_copilote']);
				$ESubmit->setLabel('Modifier');
			}
			else
				$ESubmit->setLabel('Planifier');
			
			$ECoPilote->setRegisterInArrayValidator(false);
			$EPilote->setRegisterInArrayValidator(false);

			$this->setMethod('post');
			$this->setAction('/planning/planifier-vol/date/'.$this->_dateDepart.'/numeroligne/'.$this->_numeroLigne);
			
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