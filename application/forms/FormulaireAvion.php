<?php
class FormulaireAvion extends Zend_Form
{
	public function init(){

		$FormErrors=new Zend_Form_Decorator_FormErrors();
		
		$FormErrors->setMarkupListItemStart('<div>');
		$FormErrors->setMarkupListItemEnd('</div>');
		$decoratorsForm = array('FormElements',	array('HtmlTag', array('tag' => 'div','class' =>'formulaireLigne')),'Form');
		
		$decorators = new Aeroport_Form_DecorateurElement();
		$decorators->GestionClass("Global","Global_label","Global_input","Global_error");
		$decorators = array($decorators);

		$EType = new Zend_Form_Element_Select('type');
		$EType->setLabel('Type de l\'avion');
		$TableTypeAvion = new TypeAvion;
		$TypeAvions = $TableTypeAvion->fetchAll();
		$EType->addMultiOption(0,'Choisir');
		foreach ($TypeAvions as $TypeAvion)
			$EType->addMultiOption($TypeAvion->id_type_avion,$TypeAvion->libelle);
		$EType->addValidator('Digits');
		$EType->setDecorators($decorators);
		$EType->setRequired(true);

		$EPlaces = new Zend_Form_Element_Text('places');
		$EPlaces->addValidator('Digits');
		$EPlaces->setLabel('Nombre de places');
		$EPlaces->setDecorators($decorators);
		$EPlaces->setAttrib('size','2');
		$EPlaces->setRequired(true);
		
		$EHeure = new Zend_Form_Element_Text('heure');
		$EHeure->addValidator('Digits');
		$EHeure->setLabel('Total d\'heure de vol');
		$EHeure->setDecorators($decorators);
		$EHeure->setAttrib('size','2');
		$EHeure->setRequired(true);
		
		$ERevision = new Zend_Form_Element_Text('revision');
		$ERevision->addValidator('Digits');
		$ERevision->setLabel('Nombre d\'heure avant la grande révision');
		$ERevision->setDecorators($decorators);
		$ERevision->setAttrib('size','2');
		$ERevision->setRequired(true);
		
		$EDisponibilite = new Zend_Form_Element_Radio('disponibilite');
		$EDisponibilite->setLabel('Disponibilité de l\'avion');
		$EDisponibilite->addValidator('Digits');
		$EDisponibilite->addMultiOption(1,'Disponible');
		$EDisponibilite->addMultiOption(2,'En maintenance');
		$EDisponibilite->setDecorators($decorators);
		$EDisponibilite->setRequired(true);
		
		$ESubmit = new Zend_Form_Element_Submit('Ajouter');
		$ESubmit->setDecorators($decorators);

		$this->setMethod('post');
		$this->addElement($EType);
		$this->addElement($EPlaces);
		$this->addElement($EHeure);
		$this->addElement($ERevision);
		$this->addElement($EDisponibilite);
		$this->addElement($ESubmit);
		$this->setDecorators($decoratorsForm);
		

	}
}