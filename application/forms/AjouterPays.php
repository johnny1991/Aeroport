<?php
class AjouterPays extends Zend_Form{

	private $_id;
	
	public function __construct($options = NULL){
		$params = Zend_Controller_Front::getInstance()->getRequest()->getParams();
		
		$this->_id = (isset($params['id'])) ? $params['id'] : false;
		
		parent::__construct($options);
	}

	public function init(){
		
		$tablePays = new Pays();
		
		$infos = ($this->_id != false) ? $tablePays->getInfosById($this->_id)->toArray() : null;

		$ECode = new Zend_Form_Element_Text('code');
		$ENom = new Zend_Form_Element_Text('nom');
		$EAlpha2 = new Zend_Form_Element_Text('alpha2');
		$EAlpha3 = new Zend_Form_Element_Text('alpha3');
		$ESubmit = new Zend_Form_Element_Submit('ajouter');
		
		if($infos != null){
			$ENom->setValue($infos['nom']);
			$ECode->setValue($infos['code_pays']);
			$EAlpha2->setValue($infos['alpha2']);
			$EAlpha3->setValue($infos['alpha3']);
			$this->setAction('/crud/modifier-pays/id/'.$this->_id);
			$ESubmit->setLabel('Modifier');
			
			$optionCode = array('table' => 'pays', 'field' => 'code_pays', 'exclude' => array('field' => 'code_pays', 'value' => $ECode->getValue()));
			$optionNom = array('table' => 'pays', 'field' => 'nom', 'exclude' => array('field' => 'nom', 'value' => $ENom->getValue()));
			$optionAlpha2 = array('table' => 'pays', 'field' => 'alpha2', 'exclude' => array('field' => 'alpha2', 'value' => $EAlpha2->getValue()));
			$optionAlpha3 = array('table' => 'pays', 'field' => 'alpha3', 'exclude' => array('field' => 'alpha3', 'value' => $EAlpha3->getValue()));
		}else{
			$this->setAction('/crud/ajouter-pays');
			$ESubmit->setLabel('Ajouter');
			
			$optionCode = array('table' => 'pays', 'field' => 'code_pays');
			$optionNom = array('table' => 'pays', 'field' => 'nom');
			$optionAlpha2 = array('table' => 'pays', 'field' => 'alpha2');
			$optionAlpha3 = array('table' => 'pays', 'field' => 'alpha3');
		}
		
		$ECode->setLabel('Code du pays:');
		$ECode->addValidator(new Zend_Validate_Db_NoRecordExists($optionCode));
		$ECode->addValidator('Int');
		$ECode->setRequired(true);
		
		$ENom->setLabel('Le nom du nouveau pays:');
		$ENom->addValidator(new Zend_Validate_Db_NoRecordExists($optionNom));
		$ENom->setRequired(true);
	
		$EAlpha2->setLabel('Le code alpha2:');
		$EAlpha2->addValidator(new Zend_Validate_Db_NoRecordExists($optionAlpha2));
		$EAlpha2->setRequired(true);
		
		$EAlpha3->setLabel('Le code alpha3:');
		$EAlpha3->addValidator(new Zend_Validate_Db_NoRecordExists($optionAlpha3));
		$EAlpha3->setRequired(true);
		
		$ESubmit->removeDecorator('DtDdWrapper')->removeDecorator('HtmlTag')->removeDecorator('Label');
		
		$this->setMethod('POST');
		
		$this->setAttrib('id', 'ajouterPays');
		$this->addElement($ECode);
		$this->addElement($ENom);
		$this->addElement($EAlpha2);
		$this->addElement($EAlpha3);
		$this->addElement($ESubmit);
	}
}