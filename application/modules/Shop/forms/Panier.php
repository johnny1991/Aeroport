<?php
class Shop_Form_Panier extends Zend_Form
{
	public function init(){
		$submit = new Zend_Form_Element_Submit('update');
		$submit->setLabel('Mettre à jour le panier');
		
		$this->setMethod('post');
		$this->addElement($submit);
	}
}