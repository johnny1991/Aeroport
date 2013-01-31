<?php
class AjouterVille extends Zend_Form{

	private $_id;
	
	public function __construct($options = NULL){
		$params = Zend_Controller_Front::getInstance()->getRequest()->getParams();
		
		$this->_id = (isset($params['id'])) ? $params['id'] : false;
		
		parent::__construct($options);
	}

	public function init(){
		
		$tableVille = new Ville();
		$tablePays = new Pays();
		
		$infos = ($this->_id != false) ? $tableVille->getInfosById($this->_id)->toArray() : null;
		$payss = $tablePays->fetchAll();
		
		$ENom = new Zend_Form_Element_Text('nom');
		$ECodePays = new Zend_Form_Element_Select('code_pays');
		$ECodePostal = new Zend_Form_Element_Text('code_postal');
		$ESubmit = new Zend_Form_Element_Submit('ajouter');
		
		if($infos != null){
			$ENom->setValue($infos['nom']);
			$ECodePays->setValue($infos['code_pays']);
			$ECodePostal->setValue($infos['code_postal']);
			
			$this->setAction('/crud/modifier-ville/id/'.$this->_id);
			$ESubmit->setLabel('Modifier');
			
			$optionNom = array('table' => 'ville', 'field' => 'nom', 'exclude' => array('field' => 'nom', 'value' => $ENom->getValue()));
		}else{
			$this->setAction('/crud/ajouter-ville');
			$ESubmit->setLabel('Ajouter');
			
			$optionNom = array('table' => 'ville', 'field' => 'nom');
		}
		
		$ENom->setLabel('Le nom de la nouvelle ville:');
		$ENom->addValidator(new Zend_Validate_Db_NoRecordExists($optionNom));
		$ENom->setRequired(true);
	
		$ECodePays->setLabel('Son pays:');
		foreach($payss as $pays){
			$ECodePays->addMultiOption($pays->code_pays, $pays->nom);
		}
		$ECodePays->setRequired(true);
		
		$ECodePostal->setLabel('Son code postal:');
		
		$ESubmit->removeDecorator('DtDdWrapper')->removeDecorator('HtmlTag')->removeDecorator('Label');
		
		$this->setMethod('POST');
		
		$this->setAttrib('id', 'ajouterVille');
		$this->addElement($ENom);
		$this->addElement($ECodePays);
		$this->addElement($ECodePostal);
		$this->addElement($ESubmit);
	}
}