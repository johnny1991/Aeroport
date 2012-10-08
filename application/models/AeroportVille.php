<?php
class AeroportVille extends Zend_Db_Table_Abstract
{
	protected $_name='aeroport_ville';
	protected $_primary=array('code_ville','id_aeroport');
}