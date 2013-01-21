<?php
class Shop_Form_AjoutCategorie extends Zend_Form
{
	public function isValid($values)
	{
		if ( ($values["newCategorie"] == "") && ($values["newSousCategorie"] == "") ) {
			return false;
		}
		else if($values["newCategorie"] != ""){
			if ( ($values["categorie"] != "0") || ($values["newSousCategorie"] != "") ){
				$this->getElement("newCategorie")->addError("Vous ne pouvez pas ajouter une catégorie et une sous-catégorie en même temps");
				$this->getElement("newSousCategorie")->addError("Vous ne pouvez pas ajouter une catégorie et une sous-catégorie en même temps");
				return false;
			}
		}
		else if($values["newCategorie"] == ""){
			if ( ($values["newSousCategorie"] != "") && ($values["categorie"] == "0") ){
				$this->getElement("categorie")->addError("Vous devez choisir une catégorie");
				return false;
			}
		}
		return parent::isValid($values); // NE SURTOUT PAS OUBLIER CETTE LIGNE
	}

	public function init(){

		$NewCategorie = new Zend_Form_Element_Text('newCategorie');
		$NewCategorie->setLabel('Nouvelle catégorie *');
		$NewCategorie->addValidator("Alnum",'',array('allowWhiteSpace' => true));
		
		$categorie = new Zend_Form_Element_Select('categorie');
		$TableCategorie = new Categorie();
		$categorie->setAttrib('onchange', 'AffichageSousCategorie()');
		$categorie->addMultiOption('0',"Choisissez la catégorie");
		foreach ($TableCategorie->fetchAll() as $Categorie)
		{
			$categorie->addMultiOption($Categorie->id_categorie,$Categorie->libelle);
		}
		$categorie->setValue('0');
		$categorie->setRegisterInArrayValidator(false);
		$categorie->addValidator('Digits');

		$NewSousCategorie = new Zend_Form_Element_Text('newSousCategorie');
		$NewSousCategorie->setLabel('Nouvelle sous-catégorie *');
		$NewSousCategorie->addValidator("Alnum",'',array('allowWhiteSpace' => true));
		
		$submit = new Zend_Form_Element_Submit('Ajouter');
		
		$this->setMethod("post");

		$this->addElement($NewCategorie);
		$this->addElement($categorie);
		$this->addElement($NewSousCategorie);
		$this->addElement($submit);
	}
}