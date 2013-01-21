<?php
class Shop_Form_UpdateCategorie extends Zend_Form
{
	public function isValid($values)
	{
		if (($values["newCategorie"] == "") && ($values["newSousCategorie"] == ""))
		{
			return false;
		}
		else if (($values["newCategorie"] != "") && ($values["newSousCategorie"] != "")){
			$this->getElement("newCategorie")->addError("Vous ne pouvez pas ajouter une catégorie et une sous-catégorie en même temps");
			$this->getElement("newSousCategorie")->addError("Vous ne pouvez pas ajouter une catégorie et une sous-catégorie en même temps");
			return false;
		}
		else if($values["newCategorie"] != "") // Une nouvelle categorie
		{
			if($values["categorie1"] == 0)
			{
				$this->getElement("categorie1")->addError("Vous devez choisir une catégorie");
				return false;
			}
		}
		else if($values["newCategorie"] == "") // Une nouvelle sous-categorie
		{
			if($values["categorie"] == 0)
			{
				$this->getElement("categorie")->addError("Vous devez choisir une catégorie");
				return false;
			}
			if($values["sousCategorie"] == 0)
			{
				$this->getElement("sousCategorie")->addError("Vous devez choisir une sous-catégorie");
				return false;
			}
		}

		return parent::isValid($values); // NE SURTOUT PAS OUBLIER CETTE LIGNE
	}

	public function init(){

		$categorie1 = new Zend_Form_Element_Select('categorie1');
		$TableCategorie = new Categorie();
		$categorie1->setAttrib('onchange', 'AffichageSousCategorie()');
		$categorie1->addMultiOption('0',"Choisissez la catégorie");
		$categorie1->setValue('0');
		$categorie1->setRegisterInArrayValidator(false);
		$categorie1->addValidator('Digits');

		$NewCategorie = new Zend_Form_Element_Text('newCategorie');
		$NewCategorie->setLabel('Nom de la catégorie *');
		$NewCategorie->addValidator("Alnum",'',array('allowWhiteSpace' => true));

			
		$categorie = new Zend_Form_Element_Select('categorie');
		$TableCategorie = new Categorie();
		$categorie->setAttrib('onchange', 'AffichageSousCategorie()');
		$categorie->addMultiOption('0',"Choisissez la catégorie");
		foreach ($TableCategorie->fetchAll() as $Categorie)
		{
			$categorie->addMultiOption($Categorie->id_categorie,$Categorie->libelle);
			$categorie1->addMultiOption($Categorie->id_categorie,$Categorie->libelle);
		}
		$categorie->setValue('0');
		$categorie->setRegisterInArrayValidator(false);
		$categorie->addValidator('Digits');
		$categorie->setAttrib('onchange',"AffichageSousCategorie()");

		$sousCategorie = new Zend_Form_Element_Select('sousCategorie');
		$sousCategorie->setValue("Choisissez la sous-catégorie");
		$sousCategorie->setRegisterInArrayValidator(false);
		$sousCategorie->addValidator('Digits');

		$NewSousCategorie = new Zend_Form_Element_Text('newSousCategorie');
		$NewSousCategorie->setLabel('Nom de la sous-catégorie *');


		$submit = new Zend_Form_Element_Submit('Ajouter');

		$this->setMethod("post");

		$this->addElement($categorie1);
		$this->addElement($sousCategorie);
		$this->addElement($NewCategorie);
		$this->addElement($categorie);
		$this->addElement($NewSousCategorie);
		$this->addElement($submit);
	}
}