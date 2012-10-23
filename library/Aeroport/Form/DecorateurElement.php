<?php
class Aeroport_Form_DecorateurElement extends Zend_Form_Decorator_Abstract
{
	// Construction de l'élément label
	public function buildLabel()
	{
		// Recuperer l'element a traiter
		$element=$this->getElement();

		// Recuperer le label
		$label=$element->getLabel();

		// Ajouter une etoile si l'element est obligatoire
		if($element->isRequired())
			$label .=' <div class="obligatoire">*</div>';

		// Ajouter les : de fin au label
		$label.='  : ';

		return $element->getView()->formLabel($element->getname(),$label);
	}

	// Construction de l'élément input
	public function buildInput()
	{
		// Recuperer l'element a traiter
		$element = $this->getElement();
		$helper  = $element->helper;
		return $element->getView()->$helper(
				$element->getName(),
				$element->getValue(),
				$element->getAttribs(),
				$element->options
		);
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
		return '<div class="errors">' .
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
		$label=$this->buildLabel();
		$input=$this->buildInput();
		$errors=$this->buildErrors();
		$description=$this->buildDescription();

		$output=$label.$input.$errors;
		return $content.$output;

	}
}