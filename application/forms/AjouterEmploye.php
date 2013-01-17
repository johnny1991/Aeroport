<?php
class AjouterEmploye extends Zend_Form{

	private $_id;
	
	public function __construct($options = NULL){
		$params = Zend_Controller_Front::getInstance()->getRequest()->getParams();
		
		$this->_id = (isset($params['id'])) ? $params['id'] : false;
		
		parent::__construct($options);
	}

	public function init(){
		
		$tableService = new Service();
		$tablePays = new Pays();
		$tableUser = new Utilisateur();
		
		$infosUser = ($this->_id != false) ? $tableUser->getInfosById($this->_id)->toArray() : null;
		$payss = $tablePays->fetchAll(null, 'nom');
		$services = $tableService->fetchAll(null, 'libelle_service');
		
		$ENom = new Zend_Form_Element_Text('nom');
		$EPrenom = new Zend_Form_Element_Text('prenom');
		$EMail = new Zend_Form_Element_Text('mail');
		$EPays = new Zend_Form_Element_Select('pays');
		$EVille = new Zend_Form_Element_Select('ville');
		$EService = new Zend_Form_Element_Select('service');
		$EAdresse = new Zend_Form_Element_Text('adresse');
		$ETelephone = new Zend_Form_Element_Text('telephone');
		$ESubmit = new Zend_Form_Element_Button('ajouter');
		
		$ENom->setLabel('Le nom:');
		$ENom->setRequired(true);
	
		$EPrenom->setLabel('Le prénom:');
		$EPrenom->setRequired(true);
		
		$EMail->setLabel('L\'adresse e-mail:');
		$EMail->addValidator('EmailAddress');
		$EMail->setRequired(true);
		
		$EPays->setLabel('Le pays:');
		$EPays->setAttrib('onchange', 'changeVille(0)');
		foreach($payss as $pays){
			$EPays->addMultiOption($pays->code_pays, $pays->nom);
		}
		$EPays->setRequired(true);
		$EPays->setValue(250);
		
		$EVille->setLabel('La ville:');
		$EVille->setRegisterInArrayValidator(false);
		
		$EService->setLabel('Le service:');
		$EService->setAttrib('onchange', 'addBrevet()');
		foreach($services as $service){
			$EService->addMultiOption($service->id_service, $service->libelle_service);
		}
		$EService->setRequired(true);
		
		$EAdresse->setLabel('L\'adresse:');
		$EAdresse->setRequired(true);
		
		$ETelephone->setLabel('Le numéro de téléphone:');
		$ETelephone->addValidator('Int');
		$ETelephone->setRequired(true);
		
		if($infosUser != null){
			$ENom->setValue($infosUser['nom']);
			$EPrenom->setValue($infosUser['prenom']);
			$EMail->setValue($infosUser['email']);
			$EPays->setValue($infosUser['code_pays']);
			$EVille->setValue($infosUser['code_ville']);
			$ETelephone->setValue($infosUser['telephone']);
			$EService->setValue($infosUser['id_service']);
			$EAdresse->setValue($infosUser['adresse']);
			$this->setAction('/drh/modifier-employe/id/'.$this->_id);
			$ESubmit->setLabel('Modifier');
		}else{
			$this->setAction('/drh/ajouter-employe');
			$ESubmit->setLabel('Ajouter');
		}
		
		
		$ESubmit->setAttrib('onclick', 'verifDatePicker()');
		$ESubmit->removeDecorator('DtDdWrapper')->removeDecorator('HtmlTag')->removeDecorator('Label');
		
		
		$this->setMethod('POST');
		$this->setAttrib('id', 'ajouterEmploye');
		
		$this->addElement($EService);
		$this->addElement($ENom);
		$this->addElement($EPrenom);
		$this->addElement($EMail);
		$this->addElement($EPays);
		$this->addElement($EVille);
		$this->addElement($EAdresse);
		$this->addElement($ETelephone);
		$this->addElement($ESubmit);
	}
}