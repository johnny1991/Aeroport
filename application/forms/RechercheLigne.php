<?php
class RechercheLigne extends Zend_Form{

	public function init(){
		$mot = 				new Zend_Form_Element_Text("mot");
		$EPaysOrigine =		new Zend_Form_Element_Select('paysOrigine');
		$EAeroportOrigine =	new Zend_Form_Element_Select('aeroportOrigine');
		$EPaysDepart =		new Zend_Form_Element_Select('paysDepart');
		$EAeroportDepart =	new Zend_Form_Element_Select('aeroportDepart');
		$EPaysArrive =		new Zend_Form_Element_Select('paysArrive');
		$EAeroportArrivee = new Zend_Form_Element_Select('aeroportArrivee');
		$EHeureDepartMin =	new Zend_Form_Element_Text('heureDepartMin');
		$EHeureDepartMax =	new Zend_Form_Element_Text('heureDepartMax');
		$EHeureArriveeMin =	new Zend_Form_Element_Text('heureArriveeMin');
		$EHeureArriveeMax =	new Zend_Form_Element_Text('heureArriveeMax');
		$ETarifMin =			new Zend_Form_Element_Text('tarifMin');
		$ETarifMax =			new Zend_Form_Element_Text('tarifMax');
		$EPeriodicite =		new Zend_Form_Element_Radio('periodicite');
		$EDateDepart = 		new Zend_Form_Element_Text('dateDepart');
		$EDateArrivee =		new Zend_Form_Element_Text('dateArrivee');
		$submit  = new Zend_Form_Element_Submit("Rechercher");
		$Reset = new Zend_Form_Element_Button("Reset");
		$ERepopulateOrigine = new Zend_Form_Element_Hidden('isValidOrigine');
		$ERepopulateDepart = new Zend_Form_Element_Hidden('isValidDepart');
		$ERepopulateArrivee = new Zend_Form_Element_Hidden('isValidArrivee');

		$ERepopulateOrigine->setValue("0");
		$ERepopulateDepart->setValue("0");
		$ERepopulateArrivee->setValue("0");

		$EPaysOrigine->setName("Origine");
		$EPaysDepart->setName("Depart");
		$EPaysArrive->setName("Arrivee");
		$ERepopulateOrigine->setName("PopulateOrigine");
		$ERepopulateDepart->setName("PopulateDepart");
		$ERepopulateArrivee->setName("PopulateArrivee");

		$EHeureDepartMin->setAttrib('size','2');
		$EHeureDepartMax->setAttrib('size','2');
		$EHeureArriveeMin->setAttrib('size','2');
		$EHeureArriveeMax->setAttrib('size','2');
		$ETarifMin->setAttrib('size','2');
		$ETarifMax->setAttrib('size','2');
		$EDateDepart->setAttrib('size','4');
		$EDateArrivee->setAttrib('size','4');

		$EPeriodicite->addDecorator('Label', array('placement' => 'PREPEND'));

		$mot->setLabel("Mot clé :");
		$EPaysOrigine->setLabel("Pays d'origine");
		$EAeroportOrigine->setLabel("Aéroport d'origine :");
		$EPaysDepart->setLabel('Pays de départ');
		$EAeroportDepart->setLabel('Aéroport de départ :');
		$EPaysArrive->setLabel('Pays d\'arrivé');
		$EAeroportArrivee->setLabel('Aéroport d\'arrivé :');
		$EHeureDepartMin->setLabel('Heure de départ minimum');
		$EHeureDepartMax->setLabel('Heure de départ maximun');
		$EHeureArriveeMin->setLabel('Heure d\'arrivée minimum');
		$EHeureArriveeMax->setLabel('Heure d\'arrivée maximum');
		$ETarifMin->setLabel('Tarif minimum');
		$ETarifMax->setLabel('Tarif maximum');
		$EDateDepart->setLabel('Date de départ :');
		$EDateArrivee->setLabel('Date d\'arrivée :');
		$tablePays = new Pays;
		$pays=$tablePays->fetchAll($tablePays->select()->from($tablePays)->order("nom asc"));
		$EPaysOrigine->addMultiOption("0","Choisissez le pays");
		$EPaysOrigine->setAttrib("disable",array("0"));
		$EPaysOrigine->setValue(0);
		$EPaysDepart->addMultiOption("0","Choisissez le pays");
		$EPaysDepart->setAttrib("disable",array("0"));
		$EPaysDepart->setValue(0);
		$EPaysArrive->addMultiOption("0","Choisissez le pays");
		$EPaysArrive->setAttrib("disable",array("0"));
		$EPaysArrive->setValue(0);

		foreach($pays as $pays1)
		{
			$EPaysOrigine->addMultiOption($pays1->code_pays,$pays1->nom);
			$EPaysDepart->addMultiOption($pays1->code_pays,$pays1->nom);
			$EPaysArrive->addMultiOption($pays1->code_pays,$pays1->nom);
		}
		$EPeriodicite->addMultiOptions(array("1"=>"Vol à la carte"));
		$EPeriodicite->addMultiOptions(array("2"=>"Vol périodique"));
		
		$EPaysOrigine->setAttrib('onchange', 'RechercheAeroport("origine",this.value)');
		$EPaysDepart->setAttrib('onchange', 'RechercheAeroport("depart",this.value)');
		$EPaysArrive->setAttrib('onchange', 'RechercheAeroport("arrivee",this.value)');


		$EAeroportOrigine->setRegisterInArrayValidator(false);
		$EAeroportDepart->setRegisterInArrayValidator(false);
		$EAeroportArrivee->setRegisterInArrayValidator(false);

		$this->setMethod("GET");
		$this->addElement($mot);
		$this->addElement($EPaysOrigine);
		$this->addElement($EAeroportOrigine);
		$this->addElement($EPaysDepart);
		$this->addElement($EAeroportDepart);
		$this->addElement($EPaysArrive);
		$this->addElement($EAeroportArrivee);
		$this->addElement($EHeureDepartMin);
		$this->addElement($EHeureDepartMax);
		$this->addElement($EHeureArriveeMin);
		$this->addElement($EHeureArriveeMax);
		$this->addElement($ETarifMin);
		$this->addElement($ETarifMax);
		$this->addElement($EDateDepart);
		$this->addElement($EDateArrivee);
		$this->addElement($ERepopulateOrigine);
		$this->addElement($ERepopulateDepart);
		$this->addElement($ERepopulateArrivee);
		$this->addElement($EPeriodicite);
		$this->addElement($Reset);		
		$this->addElement($submit);
		
		

	}
}