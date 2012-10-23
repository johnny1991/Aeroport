<?php
class Periodicite extends Zend_Db_Table_Abstract
{
	protected $_name='periodicite';
	protected $_primary=array('numero_ligne','numero_jour');
	protected $_referenceMap=array(
			'numero_ligne'=>array(
					'columns'=>'numero_ligne',
					'refTableClass'=>'Ligne'),
			'numero_jour'=>array(
					'columns'=>'numero_jour',
					'refTableClass'=>'JourSemaine')
	);
}