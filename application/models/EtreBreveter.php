<?php
class EtreBreveter extends Zend_Db_Table_Abstract
{
	protected $_name='etre_breveter';
	protected $_primary=array('id_pilote','id_type_avion');
}