<?php
class Aeroport_Api{
	
	public static function wrapQuote($string){
		$params = array($string => 'int');
		return (!Aeroport_Fonctions::validParam($params)) ? '\''.$string.'\'' : $string;
	}
	
	public static function is_integer($string){
		$params = array($string => 'int');
		return (Aeroport_Fonctions::validParam($params)) ? intval($string) : $string;
	}
	
	public static function concatParam($string, $charConcat, $quote = false, $delimiter = '-'){
		
		$explode = explode($delimiter, $string);
		$size = count($explode);
		$i = 1;
		$newStr = '';
		
		foreach($explode as $value){
				
			$subParam = explode('=', $value);
			if(count($subParam) != 1){
				$params = array($subParam[1] => 'int');
				$valueParam = self::wrapQuote($subParam[1]);
	
				$newStr .= $subParam[0].' = '.$valueParam;
			}else{
				$params = array($value => 'int');
				$newStr .= ($quote == true && !Aeroport_Fonctions::validParam($params)) ? '\''.$value.'\'' : $value;
			}
	
			if($size != $i){
				$newStr .= $charConcat;
			}
			$i++;
		}
	
		return $newStr;
	}
	
	public static function createSelectQuery($args){
		
		$column = $args['column'];
		$from = $args['from'];
		$join = $args['join'];
		$on = $args['on'];
		$where = $args['where'];
		$limit = $args['limit'];
		$orderdesc = $args['orderdesc'];
		$orderasc = $args['orderasc'];
		$orwhere = $args['orwhere'];
		
		$query = 'SELECT ';
		$flagError = false;
			
		$query .= ($column == NULL) ? ' *' : self::concatParam($column, ', ');	
		$query .= ' FROM '.$from.' ';
			
		if($join != NULL && $on != NULL){
			$joinParam = explode('-', $join);
			$onParam = explode('-', $on);
	
			if(count($joinParam) == count($onParam)){
				foreach($joinParam as $key => $value){
					$query .= ' JOIN '.$value.' ON '.$onParam[$key];
				}
			}else{
				$flagError = true;
			}
		}
			
		$query .= ($where != NULL) ? ' WHERE '.self::concatParam($where, ' AND ') : '';
		$query .= ($orwhere != NULL) ? ' OR ('.self::concatParam($orwhere, ' AND ').')' : '';
		$query .= ($orderdesc != NULL) ? ' ORDER By '.$orderdesc.' DESC' : '';
		$query .= ($orderasc != NULL) ? ' ORDER By '.$orderasc.' ASC' : '';
		$query .= ($limit != NULL) ? ' LIMIT '.self::concatParam($limit, ', ') : '';

		if($flagError){
			$query = 'Erreur de syntaxe !';
		}
		
		return $query;
	}
	
	public static function createArrayQuery($args){
	
		$column = $args['column'];
		$value = $args['values'];

		$query = array();
		$flagError = false;

		if($column != NULL && $value != NULL){
			$columns = explode('-', $column);
			$values = explode('-', $value);
			
			if(is_array($columns) && is_array($values)){
				foreach($columns as $key => $colonne){
					$query[$colonne] = self::is_integer($values[$key]);
				}
			}else{
				$query[$columns] = self::is_integer($values);
			}
			
			
		}else{
			$flagError = true;
		}
		
		if($flagError){
			$query = 'Erreur de syntaxe !';
		}
	
		return $query;
	}
	
	public static function createInsertQuery($args){
		$column = $args['column'];
		$value = $args['values'];
		$from = $args['from'];
		
		$query = 'INSERT INTO '.$from.' ';
		$flagError = false;
		
		if($column != NULL && $value != NULL){
			$query .= '('.self::concatParam($column, ', ').') VALUES ('.self::concatParam($value, ', ', true).')';
		}else{
			$flagError = true;
		}
		
		if($flagError){
			$query = 'Erreur de syntaxe !';
		}
		
		return $query;
	}
	
	public static function createUpdateQuery($args){
		$column = $args['column'];
		$value = $args['values'];
		$from = $args['from'];
		$where = $args['where'];
		
		$query = 'UPDATE '.$from.' SET ';
		$flagError = false;
		
		if($column != NULL && $value != NULL && $where != NULL){
			$columns = explode('-', $column);
			$values = explode('-', $value);
			
			$size = count($columns);
			$i = 1; 
			foreach($columns as $key=>$colonne){
				$query .= $colonne.' = '.self::wrapQuote($values[$key]);
				if($size != $i){
					$query .= ', ';
				}
				$i++;
			}
			
			$query .= ' WHERE '.self::concatParam($where, ' AND ');
			
		}else{
			$flagError = true;
		}
		
		if($flagError){
			$query = 'Erreur de syntaxe !';
		}
		
		return $query;
	}
	
	public static function createDeleteQuery($args){
		$from = $args['from'];
		$where = $args['where'];
		
		if($where != NULL){
			$query = 'DELETE FROM '.$from.' WHERE '.self::concatParam($where, ' AND ');
		}else{
			$query = 'Erreur de syntaxe !';
		}
		
		return $query;
	}
	
}