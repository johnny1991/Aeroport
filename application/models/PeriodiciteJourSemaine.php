<?php
class PeriodiciteJourSemaine extends Zend_Db_Table_Abstract
{
	protected $_name='periodicite_jour_semaine';
	protected $_primary=array('id_periodicite','numero_jour');
}