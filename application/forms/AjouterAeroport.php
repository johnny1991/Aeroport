<?php
class AjouterAeroport extends Zend_Form{

	private $_id;
	
	public function __construct($options = NULL){
		$params = Zend_Controller_Front::getInstance()->getRequest()->getParams();
		
		$this->_id = (isset($params['id'])) ? $params['id'] : false;
		
		parent::__construct($options);
	}

	public function init(){
		
		$tableVille = new Ville();
		$tablePays = new Pays();
		$tableAeroport = new Aeroport();
		
		$infos = ($this->_id != false) ? $tableAeroport->getInfosById($this->_id) : null;
		$payss = $tablePays->getPaysWithOrder('nom asc');
		
		$ENom = new Zend_Form_Element_Text('nom');
		$EId = new Zend_Form_Element_Text('id');
		$EAdresse = new Zend_Form_Element_Text('adresse');
		$EPays = new Zend_Form_Element_Select('pays');
		$EVille = new Zend_Form_Element_Select('ville');
		$ELongueur = new Zend_Form_Element_Text('longueur');
		$ESubmit = new Zend_Form_Element_Submit('ajouter');
		
		if($infos != null){
			
			$villes = $tableVille->getVillesByIdPays($infos['pays']);
			foreach($villes as $ville){
				$EVille->addMultiOption($ville->code_ville, $ville->nom);
			}
			
			$ENom->setValue($infos['nom']);
			$EId->setValue($infos['id_aeroport']);
			$EPays->setValue($infos['pays']);
			$EVille->setValue($infos['code_ville']);
			$EAdresse->setValue($infos['adresse']);
			$ELongueur->setValue($infos['longueur_piste']);
			
			$this->setAction('/crud/modifier-aeroport/id/'.$this->_id);
			$ESubmit->setLabel('Modifier');
			
			$optionId = array('table' => 'aeroport', 'field' => 'id_aeroport', 'exclude' => array('field' => 'id_aeroport', 'value' => $EId->getValue()));
			$optionNom = array('table' => 'aeroport', 'field' => 'nom', 'exclude' => array('field' => 'nom', 'value' => $ENom->getValue()));
		}else{
			$this->setAction('/crud/ajouter-aeroport');
			$ESubmit->setLabel('Ajouter');
			
			$optionId = array('table' => 'aeroport', 'field' => 'id_aeroport');
			$optionNom = array('table' => 'aeroport', 'field' => 'nom');
			
			$villes = $tableVille->getVillesByIdPays(250);
			foreach($villes as $ville){
				$EVille->addMultiOption($ville->code_ville, $ville->nom);
			}
			$EPays->setValue(250);
		}
		
		$EId->setLabel('Trigramme du nouvel aéroport:');
		$EId->addValidator(new Zend_Validate_Db_NoRecordExists($optionId));
		$EId->setRequired(true);
		
		$ENom->setLabel('Nom de l\'aéroport:');
		$ENom->addValidator(new Zend_Validate_Db_NoRecordExists($optionNom));
		$ENom->setRequired(true);
		
		$EAdresse->setLabel('Adresse de l\'aéroport:');
		$EAdresse->setRequired(true);
		
		$ELongueur->setLabel('Longueur de piste (en mètre):');
		$ELongueur->setRequired(true);
	
		$EPays->setLabel('Le pays:');
		foreach($payss as $pays){
			$EPays->addMultiOption($pays->code_pays, $pays->nom);
		}
		$EPays->setRequired(true);
		$EPays->setAttrib('onchange', 'rechercheVille(this.value)');
		
		$EVille->setLabel('La ville:');
		$EVille->setRequired(true);
		$EVille->setRegisterInArrayValidator(false);
		
		$ESubmit->removeDecorator('DtDdWrapper')->removeDecorator('HtmlTag')->removeDecorator('Label');
		
		$this->setMethod('POST');
	
		$this->addElement($EId);
		$this->addElement($ENom);
		$this->addElement($EPays);
		$this->addElement($EVille);
		$this->addElement($EAdresse);
		$this->addElement($ELongueur);
		$this->addElement($ESubmit);
	}
}