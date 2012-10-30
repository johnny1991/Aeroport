function initialize(){
	RechercheAeroport("depart",250);
	RechercheAeroport("arrivee",250);
	RechercheAeroport("origine",250);
}

function RechercheAeroport(provenance,pays)
{
	$.ajax(
			{
				type: "POST",
				url: "/vol/rechercher-aeroport",
				data: 'pays='+pays,
				success: function(msg)
				{
					if(provenance=='depart')
					{
						$('#aeroportDepart').empty();
						$('#aeroportDepart').append(msg);	
					}
					else if(provenance=='arrivee')
					{
						$('#aeroportArrivee').empty();
						$('#aeroportArrivee').append(msg);	
					}
					else if(provenance=='origine')
					{
						$('#aeroportOrigine').empty();
						$('#aeroportOrigine').append(msg);	
					}
				}
			}
	);
}


$(function(){
	//$('#basic_example_3').datetimepicker({
	$("input[name=dateDepart]").datepicker({
		dateFormat: "yy-mm-dd",
		timeFormat: "hh:mm:ss t",
		ampm: false
	});

	$("input[name=dateArrivee]").datepicker({
		dateFormat: "yy-mm-dd",
		timeFormat: "hh:mm:ss t",
		ampm: false
	});

	$("input[name=heureDepart]").timepicker({
		hourGrid: 4,
		minuteGrid: 10,
		timeFormat: "hh:mm:ss t",
	});

	$("input[name=heureArrivee]").timepicker({
		hourGrid: 4,
		minuteGrid: 10,
		timeFormat: "hh:mm:ss t",
	});
});

function affichePeriodicite(element){
	if(element.value==1){
		console.log("passe1");
		document.getElementById("globalJour").style.display = 'block';
		console.log("passe2");
		document.getElementById("globalDate1").style.display = 'none';
		console.log("passe3");
		document.getElementById("globalDate2").style.display = 'none';
		console.log("passe4");
	}
	else
	{
		console.log("passe5");
		document.getElementById("globalJour").style.display = 'none';
		console.log("passe6");
		document.getElementById("globalDate1").style.display = 'block';
		console.log("passe7");
		document.getElementById("globalDate2").style.display = 'block';
		console.log("passe8");
	}
}
