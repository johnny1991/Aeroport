<?php
class Astreinte extends Zend_Db_Table_Abstract
{
	protected $_name='astreinte';
	protected $_primary=array('id_aeroport','id_pilote');
}