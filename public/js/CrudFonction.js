function rechercheVille($idPays){
	
	$.ajax({
		type: 'POST',
		url: '/crud/rechercher-ville/',
		data: 'id='+$idPays,
		success: function(msg){
			$('select[name=ville]').html(msg);
		}
	});
	
}