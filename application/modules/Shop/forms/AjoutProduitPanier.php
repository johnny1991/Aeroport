<?php
class Shop_Form_AjoutProduitPanier extends Zend_Form{

	public function init(){
		$id = new Zend_Form_Element_Hidden('id');
		
		$quantite = new Zend_Form_Element_Text('quantite');
		$quantite->setAttrib('size', '2');
		$quantite->setValue('1');
		$quantite->addValidator('Digits');
		$idProduit = new Zend_Form_Element_Hidden('id');
		$submit = new Zend_Form_Element_Submit('ajouter');
		$submit->setLabel("Ajouter au panier");		
		$this->addElement($id);
		$this->addElement($quantite);
		$this->addElement($idProduit);
		$this->addElement($submit);
		$this->setMethod("POST");
	}
}