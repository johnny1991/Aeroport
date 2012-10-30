<?php
class FormulaireLigne extends Zend_Form
{
	protected $TablePays;

	public function __construct($options = NULL){
		$this->TablePays= new Pays();
		parent::__construct($options);
	}

	public function init(){

				$FormErrors=new Zend_Form_Decorator_FormErrors();
// 						array(
// 								'ignoreSubForms'=>false,
// 								'markupElementLabelEnd'=> '</b>',
// 								'markupElementLabelStart'=> '<b>',
// 								'markupListEnd' => '</div>',
// 								'markupListItemEnd'=>'</span>',
// 								'markupListItemStart'=>'<span>',
// 								'markupListStart'=>'<div>'
// 						));

		// 		$Errors=new Zend_Form_Decorator_Errors(
		// 				array(
		// 						array('closeerror' => 'HtmlTag'),
		// 						array('tag' => 'td', 'closeOnly' => true, 'placement' => Zend_Form_Decorator_Abstract::APPEND))
			
		// 				/*'Description',(array(	'escape' => false,
		// 						'tag' => 'span',
		// 						'class' => 'test',
		// 						'placement' => 'prepend'))
		// 		);
		$FormErrors->setMarkupListItemStart('<div>');
$FormErrors->setMarkupListItemEnd('</div>');
			$decoratorsForm = array(
		 				'FormElements',		array('HtmlTag', array('tag' => 'div','class' =>'formulaire')),
		 				$FormErrors,
		 				'Form'
		 		);

		// 		$decorators = array(
		// 				array('ViewHelper'),

		//                    array('Errors', array('tag' => 'p')),//array('Errors',$Errors/* 	array('tag' =>'div','class'=>'global_errors')*/),
				// 				array('HtmlTag', 	array('tag' =>'div','class'=>'global_input')),
				// 				array('Label', 		array('tag' =>'div','requiredSuffix'=>' *')),
				// 				array(				array('div' => 'HtmlTag'),
						// 						array('class' =>'global'))
				// 		);

				// 		$decoratorsDate1 = array(
						// 				array('ViewHelper'),
						// 				array('Errors',$Errors/* 	array('tag' =>'div','class'=>'global_errors')*/),
						// 				array('HtmlTag', 	array('tag' => 'div','class'=>'global_input')),
						// 				array('Label', 		array('tag' => 'div','requiredSuffix'=>' *')),
						// 				array(				array('div' => 'HtmlTag'),
								// 						array('class' => 'globalDate','id'=>'globalDate1'))
						// 		);

				// 		$decoratorsDate2 = array(
						// 				array('ViewHelper'),
						// 				array('Errors',$Errors/* 	array('tag' =>'div','class'=>'global_errors')*/),
						// 				array('HtmlTag',	array('tag' => 'div','class'=>'global_input')),
						// 				array('Label', 		array('tag' => 'div','requiredSuffix'=>' *')),
						// 				array(				array('div' => 'HtmlTag'),
								// 						array('class' => 'globalDate','id'=>'globalDate2'))
						// 		);

				// 		$decoratorsJour = array(
						// 				array('ViewHelper'),
						// 				array('Errors',$Errors/* 	array('tag' =>'div','class'=>'global_errors')*/),
						// 				array('HtmlTag', 	array('tag' => 'div','class'=>'global_input')),
						// 				array('Label', 		array('tag' => 'div','requiredSuffix'=>' *')),
						// 				array(				array('div' => 'HtmlTag'),
								// 						array('class' => 'globalJour','id'=>'globalJour'))
						// 		);

				// 		$decoratorsSubmit = array(
						// 				array('ViewHelper'),
						// 				array('Errors',$Errors/* 	array('tag' =>'div','class'=>'global_errors')*/),
						// 				array('HtmlTag', 	array('tag' => 'div','class'=>'global_input')),
						// 				array(				array('div' => 'HtmlTag'),
								// 						array('class' => 'global'))
						// 		);

		$decorators = new Aeroport_Form_DecorateurElement();
	    $decorators->GestionClass("Global","Global_label","Global_input","Global_error");
		$decorators=array($decorators);
		
		//$Error=new Zend_Form_Decorator_FormErrors();
		
		$decoratorsDate1=new Aeroport_Form_DecorateurElement();
		$decoratorsDate1->GestionClass("GlobalDate1","Global_label","Global_input","Global_error","globalDate1");
		$decoratorsDate1=array($decoratorsDate1);
		
		$decoratorsDate2=new Aeroport_Form_DecorateurElement();
		$decoratorsDate2->GestionClass("GlobalDate2","Global_label","Global_input","Global_error","globalDate2");
		$decoratorsDate2=array($decoratorsDate2);
		
		$decoratorsJour=new Aeroport_Form_DecorateurElement();
		$decoratorsJour->GestionClass("GlobalJour","Global_label","Global_input","Global_error","globalJour");
		$decoratorsJour=array($decoratorsJour);
		
		$ENumero =			new Zend_Form_Element_Text("numero");
		$EAeroportOrigine =	new Zend_Form_Element_Select('aeroportOrigine');
		$EPaysOrigine =		new Zend_Form_Element_Select('paysOrigine');
		$EPaysDepart =		new Zend_Form_Element_Select('paysDepart');
		$EAeroportDepart =	new Zend_Form_Element_Select('aeroportDepart');
		$EPaysArrive =		new Zend_Form_Element_Select('paysArrive');
		$EAeroportArrive = 	new Zend_Form_Element_Select('aeroportArrivee');
		$EHeureDepart =		new Zend_Form_Element_Text('heureDepart');
		$EHeureArrivee =	new Zend_Form_Element_Text('heureArrivee');
		$EPeriodicite =		new Zend_Form_Element_Radio('periodicite');
		$EDateDepart = 		new Zend_Form_Element_Text('dateDepart');
		$EDateArrivee =		new Zend_Form_Element_Text('dateArrivee');
		$EJour = 			new Zend_Form_Element_MultiCheckbox('jours');
		$ESubmit =			new Zend_Form_Element_Submit('Ajouter');

		$ENumero->setName("Numero");
		$EPaysOrigine->setName("Origine");
		$EPaysDepart->setName("Depart");
		$EPaysArrive->setName("Arrive");

		$ENumero->setLabel('Numero de vol');
		$EPaysOrigine->setLabel("Pays d'origine");
		$EAeroportOrigine->setLabel("Aeroport d'origine");
		$EPaysDepart->setLabel('Pays de départ');
		$EAeroportDepart->setLabel('Aeroport de départ');
		$EPaysArrive->setLabel('Pays d\'arrivé');
		$EAeroportArrive->setLabel('Aeroport d\'arrivée');
		$EHeureDepart->setLabel('Heure de depart');
		$EHeureArrivee->setLabel('Heure d\'arrivée');
		$EPeriodicite->setLabel('Périodicité');
		$EDateDepart->setLabel('Date de départ');
		$EDateArrivee->setLabel("Date de d'arrivée");
		$EJour->setLabel('Jour de la semaine');


		
		$ENumero->setDecorators($decorators);
		$EPaysOrigine->setDecorators($decorators);
		$EAeroportOrigine->setDecorators($decorators);
		$EPaysDepart->setDecorators($decorators);
		$EAeroportDepart->setDecorators($decorators);
		$EPaysArrive->setDecorators($decorators);
		$EAeroportArrive->setDecorators($decorators);
		$EHeureDepart->setDecorators($decorators);
		
		$EHeureArrivee->setDecorators($decorators);
		$EPeriodicite->setDecorators($decorators);
		$EDateDepart->setDecorators($decoratorsDate1);
		$EDateArrivee->setDecorators($decoratorsDate2);
		$EJour->setDecorators($decoratorsJour);
		$ESubmit->setDecorators($decorators);
		$this->setDecorators($decoratorsForm);

		$EPaysOrigine->setRequired(true);
		$EAeroportOrigine->setRequired(true);
		$EPaysDepart->setRequired(true);
		$EAeroportDepart->setRequired(true);
		$EPaysArrive->setRequired(true);
		$EAeroportArrive->setRequired(true);
		$EHeureDepart->setRequired(true);
		$EHeureArrivee->setRequired(true);
		$EPeriodicite->setRequired(true);

		$EPaysOrigine->setAttrib('onchange', 'RechercheAeroport("origine",this.value)');
		$EPaysDepart->setAttrib('onchange', 'RechercheAeroport("depart",this.value)');
		$EPaysArrive->setAttrib('onchange', 'RechercheAeroport("arrivee",this.value)');
		$EPeriodicite->setAttrib('onclick', 'affichePeriodicite(this);');

		$EAeroportOrigine->setRegisterInArrayValidator(false);
		$EAeroportDepart->setRegisterInArrayValidator(false);
		$EAeroportArrive->setRegisterInArrayValidator(false);

		$pays=$this->TablePays->fetchAll($this->TablePays->select()->from($this->TablePays)->order("nom_pays asc"));
		foreach($pays as $pays1){
			$EPaysOrigine->addMultiOption($pays1->code_pays,$pays1->nom_pays);
			$EPaysDepart->addMultiOption($pays1->code_pays,$pays1->nom_pays);
			$EPaysArrive->addMultiOption($pays1->code_pays,$pays1->nom_pays);
		}

		$EPeriodicite->addMultiOption(0,'Vol à la carte');
		$EPeriodicite->addMultiOption(1,'Vol périodique');
		$EJour->addMultiOptions(array('1'=>'Lundi','2'=>'mardi','3'=>'Mercredi','4'=>'Jeudi','5'=>'Vendredi','6'=>'Samedi','7'=>'Dimanche'));

		$EJour->setSeparator(' ');

		$this->setMethod('post');

		$this->addElement($ENumero);
		$this->addElement($EPaysOrigine);
		$this->addElement($EAeroportOrigine);
		$this->addElement($EPaysDepart);
		$this->addElement($EAeroportDepart);
		$this->addElement($EPaysArrive);
		$this->addElement($EAeroportArrive);
		$this->addElement($EHeureDepart);
		$this->addElement($EHeureArrivee);
		$this->addElement($EPeriodicite);
		$this->addElement($EDateDepart);
		$this->addElement($EDateArrivee);
		$this->addElement($EJour);
		$this->addElement($ESubmit);
	}
}