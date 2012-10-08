<?php
class Utilisateur extends Zend_Db_Table_Abstract
{
	protected $_name='utilisateur';
	protected $_primary='login';
}