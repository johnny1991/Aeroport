<?php
class FormulaireVol extends Zend_Form
{
	protected $TablePays;
	protected $TableAeroport;

	public function __construct($TablePays,$TableAeroport, $options = NULL){
		$this->TablePays=$TablePays;
		$this->TableAeroport=$TableAeroport;
		parent::__construct($options);
	}

	public function init(){

		$EPaysDepart= new Zend_Form_Element_Select('paysDepart');
		$pays=$this->TablePays->fetchAll();
		foreach($pays as $pays1){
			$EPaysDepart->addMultiOption($pays1->code_pays,$pays1->nom_pays);
		}
		$EPaysDepart->setLabel('Pays de départ : ');
		$EPaysDepart->setAttrib('onchange', 'RechercheAeroport("depart",this.value)');

		$EAeroportDepart= new Zend_Form_Element_Select('aeroportDepart');
		$EAeroportDepart->setLabel('Aeroport de départ');

		$EPaysArrive= new Zend_Form_Element_Select('paysArrive');
		$pays=$this->TablePays->fetchAll();
		foreach($pays as $pays1){
			$EPaysArrive->addMultiOption($pays1->code_pays,$pays1->nom_pays);
		}
		$EPaysArrive->setLabel('Pays d\'arrivée : ');
		$EPaysArrive->setAttrib('onchange', 'RechercheAeroport("arrivee",this.value)');

		$EAeroportArrive= new Zend_Form_Element_Select('aeroportArrivee');
		$EAeroportArrive->setLabel('Aeroport d\'arrivée');

		$EHeureDepart= new Zend_Form_Element_Text('heureDepart');
		$EHeureDepart->setLabel('Heure de depart : ');

		$EHeureArrivee= new Zend_Form_Element_Text('heureArrivee');
		$EHeureArrivee->setLabel('Heure d\'arrivée : ');

		$EPeriodicite= new Zend_Form_Element_Radio('periodicite');
		$EPeriodicite->addMultiOption(1,'Vol à la carte');
		$EPeriodicite->addMultiOption(2,'Vol périodique');
		$EPeriodicite->setLabel('Périodicité : ');

		$EDateDepart= new Zend_Form_Element_Text('dateDepart');
		$EDateDepart->setLabel('Date de départ : ');

		$jourSemaine=array(
				'L'=>'Lundi',
				'M'=>'mardi',
				'Me'=>'Mercredi',
				'J'=>'Jeudi',
				'V'=>'Vendredi',
				'S'=>'Samedi',
				'D'=>'Dimanche');

		$EJour= new Zend_Form_Element_MultiCheckbox('jour');
		$EJour->addMultiOptions($jourSemaine);
		$EJour->setLabel('Jour de la semaine : ');

		$ESubmit=new Zend_Form_Element_Submit('Ajouter');

		$this->setMethod('post');
		$this->setAction('ajout/insertion/true');
		$this->addElement($EPaysDepart);
		$this->addElement($EAeroportDepart);
		$this->addElement($EPaysArrive);
		$this->addElement($EAeroportArrive);
		$this->addElement($EHeureDepart);
		$this->addElement($EHeureArrivee);
		$this->addElement($EPeriodicite);
		$this->addElement($EDateDepart);
		$this->addElement($EJour);
		$this->addElement($ESubmit);


	}
}