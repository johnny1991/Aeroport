<?php
class Login extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->addElement(
				'text', 'login', array(
						'required' => true,
						'filters'    => array('StringTrim'),
				));

		$this->addElement('password', 'password', array(
				'required' => true,
		));

		$this->addElement('submit', 'submit', array(
				'ignore'   => true,
				'label'    => 'Ok',
		));

	}
}