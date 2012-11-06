<?php
class FormulaireLigne extends Zend_Form
{
	protected $TablePays;

	public function __construct($options = NULL){
		$this->TablePays= new Pays();
		parent::__construct($options);
	}

	public function isValid($values)
	{
		if(isset($values["periodicite"]))
		{
			if(!($values["periodicite"]))
				$this->getElement('jours')->setRequired(false);
			else if ($values["periodicite"])
			{
				$this->getElement('dateDepart')->setRequired(false);
				$this->getElement('dateArrivee')->setRequired(false);
			}
		}
		return parent::isValid($values); // NE SURTOUT PAS OUBLIER CETTE LIGNE
	}

	public function init(){

		$FormErrors=new Zend_Form_Decorator_FormErrors();

		$FormErrors->setMarkupListItemStart('<div>');
		$FormErrors->setMarkupListItemEnd('</div>');
		$decoratorsForm = array('FormElements',	array('HtmlTag', array('tag' => 'div','class' =>'formulaireLigne')),'Form');

		$decorators = new Aeroport_Form_DecorateurElement();
		$decorators->GestionClass("Global","Global_label","Global_input","Global_error");
		$decorators=array($decorators);

		$decoratorsTrait = new Aeroport_Form_DecorateurElement();
		$decoratorsTrait->GestionClass("GlobalTrait","Global_label","Global_input","Global_error");
		$decoratorsTrait=array($decoratorsTrait);

		$decoratorsDate1=new Aeroport_Form_DecorateurElement();
		$decoratorsDate1->GestionClass("GlobalDate1","Global_label","Global_input","Global_error","globalDate1");
		$decoratorsDate1=array($decoratorsDate1);

		$decoratorsDate2=new Aeroport_Form_DecorateurElement();
		$decoratorsDate2->GestionClass("GlobalDate2","Global_label","Global_input","Global_error","globalDate2");
		$decoratorsDate2=array($decoratorsDate2);

		$decoratorsJour=new Aeroport_Form_DecorateurElement();
		$decoratorsJour->GestionClass("GlobalJour","Global_label","Global_input","Global_error","globalJour");
		$decoratorsJour=array($decoratorsJour);

		$decoratorsTarif_Effectif=new Aeroport_Form_DecorateurElement();
		$decoratorsTarif_Effectif->GestionClass("GlobalTarif","Global_label","Global_input","Global_error","globalTarif");
		$decoratorsTarif_Effectif=array($decoratorsTarif_Effectif);

		$ENumero =			new Zend_Form_Element_Text("numero");
		$EPaysOrigine =		new Zend_Form_Element_Select('paysOrigine');
		$EAeroportOrigine =	new Zend_Form_Element_Select('aeroportOrigine');
		$EPaysDepart =		new Zend_Form_Element_Select('paysDepart');
		$EAeroportDepart =	new Zend_Form_Element_Select('aeroportDepart');
		$EPaysArrive =		new Zend_Form_Element_Select('paysArrive');
		$EAeroportArrivee = new Zend_Form_Element_Select('aeroportArrivee');
		$EHeureDepart =		new Zend_Form_Element_Text('heureDepart');
		$EHeureArrivee =	new Zend_Form_Element_Text('heureArrivee');
		$ETarif =			new Zend_Form_Element_Text('tarif');
		$EDistance =		new Zend_Form_Element_Text('distance');
		$EPeriodicite =		new Zend_Form_Element_Radio('periodicite');
		$EDateDepart = 		new Zend_Form_Element_Text('dateDepart');
		$EDateArrivee =		new Zend_Form_Element_Text('dateArrivee');
		$ETarif_effectif = 	new Zend_Form_Element_Text('tarif_effectif');
		$EJour = 			new Zend_Form_Element_MultiCheckbox('jours');
		$EAdresseDepart = 	new Zend_Form_Element_Hidden('adresseDepart');
		$EAdresseArrivee = 	new Zend_Form_Element_Hidden('adresseArrivee');
		$ESubmit =			new Zend_Form_Element_Submit('Ajouter');
		$ERepopulateOrigine = new Zend_Form_Element_Hidden('isValidOrigine');
		$ERepopulateDepart = new Zend_Form_Element_Hidden('isValidDepart');
		$ERepopulateArrivee = new Zend_Form_Element_Hidden('isValidArrivee');

		$ENumero->setName("Numero");
		$EPaysOrigine->setName("Origine");
		$EPaysDepart->setName("Depart");
		$EPaysArrive->setName("Arrivee");
		$ERepopulateOrigine->setName("PopulateOrigine");
		$ERepopulateDepart->setName("PopulateDepart");
		$ERepopulateArrivee->setName("PopulateArrivee");

		$ENumero->setLabel('Numéro de vol');
		$EPaysOrigine->setLabel("Pays d'origine");
		$EAeroportOrigine->setLabel("Aéroport d'origine");
		$EPaysDepart->setLabel('Pays de départ');
		$EAeroportDepart->setLabel('Aéroport de départ');
		$EPaysArrive->setLabel('Pays d\'arrivé');
		$EAeroportArrivee->setLabel('Aéroport d\'arrivé');
		$EHeureDepart->setLabel('Heure de départ');
		$EHeureArrivee->setLabel('Heure d\'arrivée');
		$ETarif->setLabel('Tarif (€)');
		$EDistance->setLabel('Distance (km)');
		$EPeriodicite->setLabel('Périodicité');
		$EDateDepart->setLabel('Date de départ');
		$EDateArrivee->setLabel("Date d'arrivée");
		$ETarif_effectif->setLabel('Tarif effectif (€)');
		$EJour->setLabel('Jour de la semaine');

		$ENumero->setDecorators($decorators);
		$EPaysOrigine->setDecorators($decoratorsTrait);
		$EAeroportOrigine->setDecorators($decorators);
		$EPaysDepart->setDecorators($decoratorsTrait);
		$EAeroportDepart->setDecorators($decorators);
		$EPaysArrive->setDecorators($decoratorsTrait);
		$EAeroportArrivee->setDecorators($decorators);
		$EHeureDepart->setDecorators($decoratorsTrait);
		$EHeureArrivee->setDecorators($decorators);
		$ETarif->setDecorators($decoratorsTrait);
		$EDistance->setDecorators($decorators);
		$EPeriodicite->setDecorators($decorators);
		$EDateDepart->setDecorators($decoratorsDate1);
		$EDateArrivee->setDecorators($decoratorsDate2);
		$ETarif_effectif->setDecorators($decoratorsTarif_Effectif);
		$EJour->setDecorators($decoratorsJour);
		$ESubmit->setDecorators($decorators);
		$this->setDecorators($decoratorsForm);

		$EAdresseDepart->removeDecorator('label');
		$EAdresseArrivee->removeDecorator('label');
		$ERepopulateOrigine->removeDecorator('label');
		$ERepopulateDepart->removeDecorator('label');
		$ERepopulateArrivee->removeDecorator('label');

		$ENumero->setRequired(true);
		$EPaysOrigine->setRequired(true);
		$EAeroportOrigine->setRequired(true);
		$EPaysDepart->setRequired(true);
		$EAeroportDepart->setRequired(true);
		$EPaysArrive->setRequired(true);
		$EAeroportArrivee->setRequired(true);
		$EHeureDepart->setRequired(true);
		$EHeureArrivee->setRequired(true);
		$ETarif->setRequired(true);
		$EDistance->setRequired(true);
		$EPeriodicite->setRequired(true);
		$EJour->setRequired(true);
		$EDateDepart->setRequired(true);
		$EDateArrivee->setRequired(true);

		$EPaysOrigine->setAttrib('onchange', 'RechercheAeroport("origine",this.value)');
		$EPaysDepart->setAttrib('onchange', 'RechercheAeroport("depart",this.value)');
		$EAeroportDepart->setAttrib('onclick', 'RechercheAdresse("depart",this.value)');
		$EAeroportArrivee->setAttrib('onclick', 'RechercheAdresse("arrivee",this.value)');
		$EPaysArrive->setAttrib('onchange', 'RechercheAeroport("arrivee",this.value)');
		$EPeriodicite->setAttrib('onclick', 'affichePeriodicite();');
		$ENumero->setAttrib('size','2');
		$EHeureDepart->setAttrib('size','2');
		$EHeureArrivee->setAttrib('size','2');
		$ETarif->setAttrib('size','2');
		$EDistance->setAttrib('size','3');
		$EDateDepart->setAttrib('size','5');
		$EDateArrivee->setAttrib('size','5');
		$ETarif_effectif->setAttrib('size','5');
		$EAeroportOrigine->setRegisterInArrayValidator(false);
		$EAeroportDepart->setRegisterInArrayValidator(false);
		$EAeroportArrivee->setRegisterInArrayValidator(false);

		$pays=$this->TablePays->fetchAll($this->TablePays->select()->from($this->TablePays)->order("nom asc"));
		foreach($pays as $pays1)
		{
			$EPaysOrigine->addMultiOption($pays1->code_pays,$pays1->nom);
			$EPaysDepart->addMultiOption($pays1->code_pays,$pays1->nom);
			$EPaysArrive->addMultiOption($pays1->code_pays,$pays1->nom);
		}

		$EPeriodicite->addMultiOption(0,'Vol à la carte');
		$EPeriodicite->addMultiOption(1,'Vol périodique');
		$EJour->addMultiOptions(array('1'=>'Lundi','2'=>'mardi','3'=>'Mercredi','4'=>'Jeudi','5'=>'Vendredi','6'=>'Samedi','7'=>'Dimanche'));

		$EAeroportOrigine->addFilter('StringToUpper');
		$EAeroportDepart->addFilter('StringToUpper');
		$EAeroportArrivee->addFilter('StringToUpper');

		$ENumero->addValidator('Digits');
		$EPaysOrigine->addValidator('Digits');
		$EAeroportOrigine->addValidator('Alpha');
		$EPaysDepart->addValidator('Digits');
		$EAeroportDepart->addValidator('Alpha');
		$EPaysArrive->addValidator('Digits');
		$EAeroportArrivee->addValidator('Alpha');
		$EHeureDepart->addValidator('Date',null,(array('format' => 'hh:mm:ss')));
		$EHeureArrivee->addValidator('Date',null,(array('format' => 'hh:mm:ss')));
		$ETarif->addValidator('Float');
		$EDistance->addValidator('Digits');
		$EPeriodicite->addValidator('Between',null,(array('min'=>'0','max'=>'1')));
		$EDateDepart->addValidator('Date',null,(array('format' => 'dd-mm-yy')));
		$EDateArrivee->addValidator('Date',null,(array('format' => 'dd-mm-yy')));
		$ETarif_effectif->addValidator('Float');

		$EJour->addValidator('Between',null,(array('min'=>'1','max'=>'7')));

		$EJour->setSeparator(' ');

		$ERepopulateOrigine->setValue("0");
		$ERepopulateDepart->setValue("0");
		$ERepopulateArrivee->setValue("0");

		$this->setMethod('post');

		$this->addElement($ENumero);
		$this->addElement($EPaysOrigine);
		$this->addElement($EAeroportOrigine);
		$this->addElement($EPaysDepart);
		$this->addElement($EAeroportDepart);
		$this->addElement($EPaysArrive);
		$this->addElement($EAeroportArrivee);
		$this->addElement($EHeureDepart);
		$this->addElement($EHeureArrivee);
		$this->addElement($ETarif);
		$this->addElement($EDistance);
		$this->addElement($EPeriodicite);
		$this->addElement($EDateDepart);
		$this->addElement($EDateArrivee);
		$this->addElement($ETarif_effectif);
		$this->addElement($EJour);
		$this->addElement($EAdresseDepart);
		$this->addElement($EAdresseArrivee);
		$this->addElement($ERepopulateOrigine);
		$this->addElement($ERepopulateDepart);
		$this->addElement($ERepopulateArrivee);	

		$this->addDisplayGroup(
				array('Numero','Origine','aeroportOrigine','Depart','aeroportDepart','Arrivee',
						'aeroportArrivee','heureDepart','heureArrivee','tarif','distance','periodicite'	),
				'ligne',
				array('legend' => 'Information sur la ligne')
		);

		$this->addDisplayGroup(
				array('dateDepart','dateArrivee','tarif_effectif'),
				'carte',
				array('legend' => 'Vol à la carte')
		);

		$this->addDisplayGroup(
				array('jours'),
				'periodique',
				array('legend' => 'Ligne périodique')
		);

		$optionGroups=array('FormElements',array('HtmlTag', array('tag' => 'div')),'Fieldset');

		$this->getDisplayGroup('ligne')->removeDecorator('DtDdWrapper');
		$this->getDisplayGroup('ligne')->setDecorators($optionGroups);
		$this->getDisplayGroup('carte')->removeDecorator('DtDdWrapper');
		$this->getDisplayGroup('carte')->setDecorators($optionGroups);
		$this->getDisplayGroup('periodique')->removeDecorator('DtDdWrapper');
		$this->getDisplayGroup('periodique')->setDecorators($optionGroups);

		$this->addElement($ESubmit);
	}
}