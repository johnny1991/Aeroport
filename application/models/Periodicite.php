<?php
class PeriodiciteJ extends Zend_Db_Table_Abstract
{
	protected $_name='periodicite_jour_semaine';
	protected $_primary=array('id_periodicite','numero_jour');
	protected $_referenceMap=array(
			'numero_lign'=>array(
					'columns'=>'numero_lign',
					'refTableClass'=>'Ligne'),
			'numero_jour'=>array(
					'columns'=>'numero_jour',
					'refTableClass'=>'JourSemaine')
	);
}