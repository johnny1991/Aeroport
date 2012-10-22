<?php
class Remarque extends Zend_Db_Table_Abstract
{
	protected $_name='remarque';
	protected $_primary='id_remarque';
	protected $referenceMap=array(
			'numero_vol'=>array(
					'columns'=>'numero_vol',
					'refTableClass'=>'Vol')
			);
}