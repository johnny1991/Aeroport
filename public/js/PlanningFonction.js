function recherchePilote(numeroLigne, dateDepart, idTypeAvion, update){

	$.ajax({
		type: "POST",
		url: "/planning/recherchepilote/",
		data: 'numeroligne='+numeroLigne+'&dateDepart='+dateDepart+'&idTypeAvion='+idTypeAvion+'&update='+update,
		async: false,
		success: function(msg){
			$('select[name=pilote0]').html(msg);
			$('select[name=pilote1]').html(msg);
		}
	});
	
	MaJCoPilote(0);
}

function MaJCoPilote(numSelect){
	$('select[name=pilote0]').show();
	$('select[name=pilote1]').show();
	$('.error-pilote').remove();
	$('input[type=submit]').removeAttr('disabled');

	 value = $('select[name=pilote'+numSelect+']').val()
	    $('select[name=pilote'+numSelect+']').val(value).attr('selected', 'selected');
		var tabElements = new Array();
	    var tabSelected = new Array();
	    
	    $('form').find('select[name*=pilote]').each(function(indexElts){
		    tabElements[indexElts] = $(this);
		    tabSelected[$(this).attr('name')] = $(this).val();
		 });
	    
	    var nbSelect = tabElements.length - 1;
	    var nextElts = numSelect + 1;
	    
	    var i;
	    var j;
	    index = 0;
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
	       tabValue['pilote'+numSelect+''] = $('select[name=pilote'+numSelect+']').val();
	       index = 0;
	       //On boucle sur les options pour savoir si on affiche ou pas;
	       tabElements[i].children('option').each(function(indexOption){
	           index++;
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
	
	if(index == 0){
		$('select[name=pilote0]').hide();
		$('select[name=pilote1]').hide();
		$('div#copilote, div#pilote').after('<span class="error-pilote" style="color:red;">Aucun pilote disponible pour cet avion.</span>');
		$('input[type=submit]').attr('disabled', 'disabled');
	}
	else{
		if(index == 1){
			$('#selectCoPilote').hide();
			$('div#copilote').after('<span class="error-pilote" style="color:red;">Aucun pilote disponible pour cet avion.</span>');
			$('input[type=submit]').attr('disabled', 'disabled');
		}
	}
	
}

function getActionsUrl(){
	url = document.location.href;
	explodeUrl = url.split('actions/');
	return explodeUrl[1];
}

function getActionUrl(){
	url = document.location.href;
	explodeUrl = url.split('/');
	return explodeUrl[4];
}

function in_array(needle, haystack){
    for(i in haystack){
        if(haystack[i] == needle)
            return true;
    }
    
    return false;
}

function debug(tabDebug){
    for(k in tabDebug){
        console.log('key: '+k+' Value: '+tabDebug[k]);
    }
    console.log('fin');
}

function MaJSelect(numSelect){
	value = $('select[name=pilote'+numSelect+']').val();
    $('select[name=pilote'+numSelect+']').val(value).attr('selected', 'selected');
	var tabElements = new Array();
    var tabSelected = new Array();
    
    $('form').find('select[name*=pilote]').each(function(indexElts){
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
       tabValue['pilote'+numSelect+''] = $('select[name=pilote'+numSelect+']').val();
       
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

$(document).ready(function(){
	
	if(getActionUrl() == 'planifier-vol'){
		MaJCoPilote(0);
	}	
	
	if(getActionUrl() == 'planifier-astreinte'){
		MaJSelect(0);
	}
});