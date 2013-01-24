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
            url: '/logistique/modifier-remarque/',
            data: 'lib='+val+'&id='+id+'&orderBy='+orderBy+'&page='+page,
            async: false,
            success: function(msg){
               if(msg != 'errors'){
                  // console.log(msg); 
                    window.location = '/logistique/consulter-remarque/'+msg;
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

function traiterAll($numeroLigne, $idVol){
	$.ajax({
		type: 'POST',
		url: '/logistique/traiter-all/',
		data: 'numeroLigne='+$numeroLigne+'&idVol='+$idVol,
		success: function(msg){
			window.location = '/logistique/fiche-vol/ligne/'+$numeroLigne+'/vol/'+$idVol;
		}
	});
}

function traiterCategorie($idType, $numeroLigne, $idVol){
	$.ajax({
		type: 'POST',
		url: '/logistique/traiter-type/',
		data: 'numeroLigne='+$numeroLigne+'&idVol='+$idVol+'&idType='+$idType,
		success: function(msg){
			window.location = '/logistique/fiche-vol/ligne/'+$numeroLigne+'/vol/'+$idVol;
		}
	});
}

function traiterRemarque($idRemarque, $numeroLigne, $idVol){
	$.ajax({
		type: 'POST',
		url: '/logistique/traiter-remarque/',
		data: 'numeroLigne='+$numeroLigne+'&idVol='+$idVol+'&idRemarque='+$idRemarque,
		success: function(msg){
			window.location = '/logistique/fiche-vol/ligne/'+$numeroLigne+'/vol/'+$idVol;
		}
	});
}

$(document).ready(function(){
	$('.deleteRemarque').click(function(){
		$classRemarque = $(this).parent().parent().attr('class').split('-');
		$idRemarque = $classRemarque[1];
		
		$.ajax({
			type: 'POST',
			url: '/logistique/supprimer-remarque/',
			data: 'id='+$idRemarque,
			success: function(msg){
				$('.remarque-'+$idRemarque).fadeOut();
			}
		});
	});
	
});

