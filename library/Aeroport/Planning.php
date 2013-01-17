<?php
class Aeroport_Planning {
	
	private $_tabJour = array('Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche');
	private $_tabMois = array('Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre');
	
	function __construct(){

	}
	
	function getTranslateDay($numDay){
		return $this->_tabJour[($numDay-1)];
	}
	
	function getTranslateMonth($numMonth){
		return $this->_tabMois[($numMonth-1)];
	}
	
	function getTimestampFirstMonday(){
		$myTimestamp = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
		$myDay = date('N');
		$myDate = date('d');
		$myMonth = date('m');
		$myYear = date('Y');
		
		$firstMonday = $myDate - $myDay + 1;
		
		if($firstMonday < 0){
			$thePrevMonth = $myMonth - 1;
		
			if($thePrevMonth == 0)
				$thePrevMonth = 12;
		
			$firstMonday = date('t', mktime(0, 0, 0, $thePrevMonth, 1, $myYear)) + $firstMonday;
		}
		
		$timestampFirstMonday = mktime(-1, 0, 0, date('m'), $firstMonday, date('Y'));
		return $timestampFirstMonday;
	}
	
	function getTimestampLastSunday($nbSemaine){
		$myTimestamp = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
		$myDay = date('N');
		$myDate = date('d');
		$myDayInMonth = date('t');
		$myMonth = date('m');
		$myYear = date('Y');
		
		$daysRemaining = 7 - $myDay;
		$lastDayWeek = $myDate + $daysRemaining;
		$Sundays4 = (($nbSemaine - 1)*7) + $lastDayWeek;
		
		$timestamp2 = mktime(22, 59, 59, $myMonth, $Sundays4, date('Y'));
		
		if($Sundays4 > $myDayInMonth){
			$dayNextMonth = $Sundays4 - $myDayInMonth; 
			$NextMonth = $myMonth + 1;
			
			$inc = 1;
			$inc2 = $myMonth + 1;
			
			if($inc2 == 13)
				$inc2 = 1;
			
			if($NextMonth == 13)
				$NextMonth = 1;
			
			while($dayNextMonth >= date('t', mktime(0, 0, 0, $inc2, 1, $myYear))){
			
				$dayNextMonth = $dayNextMonth - date('t', mktime(0, 0, 0, $inc2, 1, $myYear));
				$NextMonth = $myMonth + ($inc + 1);
			
				$inc2++;
				$inc++;
				if($NextMonth == 13)
					$NextMonth = 1;
			
				if($inc2 == 13)
					$inc2 = 1;
			}
			
			$timestamp2 = mktime(22, 59, 59, $NextMonth, $dayNextMonth, date('Y'));
			 
			if($NextMonth == 1){
				$timestamp2 = mktime(22, 59, 59, $NextMonth, $dayNextMonth, (date('Y') + 1));
			}
		}
		
		return $timestamp2;
	}
}