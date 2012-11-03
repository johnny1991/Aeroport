<?php
class Aeroport_Planning {
	
	private $_tabJour = array('Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche');
	private $_tabMois = array('Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre');
	private $_numDay;
	private $_numMonth;
	
	function __construct($numDay, $numMonth){
		$this->_numDay = $numDay;
		$this->_numMonth = $numMonth;
	}
	
	function getTranslateDay(){
		return $this->_tabJour[($this->_numDay-1)];
	}
	
	function getTranslateMonth(){
		return $this->_tabMois[($this->_numMonth-1)];
	}
	
	function getTimestampFirstMonday(){
		$myTimestamp = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
		$myDay = date('N');
		$myDate = date('d');
		
		$firstMonday = $myDate - $myDay + 1;
		$timestampFirstMonday = mktime(0, 0, 0, date('m'), $firstMonday, date('Y'));
		return $timestampFirstMonday;
	}
	
	function getTimestampLastSunday(){
		$myTimestamp = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
		$myDay = date('N');
		$myDate = date('d');
		$myDayInMonth = date('t');
		$myMonth = date('m');
		
		$daysRemaining = 7 - $myDay;
		$lastDayWeek = $myDate + $daysRemaining;
		$Sundays4 = (3*7) + $lastDayWeek;
		
		$timestamp2 = mktime(0, 0, 0, $myMonth, $Sundays4, date('Y'));
		
		if($Sundays4 > $myDayInMonth){
			$dayNextMonth = $Sundays4 - $myDayInMonth; 
			$NextMonth = $myMonth + 1;
			
			if($NextMonth == 13)
				$NextMonth = 1;
			
			$timestamp2 = mktime(0, 0, 0, $NextMonth, $dayNextMonth, date('Y'));
			 
			if($NextMonth == 1){
				$timestamp2 = mktime(0, 0, 0, $NextMonth, $dayNextMonth, (date('Y') + 1));
			}
		}
		
		return $timestamp2;
	}
}