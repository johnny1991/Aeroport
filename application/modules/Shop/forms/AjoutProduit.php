<?php
class Shop_Form_AjoutProduit extends Zend_Form
{
	/*public function isValid($values)
	{
		
		if($values["categorie"] == '0')
			$values["categorie"]="";		
		
		return parent::isValid($values); // NE SURTOUT PAS OUBLIER CETTE LIGNE
		
	}*/

	public function init(){

		$designation= new Zend_Form_Element_Text('designation');
		$designation->setLabel("Désignation *");
		$designation->setRequired(true);
		$designation->setAttrib('onchange', '');
		$designation->setAttrib('size','73');

		$descriptionBreve= new Application_Form_Element_CKEditor('descriptionBreve');
		$descriptionBreve->setLabel("Description breve *");
		$descriptionBreve->setRequired(true);

		$description= new Application_Form_Element_CKEditor('description');
		$description->setLabel("Description complète *");
		$description->setRequired(true);
		$description->setAttribs(array('cols' => 60, 'rows' => 12));

		$categorie = new Zend_Form_Element_Select('categorie');
		$TableCategorie = new Shop_Model_Categorie();
		$categorie->setAttrib('onchange', 'AffichageSousCategorie()');
		$categorie->addMultiOption('0',"Choisissez la catégorie");
		//$categorie->setRequired(true);
		foreach ($TableCategorie->fetchAll() as $Categorie)
		{
			$categorie->addMultiOption($Categorie->id_categorie,$Categorie->libelle);
		}
		$categorie->setValue('0');
		$categorie->setRegisterInArrayValidator(false);
		$categorie->addValidator('Digits');

		$sousCategorie = new Zend_Form_Element_Select('sousCategorie');
		$sousCategorie->setValue("Choisissez la sous-catégorie");
		$sousCategorie->setRegisterInArrayValidator(false);
		$sousCategorie->addValidator('Digits');

		$prix= new Zend_Form_Element_Text('prix');
		$prix->setLabel("Prix *");
		$prix->setRequired(true);
		$prix->addValidator('Float');


		$quantite= new Zend_Form_Element_Text('quantite');
		$quantite->setLabel("Quantité *");
		$quantite->setRequired(true);
		$quantite->addValidator('Digits');

		$photo1= new Zend_Form_Element_File("photo1");
		$photo1->setLabel("Photo 1");
		$photo1->addValidator('Extension', false, array('jpeg','jpg', 'png'));
		$photo1->addValidator('Count', false, 1);
		$photo1->addValidator('FilesSize', false, 1040000);

		$photo2= new Zend_Form_Element_File("photo2");
		$photo2->setLabel("Photo 2");
		$photo2->addValidator('Extension', false, array('jpeg','jpg', 'png'));
		$photo2->addValidator('FilesSize', false, 1040000);
		$photo2->addValidator('Count', false, 1);

		$photo3= new Zend_Form_Element_File("photo3");
		$photo3->setLabel("Photo 3");
		$photo3->addValidator('Extension', false, array('jpeg','jpg', 'png'));
		$photo3->addValidator('FilesSize', false, 1040000);
		$photo3->addValidator('Count', false, 1);

		$actif = new Zend_Form_Element_Select('actif');
		$actif->addMultiOption("1","Actif");
		$actif->addMultiOption("0","Non actif");

		$submit = new Zend_Form_Element_Submit("Ajouter");
		$submit->setAttrib('id',"boutonAjout");

		$this->setAttrib('enctype', 'multipart/form-data');
		$this->setMethod('POST');

		$this->addElement($designation);
		$this->addElement($descriptionBreve);
		$this->addElement($description);
		$this->addElement($categorie);
		$this->addElement($sousCategorie);
		$this->addElement($prix);
		$this->addElement($quantite);
		$this->addElement($photo1);
		$this->addElement($photo2);
		$this->addElement($photo3);
		$this->addElement($actif);
		$this->addElement($submit);
	}
}