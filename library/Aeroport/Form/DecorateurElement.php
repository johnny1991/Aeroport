<?php
class Aeroport_Form_DecorateurElement extends Zend_Form_Decorator_Abstract
{
	public $ElementTag;
	public $ElementLabel;
	public $ElementInput;
	public $ElementError;
	public $ElementId;

	public function GestionClass($ElementTag,$ElementLabel,$ElementInput,$ElementError,$ElementId = NULL){
		$this->ElementTag=$ElementTag;
		$this->ElementLabel=$ElementLabel;
		$this->ElementInput=$ElementInput;
		$this->ElementError=$ElementError;
		$this->ElementId=$ElementId;
	}

	// Construction de l'élément label
	public function buildLabel()
	{
		// Recuperer l'element a traiter
		$element=$this->getElement();

		// Recuperer le label
		$label=$element->getLabel();

		// Ajouter les : de fin au label
		$label.='  : ';

		// Ajouter une etoile si l'element est obligatoire
		if($element->isRequired())
			$label .='<b>*</b>';



		return '<div class="'.$this->ElementLabel.'">'.$element->getView()->formLabel($element->getname(),$label).'</div>';
	}

	// Construction de l'élément input
	public function buildInput()
	{
		// Recuperer l'element a traiter
		$element = $this->getElement();
		$helper  = $element->helper;
		if($element->getType()=='Zend_Form_Element_Submit')
			$value="Ajouter";
		else
			$value=$element->getValue();

		return '<div class="'.$this->ElementInput.'">'.$element->getView()->$helper(
				$element->getName(),
				$value,
				$element->getAttribs(),
				$element->options
		).'</div>';
	}

	// Construction de la partie errreur
	public function buildErrors()
	{
		// Recuperer l'element a traiter
		$element  = $this->getElement();
		$messages = $element->getMessages();
		if (empty($messages)) {
			return '';
		}
		return '<div class="'.$this->ElementError.'">' .
				$element->getView()->formErrors($messages) . '</div>';
	}

	// Construction de la zone description
	public function buildDescription()
	{
		$deac=$this->getElement()->getDescription();
		if(empty($deac)) return;
		return $deac ;
	}

	// Construction de l'element final (label+input+erreur+description)
	public function render($content)
	{
		$element=$this->getElement();

		$label=$this->buildLabel();
		$input=$this->buildInput();
		$errors=$this->buildErrors();
		$description=$this->buildDescription();
		if($element->getType()!='Zend_Form_Element_Submit')
			$output=$label.$input.$errors;
		else
			$output=$input.$errors;

		if ($this->ElementId!="")
			return '<div class="'.$this->ElementTag.'" id="'.$this->ElementId.'">'.$content.$output.'</div>';
		else
			return '<div class="'.$this->ElementTag.'">'.$content.$output.'</div>';

	}
}