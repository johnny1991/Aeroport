<?php
class TypeAvion extends Zend_Db_Table_Abstract
{
	protected $_name='type_avion';
	protected $_dependentTables = array('Avion');
	protected $_primary='id_type_avion';
	
}