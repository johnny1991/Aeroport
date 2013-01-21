<?php
class Shop_Form_SearchBar extends Zend_Form
{
	
	public function init(){
		
		$Texte = new Zend_Form_Element_Text('mot');
		$Texte->setValue("Rechercher ...");
		$Texte->setAttrib('onclick', 'ChangeRecherche();');
		$Texte->setAttrib('onBlur', 'ChangeRecherche();');
		$submit = new Zend_Form_Element_Submit('Rechercher');
		$submit->setLabel(null);
		
		$this->setAction('catalogue');
		$this->setMethod('get');
		$this->addElement($Texte);
		$this->addElement($submit);
	}
}
