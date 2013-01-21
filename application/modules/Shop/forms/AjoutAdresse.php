<?php
class Shop_Form_AjoutAdresse extends Zend_Form
{
	public function init()
	{
		$Adresse = new Zend_Form_Element_Text('adresse');
		$Adresse->setLabel("Adresse *");
		$Adresse->setRequired(true);
		$Adresse->setAttrib('size','30');
		//$Adresse->addValidator('Regex','',"/\w+[']?\w+/");
		//$Adresse->addValidator('Alnum','',array('allowWhiteSpace' => true));

		$Ville = new Zend_Form_Element_Text('ville');
		$Ville->setLabel('Ville *');
		$Ville->setRequired(true);
		$Ville->addValidator('Alpha','',array('allowWhiteSpace' => true));

		$CP = new Zend_Form_Element_Text('code_postal');
		$CP->setLabel('Code postal *');
		$CP->setRequired(true);
		$CP->addValidator('Digits');
		$CP->addValidator("StringLength",'',array('min' => 5,'max' => 5));


		$pays = new Zend_Form_Element_Text('pays');
		$pays->setLabel('Pays *');
		$pays->setRequired(true);
		$pays->addValidator('Alpha','',array('allowWhiteSpace' => true));
		
		$defaut = new Zend_Form_Element_Checkbox('defaut');
		$defaut->setLabel('Utiliser comme adresse de facturation par défaut');

		$submit = new Zend_Form_Element_Submit('ajouter');
		$submit->setLabel("Ajouter l'adresse");

		$this->setMethod('post');
		$this->addElement($Adresse);
		$this->addElement($Ville);
		$this->addElement($CP);
		$this->addElement($pays);
		$this->addElement($defaut);
		$this->addElement($submit);
	}
}