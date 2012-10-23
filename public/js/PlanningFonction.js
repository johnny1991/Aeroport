(function($){

    var methods = {
        init : function(options) {
        	
        	dateToday = new Date();
        	nextDate = new Date();
        	prevDate = new Date();
        	
        	var defaults = {
            		'myYear': dateToday.getFullYear(),		
            		'myMonth': dateToday.getMonth(),		
            		'myDate': dateToday.getDate(),
                    'myWeek': getWeek(dateToday),
                    'myDay': dateToday.getDay(),
            		'minYear' : 2012,
        			'maxYear' : 1 + dateToday.getFullYear()
                };

        	var params = $.extend(defaults, options);
        	
        	selector = this.selector;
        	
        	myYear = params.myYear;
        	myMonth = params.myMonth;
        	myDate = params.myDate;
        	myWeek = params.myWeek;
        	myDay = params.myDay;
        	
        	minYear = params.minYear;
        	maxYear = params.maxYear;
        	
        	currentMonth = dateToday.getMonth();
        	currentYear = dateToday.getFullYear();
        	currentWeek = 0;
        	
        	arrayMonthSelect = new Array('Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre');
        	
        	createHeader();
        	createCalendar();
        	calendar(currentMonth, currentYear);
    		
    		$('.buttonLeft').bind('click', function(){
    			if(!(currentMonth == 0 && currentYear == minYear)){
    				previousMonth(currentMonth);
    				buttonActive('buttonLeft');
    				buttonActive('buttonRight');
    			}
    			else{
    				buttonNoActive('buttonLeft');
    				buttonActive('buttonRight');
    			}	
    		});
    		
    		$('.buttonRight').bind('click', function(){
    			if(!(currentMonth == 11 && currentYear == maxYear)){
    				nextMonth(currentMonth);
    				buttonActive('buttonRight');
    				buttonActive('buttonLeft');
    			}
    			else{
    				buttonNoActive('buttonRight');
    				buttonActive('buttonLeft');
    			}	
    		});
    		
    		$('.buttonReset').bind('click', function(){
    			if(currentMonth != myMonth || currentYear != myYear){
    				currentMonth = myMonth;
    				currentYear = myYear;
    				calendar(currentMonth, currentYear);
    				buttonTestActive();
    				buttonTodayActiveTest();
    				$('select[name=selectMonth]').val(currentMonth);
    				$('select[name=selectYear]').val(currentYear);
    			}
    		});
    		
    		$('select[name=selectMonth], select[name=selectYear]').bind('change', function(){
    			currentYear = $('select[name=selectYear]').val();
    			currentMonth = $('select[name=selectMonth]').val();
    			calendar(currentMonth, currentYear);
    			buttonTestActive();	
    			buttonTodayActiveTest();
    		});
    		
    		function createHeader(){
    			
    			$(selector).append('<table id="headerCalendar"></table>');
    			$('#headerCalendar').append('<tr></tr>');
    			$('#headerCalendar tr').append('<td class="tdButton"></td>');
    			$('#headerCalendar tr td.tdButton').append('<div class="buttonStyle buttonLeft"><img src="/img/fleche-gauche.png" alt="Mois Précédent" title="Mois Précédent"/></div>');
    			$('#headerCalendar tr td.tdButton').append('<div class="buttonStyle buttonRight"><img src="/img/fleche-droite.png" alt="Mois Suivant" title="Mois Suivant"/></div>');
    			$('#headerCalendar tr td.tdButton').append('<div class="buttonReset buttonTodayNoActive"></div>');
    			$('#headerCalendar tr td.tdButton').after('<td class="tdTitle"><div id="titleCalendar"><b></b></div></td>');
    			$('#headerCalendar tr td.tdTitle').after('<td class="tdFormCalendar"></td>');
    			$('#headerCalendar tr td.tdFormCalendar').append('<div class="formCalendar"></div>');
    			$('#headerCalendar div.formCalendar').append('<form method="post" action=""><label>Mois:</label><select name="selectMonth">');
									
				for(m=0;m<=11;m++){
					selected = (m == myMonth) ? 'selected="selected"' : '';
					$('select[name=selectMonth]').append('<option '+selected+' value="'+m+'">'+arrayMonthSelect[m]+'</option>');
				}
									
				$('#headerCalendar div.formCalendar form').append('</select><label>Année:</label><select name="selectYear">');				
								
									
				for(y=minYear;y<=maxYear;y++){
					selected = (y == myYear) ? 'selected="selected"' : '';
					$('select[name=selectYear]').append('<option '+selected+' value="'+y+'">'+y+'</option>');
				}
									
				$('#headerCalendar div.formCalendar form').append('</select></form>');					
    		}
    		
    		function createCalendar(){
    			$(selector).append('<table id="tableCalendar"></table>');
    			$('#tableCalendar').append('<thead></thead>');
    			$('#tableCalendar thead').append('<tr></tr>');
    			$('#tableCalendar thead tr').append('<th class="tdWeek">Semaine</th><th>Lundi</th><th>Mardi</th><th>Mercredi</th><th>Jeudi</th><th>Vendredi</th><th>Samedi</th><th>Dimanche</th>');
    			$('#tableCalendar thead').after('<tbody></tbody>');
    		}
        	
        	function valueSelect(valueMonth, valueYear){
        		$('select[name=selectMonth]').val(valueMonth);
        		$('select[name=selectYear]').val(valueYear);
        	}
        	
        	function buttonNoActive(classButton){
        		$('.'+classButton).removeClass('buttonStyle');
        		$('.'+classButton).addClass('buttonStyleNoActive');
        	}
        	
        	function buttonActive(classButton){
        		$('.'+classButton).removeClass('buttonStyleNoActive');
        		$('.'+classButton).addClass('buttonStyle');
        	}
        	
        	function buttonTestActive(){
        		if(!(currentMonth == 11 && currentYear == maxYear)){
        			if(!(currentMonth == 0 && currentYear == minYear)){
        				buttonActive('buttonRight');
        				buttonActive('buttonLeft');
        			}
        			else{
        				buttonActive('buttonRight');
        				buttonNoActive('buttonLeft');
        			}
        		}
        		else{
        			buttonNoActive('buttonRight');
        			buttonActive('buttonLeft');
        		}	
        	}
        	
        	function buttonTodayActiveTest(){
        		if((currentMonth != myMonth || currentYear != myYear)){
        			$('.buttonReset').removeClass('buttonTodayNoActive');
        			$('.buttonReset').addClass('buttonToday');
        		}
        		else{
        			$('.buttonReset').removeClass('buttonToday');
        			$('.buttonReset').addClass('buttonTodayNoActive');
        		}
        	}
        	
        	function previousMonth(month){
        		currentMonth--;

        		if(currentMonth == -1){
        			currentMonth = 11;
        			currentYear--;
        		}
        		
        		if(currentYear < minYear)
        			currentYear = minYear;
        		
        		calendar(currentMonth, currentYear);
        		valueSelect(currentMonth, currentYear);
        		buttonTodayActiveTest();
        	}
        	
        	function nextMonth(month){
        		currentMonth++;

        		if(currentMonth == 12){
        			currentMonth = 0;
        			currentYear++;
        		}
        		
        		if(currentYear > maxYear)
        			currentYear = maxYear;
        		
        		calendar(currentMonth, currentYear);
        		valueSelect(currentMonth, currentYear);
        		buttonTodayActiveTest();
        	}
        	
        	function convert(needle, array){
        		return array[needle];
        	}
        	
        	function isDay(theDay, theMonth, theYear){
        		if(theYear == myYear){
        			if(theMonth == myMonth){
        				if(theDay == myDate)
        					return true;
        				else
        					return false;
        			}
        			else
        				return false;
        		}
        		else
        			return false;
        	}
        	
        	function getWeek(theDate){
        		var checkDate = new Date(theDate.getTime());
        		// Find Thursday of this week starting on Monday
        		checkDate.setDate(checkDate.getDate() + 4 - (checkDate.getDay() || 7));
        		var time = checkDate.getTime();
        		checkDate.setMonth(0); // Compare with Jan 1
        		checkDate.setDate(1);
        		
        		return Math.floor(Math.round((time - checkDate) / 86400000) / 7) + 1;
        	}
        	
        	function colorWeek(theWeek){
        		
        		weekFour = myWeek + 3;
        		
        		if(currentYear == myYear){
        			if(currentMonth == myMonth){
        				if(currentMonth == 11){
        					if(theWeek >= myWeek && theWeek <= weekFour){
        						$('.date-'+theWeek+'-'+currentMonth+'-'+currentYear).addClass('trWeek');
        					}
        					else{
        						
        						dateTestMaxWeek = new Date();
        						dateTestMaxWeek.setYear(myYear);
        						dateTestMaxWeek.setMonth(11);
        						dateTestMaxWeek.setDate(31);
        						
        						maxWeek = getWeek(dateTestMaxWeek);
        						
        						if(maxWeek == 1){
        							maxWeek = 52;
        						}
        						
        						if(weekFour > maxWeek){
        							maxWeek = weekFour - maxWeek;
        						
        							if(theWeek <= maxWeek){
        								$('.date-'+theWeek+'-'+currentMonth+'-'+currentYear).addClass('trWeek');
        							}
        						}
        					}
        					
        				}
        				else{
        					if(theWeek >= myWeek && theWeek <= weekFour){
        						$('.date-'+theWeek+'-'+currentMonth+'-'+currentYear).addClass('trWeek');
        					}
        				}
        			}
        			else{
        				if(currentMonth == myMonth+1){
        					if(theWeek >= myWeek && theWeek <= weekFour){
        						$('.date-'+theWeek+'-'+currentMonth+'-'+currentYear).addClass('trWeek');
        					}
        				}
        				else{
        					if(currentMonth == myMonth-1){
        						if(theWeek == myWeek){
        							$('.date-'+theWeek+'-'+currentMonth+'-'+currentYear).addClass('trWeek');
        						}
        					}
        				}
        			}
        		}
        		else{
        			if(currentYear == myYear+1){
        				if(currentMonth == 0){
        					dateTestMaxWeek = new Date();
        					dateTestMaxWeek.setYear(myYear);
        					dateTestMaxWeek.setMonth(11);
        					dateTestMaxWeek.setDate(31);
        					
        					maxWeek = getWeek(dateTestMaxWeek);
        					
        					if(maxWeek == 1){
        						maxWeek = 52;
        					}
        					
        					if(weekFour > maxWeek){
        						maxWeek = weekFour - maxWeek;
        					
        						if(theWeek <= maxWeek){
        							$('.date-'+theWeek+'-'+currentMonth+'-'+currentYear).addClass('trWeek');
        							
        						}
        					}
        				}
        			}
        			else{
        				if(currentYear == myYear-1){
        					if(currentMonth == 11){
        						if(theWeek == myWeek){
        							$('.date-'+theWeek+'-'+currentMonth+'-'+currentYear).addClass('trWeek');
        						}
        					}
        				}
        			}
        		}		
        	}
        	
        	function lastSunday(){
        		theDay = myDay;
        		dayMonth = arrayDayPerMonth[myMonth];
        		
        		if(leapYear(myYear)){
        			if(dayMonth == 28){
        				dayMonth = 29;
        			}
        		}
        		
        		if(theDay == 0)
        			theDay = 7;
        		
        		daysRemaining = 7 - theDay;
        		lastDayWeek = myDate + daysRemaining;
        		Sundays4 = (3*7) + lastDayWeek;		
        		
        		timestamp2 = new Date();
        		timestamp2.setDate(Sundays4);
        		timestamp2.setMonth(myMonth);
        		timestamp2.setYear(myYear);
        		
        		if(Sundays4 > dayMonth){
        			dayNextMonth = Sundays4 - dayMonth;
        			timestamp2.setDate(dayNextMonth);
        			
        			NextMonth = myMonth + 1;
        			timestamp2.setMonth(NextMonth);
        			
        			if(NextMonth == 0){
        				timestamp2.setYear(myYear + 1);
        			}
        		}
        		
        		timestamp2Time = timestamp2.getTime();
        		
        		return timestamp2Time;
        	}
        	
        	function linkDay(date){
        		
        		formatDate = date.getFullYear()+'-'+(date.getMonth()+1)+'-'+date.getDate();
        		
	      		if(lastSunday() >= date.getTime()){
	        		$('#tableCalendar tbody tr td:last-child span').wrap('<a href="/planning/listevol/date/'+date.getTime()+'"><div style="width:100%;height:80px;"></div></a>');
	        	}	
        		
        	}
        	
        	function addAttributWeek(theWeek, theYear){
        		$('#tableCalendar tbody tr:last-child').addClass('date-'+theWeek+'-'+currentMonth+'-'+theYear);
        	}
        	
        	function leapYear(theYear){
        		if((theYear % 4) == 0){
        			if((theYear % 100) == 0){
        				if((theYeat % 400) == 0){
        					return true;
        				}
        				else{
        					return false;
        				}
        			}
        			else{
        				return true;
        			}
        		}
        		else{
        			return false;
        		}
        	}
        	
        	function calendar(indexMonth, indexYear){
        		
        		$('#tableCalendar tbody').empty();
        		
        		date = new Date();
        		dateJan = new Date();
        		
        		arrayDay = {'Sun':'Dimanche', 'Mon':'Lundi', 'Tue':'Mardi', 'Wed':'Mercredi', 'Thu':'Jeudi', 'Fri':'Vendredi', 'Sat':'Samedi'};
        		arrayMonth = {'Jan':'Janvier', 'Feb':'Février', 'Mar':'Mars', 'Apr':'Avril', 'May':'Mai', 'Jun':'Juin', 'Jul':'Juillet', 'Aug':'Août', 'Sep':'Septembre', 'Oct':'Octobre', 'Nov':'Novembre', 'Dec':'Décembre'};
        		arrayMonthEng = new Array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
        		arrayDayPerMonth = new Array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
        	
        		date.setFullYear(indexYear);
        		date.setMonth(indexMonth);
        		
        		titleDate = date.toDateString(Math.round(date.getTime()/1000));
        		titleExplode = titleDate.split(' ');
        		titleMonth = convert(titleExplode[1], arrayMonth);
        		titleYear = titleExplode[3];
        		
        		$('#titleCalendar b').text(titleMonth +' '+ titleYear);
        		
        		$('#tableCalendar tbody').append('<tr>');
        		
        		for(i=1; i<=31; i++){

        			if(date.getDay() == 0){	
        				$('#tableCalendar tbody').append('<tr>');
        			}
        			
        			date.setDate(i);
        			dateString = date.toDateString(Math.round(date.getTime()/1000));
        			dateExplode = dateString.split(' ');
        			
        			dateNumberDay = dateExplode[2];
        			currentDay = dateNumberDay;
        			
        			if(dateExplode[1] == arrayMonthEng[indexMonth]){
        				
        				if(date.getDate() == 1 || date.getDay() == 1){
        					
        					currentWeek = getWeek(date);
        				
        					$('#tableCalendar tbody tr:last-child').append('<td class="tdWeek">'+currentWeek+'</td>');
        				}
        				
        				if(date.getDate() == 1){
        					firstDay = date.getDay();
        					
        					if(firstDay == 0)
        						firstDay = 7;
        					
        					for(j=firstDay-2;j>=0;j--){
        						
        						prevMonth = currentMonth - 1;
        						if(prevMonth == -1){
        							prevMonth = 11;
        						}
        						
        						totalDays = arrayDayPerMonth[prevMonth];
        						if(totalDays == 28){
        							if(leapYear(currentYear)){
        								totalDays = 29;
        							}
        						}
        						
        						prevDay = totalDays - j;
        						prevYear = currentYear;
        						
        						if(prevMonth == 11)
        							prevYear = currentYear - 1;
        						
        						prevDate.setYear(prevYear);
        						prevDate.setMonth(prevMonth);
        						prevDate.setDate(prevDay);
        						
        						$('#tableCalendar tbody tr:last-child').append('<td class="prevDay"><span>'+prevDay+'</span></td></a>');
        						linkDay(prevDate);
        					}
        					
        				}
        				
        				classToday = '';
        				
        				if(isDay(dateNumberDay, currentMonth, currentYear)){
        					dateNumberDay = '<b>'+dateNumberDay+'</b>';
        					myWeek = currentWeek;
        					classToday = 'class="tdToday"';
        				}
        				
        				addAttributWeek(currentWeek, currentYear);
        				colorWeek(currentWeek);
        				
        				$('#tableCalendar tbody tr:last-child').append('<td '+classToday+'><span>'+ dateNumberDay +'</span></td>');
        				
        				linkDay(date);
        			}
        			else{
        				lastDay = date.getDay() - 1;
        				if(lastDay == -1)
        					lastDay = 6;

        				break;
        			}
        			
        			if(i == 31)
        				lastDay = date.getDay();
        				
        			if(date.getDay() == 0)
        				$('#tableCalendar tbody').append('</tr>');
        		}
        		
        		if(lastDay == 0)
        			lastDay = 7;
        		
        		for(j=1;j<=7-lastDay;j++){
        			
        			theNextYear = currentYear;
        			theNextMonth = currentMonth + 1;
        			
        			if(currentMonth == 11){
        				theNextYear = currentYear + 1;
        				theNextMonth = 0;
        			}
        			
        			nextDate.setYear(theNextYear);
        			nextDate.setMonth(theNextMonth);
        			nextDate.setDate(j);
        			
        			$('#tableCalendar tbody tr:last-child').append('<td class="prevDay"><span>'+j+'</span></td>');
        			linkDay(nextDate);
        		}

        		$('#tableCalendar tbody').append('</tr>');
        		currentWeek = 0;
        		
        	}
        
        }
 
    };

    $.fn.calendar = function (methodOrOptions){
        if(methods[methodOrOptions]){
            return methods[ methodOrOptions ].apply(this, Array.prototype.slice.call( arguments, 1 ));
        } 
        else if (typeof methodOrOptions === 'object' || ! methodOrOptions){
            return methods.init.apply( this, arguments );
        } 
        else{
            $.error( 'Method ' +  method + ' does not exist on jQuery.tooltip' );
        }    
    };
})( jQuery );