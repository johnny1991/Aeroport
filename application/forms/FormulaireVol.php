<?php
class FormulaireVol extends Zend_Form
{
	protected $TablePays;

	public function __construct($options = NULL){
		$this->TablePays= new Pays();
		parent::__construct($options);
	}

	public function init(){

		/*$decorators= new Aeroport_Form_DecorateurElement();
		 $decorators=array($decorators);*/

		$decorators = array(
				array('ViewHelper', array('class'=>'test')),
				array('Errors', array('tag' =>'div','class'=>'global_errors')),
				array('HtmlTag', array('tag' =>'div','class'=>'global_input')),
				array('Label', array('tag' =>'div','requiredSuffix'=>' *')),
				array(array('div' => 'HtmlTag'), array('class' =>'global'))
		);

		$decoratorsDate1 = array(
				array('ViewHelper', array('class'=>'test')),
				array('Errors', array('tag' => 'div','class'=>'global_errors')),
				array('HtmlTag', array('tag' => 'div','class'=>'global_input')),
				array('Label', array('tag' => 'div','requiredSuffix'=>' *')),
				array(array('div' => 'HtmlTag'), array('class' => 'globalDate','id'=>'globalDate1'))
		);
		
		$decoratorsDate2 = array(
				array('ViewHelper', array('class'=>'test')),
				array('Errors', array('tag' => 'div','class'=>'global_errors')),
				array('HtmlTag', array('tag' => 'div','class'=>'global_input')),
				array('Label', array('tag' => 'div','requiredSuffix'=>' *')),
				array(array('div' => 'HtmlTag'), array('class' => 'globalDate','id'=>'globalDate2'))
		);

		$decoratorsJour = array(
				array('ViewHelper', array('class'=>'test')),
				array('Errors', array('tag' => 'div','class'=>'global_errors')),
				array('HtmlTag', array('tag' => 'div','class'=>'global_input')),
				array('Label', array('tag' => 'div','requiredSuffix'=>' *')),
				array(array('div' => 'HtmlTag'), array('class' => 'globalJour','id'=>'globalJour'))
		);
		
		$decoratorsSubmit = array(
				array('ViewHelper', array('class'=>'test')),
				array('Errors', array('tag' => 'div','class'=>'global_errors')),
				array('HtmlTag', array('tag' => 'div','class'=>'global_input')),
				//array('Label', array('tag' => 'div','requiredSuffix'=>' *')),
				array(array('div' => 'HtmlTag'), array('class' => 'global'))
		);

		$ENumero=new Zend_Form_Element_Text("numero");
		$ENumero->setLabel('Numero de vol');
		$ENumero->setDecorators($decorators);
		
		$EPaysDepart= new Zend_Form_Element_Select('paysDepart');
		$pays=$this->TablePays->fetchAll();
		foreach($pays as $pays1){
			$EPaysDepart->addMultiOption($pays1->code_pays,$pays1->nom_pays);
		}
		$EPaysDepart->setLabel('Pays de départ');
		$EPaysDepart->setAttrib('onchange', 'RechercheAeroport("depart",this.value)');
		$EPaysDepart->setRequired(true);
		$EPaysDepart->setDecorators($decorators);

		$EAeroportDepart= new Zend_Form_Element_Select('aeroportDepart');
		$EAeroportDepart->setLabel('Aeroport de départ');
		$EAeroportDepart->setRequired(true);
		$EAeroportDepart->setRegisterInArrayValidator(false);
		$EAeroportDepart->setDecorators($decorators);

		$EPaysArrive= new Zend_Form_Element_Select('paysArrive');
		$pays=$this->TablePays->fetchAll();
		foreach($pays as $pays1){
			$EPaysArrive->addMultiOption($pays1->code_pays,$pays1->nom_pays);
		}
		$EPaysArrive->setLabel('Pays d\'arrivée : ');
		$EPaysArrive->setAttrib('onchange', 'RechercheAeroport("arrivee",this.value)');
		$EPaysArrive->setRequired(true);
		$EPaysArrive->setDecorators($decorators);

		$EAeroportArrive= new Zend_Form_Element_Select('aeroportArrivee');
		$EAeroportArrive->setLabel('Aeroport d\'arrivée');
		$EAeroportArrive->setRequired(true);
		$EAeroportArrive->setRegisterInArrayValidator(false);
		$EAeroportArrive->setDecorators($decorators);

		$EHeureDepart= new Zend_Form_Element_Text('heureDepart');
		$EHeureDepart->setLabel('Heure de depart : ');
		$EHeureDepart->setRequired(true);
		$EHeureDepart->setDecorators($decorators);

		$EHeureArrivee= new Zend_Form_Element_Text('heureArrivee');
		$EHeureArrivee->setLabel('Heure d\'arrivée : ');
		$EHeureArrivee->setRequired(true);
		$EHeureArrivee->setDecorators($decorators);

		$EPeriodicite= new Zend_Form_Element_Radio('periodicite');
		$EPeriodicite->addMultiOption(0,'Vol à la carte');
		$EPeriodicite->addMultiOption(1,'Vol périodique');
		$EPeriodicite->setLabel('Périodicité : ');
		$EPeriodicite->setRequired(true);
		$EPeriodicite->setAttrib('onclick', 'affichePeriodicite(this);');
		$EPeriodicite->setDecorators($decorators);

		$EDateDepart= new Zend_Form_Element_Text('dateDepart');
		$EDateDepart->setLabel('Date de départ : ');
		$EDateDepart->setDecorators($decoratorsDate1);
		
		$EDateArrivee= new Zend_Form_Element_Text('dateArrivee');
		$EDateArrivee->setLabel("Date de d'arrivée : ");
		$EDateArrivee->setDecorators($decoratorsDate2);
		
		$jourSemaine=array(
				'1'=>'Lundi',
				'2'=>'mardi',
				'3'=>'Mercredi',
				'4'=>'Jeudi',
				'5'=>'Vendredi',
				'6'=>'Samedi',
				'7'=>'Dimanche');

		$EJour= new Zend_Form_Element_MultiCheckbox('jours');
		$EJour->setSeparator(' ');
		$EJour->addMultiOptions($jourSemaine);
		$EJour->setLabel('Jour de la semaine : ');
		$EJour->setDecorators($decoratorsJour);

		$ESubmit=new Zend_Form_Element_Submit('Ajouter');
		$ESubmit->setDecorators($decorators);

		$this->setMethod('post');
		$this->setAction('ajout');
		$this->addElement($ENumero);
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