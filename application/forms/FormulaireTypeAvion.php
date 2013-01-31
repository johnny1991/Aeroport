<?php
class FormulaireTypeAvion extends Zend_Form
{
	public function init(){

		$FormErrors=new Zend_Form_Decorator_FormErrors();
		
		$FormErrors->setMarkupListItemStart('<div>');
		$FormErrors->setMarkupListItemEnd('</div>');
		$decoratorsForm = array('FormElements',	array('HtmlTag', array('tag' => 'div','class' =>'formulaireLigne')),'Form');
		
		$decorators = new Aeroport_Form_DecorateurElement();
		$decorators->GestionClass("Global","Global_label","Global_input","Global_error");
		$decorators = array($decorators);

		$ENom = new Zend_Form_Element_Text('nom');
		$ENom->setLabel('Nom du modèle');
		$ENom->setDecorators($decorators);
		$ENom->setRequired(true);

		$ERayon = new Zend_Form_Element_Text('rayon');
		$ERayon->addValidator('Digits');
		$ERayon->setLabel('Rayon d\'action (km)');
		$ERayon->setDecorators($decorators);
		$ERayon->setAttrib('size','2');
		$ERayon->setRequired(true);
		
		$EDecollage = new Zend_Form_Element_Text('decollage');
		$EDecollage->addValidator('Digits');
		$EDecollage->setLabel('Longueur de décollage (m)');
		$EDecollage->setDecorators($decorators);
		$EDecollage->setAttrib('size','2');
		$EDecollage->setRequired(true);
		
		$EAtterrissage = new Zend_Form_Element_Text('atterrissage');
		$EAtterrissage->addValidator('Digits');
		$EAtterrissage->setLabel('Longueur d\'atterrissage (m)');
		$EAtterrissage->setDecorators($decorators);
		$EAtterrissage->setAttrib('size','2');
		$EAtterrissage->setRequired(true);
		
		$ESubmit = new Zend_Form_Element_Submit('Ajouter');
		$ESubmit->setDecorators($decorators);

		$this->setMethod('post');
		$this->addElement($ENom);
		$this->addElement($ERayon);
		$this->addElement($EDecollage);
		$this->addElement($EAtterrissage);
		$this->addElement($ESubmit);
		$this->setDecorators($decoratorsForm);
		

	}
}