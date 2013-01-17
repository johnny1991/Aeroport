(function() {
    var fieldSelection = {
        setSelection: function() {
            var e = (this.jquery) ? this[0] : this, len = this.val().length ;
            var args = arguments[0] || {"start":len, "end":len};
            /* mozilla / dom 3.0 */
            if ('selectionStart' in e) {
                if (args.start != undefined) {
                    e.selectionStart = args.start;
                }
                if (args.end != undefined) {
                    e.selectionEnd = args.end;
                }
                e.focus();
            }
            /* exploder */
            else if (document.selection) {
                e.focus();
                var range = document.selection.createRange();
                if (args.start != undefined) {
                    range.moveStart('character', args.start);
                    range.collapse();
                }
                if (args.end != undefined) {
                    range.moveEnd('character', args.end);
                }
                range.select();
            }
            return this;
        }
    };
    jQuery.each(fieldSelection, function(i) { jQuery.fn[i] = this; });
})();

function MaJSelect(numSelect){
	value = $('select[name=brevet'+numSelect+']').val();
    $('select[name=brevet'+numSelect+']').val(value).attr('selected', 'selected');
	var tabElements = new Array();
    var tabSelected = new Array();
    
    $('form').find('select[name*=brevet]').each(function(indexElts){
	    tabElements[indexElts] = $(this);
	    tabSelected[$(this).attr('name')] = $(this).val();
	 });
    
    var nbSelect = tabElements.length - 1;
    var nextElts = numSelect + 1;
    
    var i;
    var j;
    
    //On boucle sur tous les selects suivant celui sélectionné;
    for(i = nextElts; i <= nbSelect; i++){
       var flagSelected = false;
       var tabValue = new Array();
       var prevElts = i - 1;
       
       //On boucle sur les selects précédents pour récupérer leur valeur puis les insérer dans un tableau;
       for(j = prevElts; j >= 0; j--){
           tabValue[tabElements[j].attr('name')] = tabElements[j].val();
       }
       
       //On insére la valeur du select sélectionné dans le tableau;
       tabValue['brevet'+numSelect+''] = $('select[name=brevet'+numSelect+']').val();

       //On boucle sur les options pour savoir si on affiche ou pas;
       tabElements[i].children('option').each(function(indexOption){
           
           //On teste si la valeur de l'option est dans le tableau; Si oui, on la cache, sinon on l'affiche au cas ou elle était caché
           if(in_array($(this).val(), tabValue)){
               $(this).hide();
               
               if(tabSelected[$(this).parent().attr('name')] == $(this).val()){

                   $(this).removeAttr('selected');
                   
                   $(this).parent().children('option').each(function(){
                      
                       if($(this).css('display') != 'none'){
                           $(this).attr('selected', 'selected');
                           tabSelected[$(this).parent().attr('name')] = $(this).val();
                           return false;
                       }
                   });
               }  
           }
          	else{
          		$(this).show();
          	}
       });
    }
}

function CreationInput(id, originValue, pageName){
	$('.tdLibelleService div').css('display', 'block');
	$('.tdLibelleService form').css('display', 'none');
	$('.'+pageName+'-'+id+' .tdLibelleService form input').val(originValue);
	$('.MessageContentModifier').fadeOut();
	
	$('.'+pageName+'-'+id+' .tdLibelleService div').css('display','none');
	$('.'+pageName+'-'+id+' .tdLibelleService form').css('display','block');
	$('.'+pageName+'-'+id+' .tdLibelleService form input').focus();
	$('.'+pageName+'-'+id+' .tdLibelleService form input').setSelection();
	
	$('.'+pageName+'-'+id+' .tdLibelleService form input').focusout( function() {
	    $('.tdLibelleService div').css('display', 'block');
	    $('.tdLibelleService form').css('display', 'none');
	});
    
}

function AjaxModifierLibelle(id, originValue, pageName, orderBy, page){
    val = $('.'+pageName+'-'+id+' .tdLibelleService form input[name=modifier]').val();
    
    if(val != ''){
        $.ajax({
            type: 'POST',
            url: '/drh/modifier-service/',
            data: 'lib='+val+'&id='+id+'&orderBy='+orderBy+'&page='+page,
            async: false,
            success: function(msg){
               if(msg != 'errors'){
                  // console.log(msg); 
                    window.location = '/drh/consulter-service/'+msg;
                }
                else{
                    $('.tdLibelleService div').css('display', 'block');
                    $('.tdLibelleService form').css('display', 'none');
                    $('.'+pageName+'-'+id+' .tdLibelleService form input').val(originValue);
                    $('.MessageContentModifier').html('<span>Impossible de modifier, ce libellé existe déjà.</span>');
                    $('.MessageContentModifier').fadeIn();
                }
            }
        });
    }
    else{
        $('.tdLibelleService div').css('display', 'block');
        $('.tdLibelleService form').css('display', 'none');
        $('.'+pageName+'-'+id+' .tdLibelleService form input').val(originValue);
    }
    
}

function changeVille($idVille){
	$idPays = $('select[name=pays]').val();
	
	 $.ajax({
         type: 'POST',
         url: '/drh/change-ville/',
         data: 'idPays='+$idPays+'&idVille='+$idVille,
         success: function(msg){
        	 $('select[name=ville]').html(msg);
         }
	 });
}

function removeBrevet($numBrevet){
	$('#brevet-'+$numBrevet).attr('class', 'old');
	
	$('#brevet-'+$numBrevet).nextAll('.divBrevet').each(function(e){
		
		$class = $(this).attr('id').split('-');
		$id = parseInt($class[1]) - 1;

		$(this).attr('id', 'brevet-'+$id);
		$(this).children('.icone_delete_ligne').attr('onclick', 'removeBrevet('+$id+')');
		$(this).children('dt').children('label').text('Le brevet n° '+$id);
		$(this).children('dd').children('select').attr('name', 'brevet'+$id).attr('onchange', 'addBrevet('+$id+')').children('option[value=0]').text('Brevet '+$id);
		$(this).children('dd').children('input').attr('name', 'datebrevet'+$id);
		
	});
	
	$('.old').remove();
	if($('input[name=ajouter-brevet]').length == 0){
		addBoutonBrevet();
	}	
}

function removeAllBrevet(){
	$('.divBrevet').remove();
	$('input[name=ajouter-brevet]').remove();
}

function addBoutonBrevet(){
	$('button[name=ajouter]').before('<input type="button" onclick="addBrevet()" name="ajouter-brevet" value="Ajouter un brevet"/>');
}

function in_array(needle, haystack){
    for(i in haystack){
        if(haystack[i] == needle)
            return true;
    }
    
    return false;
}

function addBrevet($theSelect){
	
	url = document.location.href;
	$idParam = url.split('id/');
	
	($idParam.length != 1) ? $idEmploye = $idParam[1] : $idEmploye = 0;
	
	/*if($idEmploye != 0){
		initBrevet($idEmploye);
	}*/
	
	$value = $('select[name=service]').val();
	
	if($value != 8){
		removeAllBrevet();
	}else{
			
		$listeBrevet = $('#ajouterEmploye').find('.brevetSelect');
		$nbBrevet = $listeBrevet.length;
		
		if($nbBrevet != 0){
			$lastBrevetName = $('.brevetSelect')[($nbBrevet - 1)].name;
			$lastIdBrevets = $lastBrevetName.split('brevet');
			$lastIdBrevet = parseInt($lastIdBrevets[1]) + 1;
		}else{
			$lastIdBrevet = 1;
		}
		
		$html = '<div style="margin-bottom:10px;margin-top:10px;" id="brevet-'+$lastIdBrevet+'" class="divBrevet"><div style="float:left;margin-right:10px;" onclick="removeBrevet('+$lastIdBrevet+')" class="icone_delete_ligne"></div><dt><label>Le brevet n° '+$lastIdBrevet+'</label></dt><dd><select name="brevet'+$lastIdBrevet+'" onchange="addBrevet('+$lastIdBrevet+')" class="brevetSelect"><option value="0">Brevet '+$lastIdBrevet+'</option>';
		
		$.ajax({
			type: 'POST',
			url: '/drh/get-typeavion/',
			async: false,
			success: function(msg){
				$html += msg;
			}
		});
		
		$html += '</select></dd><dd><input type="text" name="datebrevet'+$lastIdBrevet+'"/></dd></div>';
		
		if($value == 8 && $('div#brevet-'+($theSelect + 1)).length == 0){
			
			if($('input[name=ajouter-brevet]').length == 0){
				$('button[name=ajouter]').before($html);
			}else{
				$('input[name=ajouter-brevet]').before($html);
			}
			
			$('input[name=datebrevet'+$lastIdBrevet+']').datepicker({
				dateFormat: "dd-mm-yy",
				timeFormat: "hh:mm:ss t",
				ampm: false
			});
		}
	
	}
	
}

function initBrevet($idEmploye){
	removeAllBrevet();

	$html = '';
	
	$.ajax({
		type: 'POST',
		url: '/drh/get-brevet/',
		data: 'id='+$idEmploye,
		async: false,
		success: function(msg){
			$html += msg;
		}
	});
	
	addBoutonBrevet();
	$('input[name=ajouter-brevet]').before($html);
	
	$listeBrevet = $('#ajouterEmploye').find('.brevetSelect');
	$nbBrevet = $listeBrevet.length;
	
	if($nbBrevet != 0){
		$lastBrevetName = $('.brevetSelect')[($nbBrevet - 1)].name;
		$lastIdBrevets = $lastBrevetName.split('brevet');
		$lastIdBrevet = parseInt($lastIdBrevets[1]) + 1;
	}else{
		$lastIdBrevet = 1;
	}
	
	for($i=1;$i<=$lastIdBrevet;$i++){
		$('input[name=datebrevet'+$i+']').datepicker({
			dateFormat: "dd-mm-yy",
			timeFormat: "hh:mm:ss t",
			ampm: false
		});
	}
}

function verifDatePicker(){
	$('.errorSpan').remove();
	if($('select[name=service]').val() == 8){
		$flag = true;
		
		$('.divBrevet').each(function(e){
			$index = e + 1;
			$eltsSelect = $('select[name=brevet'+$index+']');
			$eltsDate = $('input[name=datebrevet'+$index+']');
			
			$select = $eltsSelect.val();
			$date = $eltsDate.val();
			
			if($select != 0 && $date != ''){
				$('input[name=ajouter]').removeAttr('disabled');
			}else{
				if($select == 0 && $date != ''){
					$eltsSelect.after('<span class="errorSpan">Veuillez remplir ce champs.</span>');
					$('input[name=ajouter]').attr('disabled', 'disabled');
					$flag = false;
				}else{
					if($date == '' && $select != 0){
						$eltsDate.after('<span class="errorSpan">Veuillez remplir ce champs.</span>');
						$('input[name=ajouter]').attr('disabled', 'disabled');
						$flag = false;
					}else{
						$('input[name=ajouter]').removeAttr('disabled');
					}
				}
			}
			
		});
		
		if($flag == true){
			$('form').submit();
		}
	}else{
		$('#ajouterEmploye').submit();
	}

}

function changeDisponibilite($idPilote, $value){
	$.ajax({
		type: 'POST',
		url: '/drh/change-disponibilite',
		data: 'dispo='+$value+'&id='+$idPilote,
		success: function (msg){
			location.reload();
		}
	});
}

function prolongeBrevet($idPilote, $idTypeAvion, $date){
	$.ajax({
		type: 'POST',
		url: '/drh/prolonge-brevet',
		data: 'idPilote='+$idPilote+'&idTypeAvion='+$idTypeAvion+'&date='+$date,
		success: function (msg){
			//console.log(msg);
			location.reload();
		}
	});
}

$(document).ready(function(){
	$('.deleteService').click(function(){
		$classService = $(this).parent().parent().attr('class').split('-');
		$idService = $classService[1];
		
		$.ajax({
			type: 'POST',
			url: '/drh/supprimer-service/',
			data: 'id='+$idService,
			success: function(msg){
				$('.service-'+$idService).fadeOut();
			}
		});
	});
	
	$('.deleteEmploye').click(function(){
		$classEmploye = $(this).parent().parent().attr('class').split('-');
		$idEmploye = $classEmploye[1];
		
		$.ajax({
			type: 'POST',
			url: '/drh/supprimer-employe/',
			data: 'id='+$idEmploye,
			success: function(msg){
				$('.employe-'+$idEmploye).fadeOut();
			}
		});
	});

});

