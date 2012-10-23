<?php
class Ville extends Zend_Db_Table_Abstract
{
	protected $_name='ville';
	protected $_primary='code_ville';
	protected $_referenceMap=array(
			'code_pays'=>array(
					'columns'=>'code_pays',
					'refTableClass'=>'Pays')
	);
}