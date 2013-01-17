<?php
class AjouterService extends Zend_Form
{
	protected $_dateDepart;
	protected $_numeroLigne;
	protected $_action;

	public function __construct($options = NULL){
		$params = Zend_Controller_Front::getInstance()->getRequest()->getParams();
		parent::__construct($options);
	}

	public function init(){
		
		$EService = new Zend_Form_Element_Text('service');
		$ESubmit = new Zend_Form_Element_Submit('ajouter');
		
		$EService->addValidator(new Zend_Validate_Db_NoRecordExists('Service', 'libelle_service'));
		$EService->removeDecorator('label');
		$EService->removeDecorator('HtmlTag');
		$EService->removeDecorator('Errors');
		$EService->setRequired(true);
		
		$ESubmit->setLabel('Ajouter');
		$ESubmit->removeDecorator('DtDdWrapper')
            ->removeDecorator('HtmlTag')
            ->removeDecorator('Label');
		
		
		//$this->setAction('/drh/ajouter-service');
		$this->setMethod('POST');
		$this->addElement($EService);
		$this->addElement($ESubmit);
	}
}