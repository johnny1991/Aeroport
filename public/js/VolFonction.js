function initialisation(){
	try{	
		initializeMap();
		affichePeriodicite();
	}
	finally{
		initializeAeroport();
	}
}

function initializeAeroport()
{
	if(document.getElementById('aeroportOrigine').value=="")
		RechercheAeroport("origine",document.getElementById('Origine').value);
	if(document.getElementById('aeroportDepart').value=="")
		RechercheAeroport("depart",document.getElementById('Depart').value);
	else
		RechercheAdresse("depart",document.getElementById('aeroportDepart').value);
	if(document.getElementById('aeroportArrivee').value=="")
		RechercheAeroport("arrivee",document.getElementById('Arrivee').value);
	else
		RechercheAdresse("arrivee",document.getElementById('aeroportArrivee').value);
	try{ showAdresse(); } catch(err){}
}

function RechercheAeroport(provenance,pays)
{
	var isValid;
	if(provenance=='depart')
		isValid=document.getElementById('PopulateDepart').value;
	else if(provenance=='arrivee')
		isValid=document.getElementById('PopulateArrivee').value;
	else if(provenance=='origine')
		isValid=document.getElementById('PopulateOrigine').value;

	$.ajax(
			{
				type: "POST",
				url: "/vol/rechercher-aeroport",
				data: 'pays='+pays+'&isValid='+isValid,
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


jQuery(function(){
	//$('#basic_example_3').datetimepicker({
	jQuery("input[name=dateDepart]").datepicker({
		dateFormat: "dd-mm-yy",
		timeFormat: "hh:mm:ss t",
		ampm: false
	});

	jQuery("input[name=dateArrivee]").datepicker({
		dateFormat: "dd-mm-yy",
		timeFormat: "hh:mm:ss t",
		ampm: false
	});

	jQuery("input[name=heureDepart]").timepicker({
		hourGrid: 4,
		minuteGrid: 10,
		timeFormat: "hh:mm:ss t",
	});

	jQuery("input[name=heureArrivee]").timepicker({
		hourGrid: 4,
		minuteGrid: 10,
		timeFormat: "hh:mm:ss t",
	});

	jQuery("input[name=heureDepartMin]").timepicker({
		hourGrid: 4,
		minuteGrid: 10,
		timeFormat: "hh:mm t",
	});

	jQuery("input[name=heureDepartMax]").timepicker({
		hourGrid: 4,
		minuteGrid: 10,
		timeFormat: "hh:mm t",
	});

	jQuery("input[name=heureArriveeMin]").timepicker({
		hourGrid: 4,
		minuteGrid: 10,
		timeFormat: "hh:mm t",
	});

	jQuery("input[name=heureArriveeMax]").timepicker({
		hourGrid: 4,
		minuteGrid: 10,
		timeFormat: "hh:mm t",
	});

});

function affichePeriodicite(){
	if(document.getElementById('periodicite-1').checked==true)
	{
		document.getElementById("fieldset-periodique").style.display = 'block';
		document.getElementById("fieldset-carte").style.display = 'none';
	}
	else if (document.getElementById('periodicite-0').checked==true)
	{
		document.getElementById("fieldset-periodique").style.display = 'none';
		document.getElementById("fieldset-carte").style.display = 'block';
	}
}

function RechercheAdresse(provenance,id_aeroport)
{
	$.ajax(
			{
				type: "POST",
				url: "/vol/rechercher-adresse",
				data: 'id_aeroport='+id_aeroport,
				success: function(msg)
				{ 
					if(provenance=='depart')
					{					
						$('#adresseDepart').attr('value',msg); 
					}
					else if(provenance=='arrivee')
					{
						$('#adresseArrivee').attr('value',msg); 

					}
				}
			}
	);
	setTimeout(showAdresse,1000);
}

var geocoder;
var map;
var markers;
var bounds;
var path;
var traceParcours;

function initializeMap() {
	geocoder = new google.maps.Geocoder();
	var mapOptions = {
			zoom: 5,
			center: new google.maps.LatLng(48.8735087, 2.2958688999999595),
			mapTypeControl:false,
			mapTypeId: google.maps.MapTypeId.ROADMAP
	};
	map = new google.maps.Map(document.getElementById("Map"), mapOptions);
	path = new google.maps.MVCArray();
	markers=new google.maps.MVCArray();
	bounds = new google.maps.LatLngBounds();
	traceParcours = new google.maps.Polyline({
		path: path,//chemin du tracé
		strokeColor: "#ff9305",//couleur du tracé
		strokeOpacity: 1.0,//opacité du tracé
		strokeWeight: 2//grosseur du tracé
	});
	traceParcours.setMap(map);
}
function initializeMap1() {
	geocoder = new google.maps.Geocoder();
	var mapOptions = {
			zoom: 5,
			center: new google.maps.LatLng(48.8735087, 2.2958688999999595),
			mapTypeControl:false,
			mapTypeId: google.maps.MapTypeId.ROADMAP
	};
	map = new google.maps.Map(document.getElementById("Map"), mapOptions);
	path = new google.maps.MVCArray();
	markers=new google.maps.MVCArray();
	bounds = new google.maps.LatLngBounds();
	traceParcours = new google.maps.Polyline({
		path: path,//chemin du tracé
		strokeColor: "#ff9305",//couleur du tracé
		strokeOpacity: 1.0,//opacité du tracé
		strokeWeight: 2//grosseur du tracé
	});
	traceParcours.setMap(map);
	showAdresse();
}

function showAdresse(){
	
	var newAdresseDepart = document.getElementById("adresseDepart").value;
	var newAdresseArrivee = document.getElementById("adresseArrivee").value;
	geocoder.geocode( { 'address': newAdresseDepart}, function(results, status) {
		if (status == google.maps.GeocoderStatus.OK) {
			point=new google.maps.LatLng(results[0].geometry.location.lat(),results[0].geometry.location.lng());
			bounds.extend(point);
			var myMarkerImage = new google.maps.MarkerImage('/img/map-icon1.png');
			var titleMarker = "";
			try {
				titleMarker = document.getElementById('aeroportDepart').options[document.getElementById('aeroportDepart').selectedIndex].text;
			} catch(err){}
			marker = new google.maps.Marker({
				position: point,
				map: map,
				icon : myMarkerImage,
				title: titleMarker
			});
			if(markers.getAt(0)!=null)
				markers.getAt(0).setMap(null);
			markers.setAt(0,marker);
			path.setAt(0,point);
			try {
				document.getElementById('legendDepart').style.display = 'block';
			} catch(err){}

		}
	});

	geocoder.geocode( { 'address': newAdresseArrivee}, function(results, status) {
		if (status == google.maps.GeocoderStatus.OK) {
			point=new google.maps.LatLng(results[0].geometry.location.lat(),results[0].geometry.location.lng());
			bounds.extend(point);
			var myMarkerImage = new google.maps.MarkerImage('/img/map-icon.png');
			var titleMarker1 = "";
			try {
				titleMarker1 = document.getElementById('aeroportArrivee').options[document.getElementById('aeroportArrivee').selectedIndex].text;
			} catch(err){}
			marker = new google.maps.Marker({
				position: point,
				map: map,
				icon : myMarkerImage,
				title: titleMarker1
			});
			if(markers.getAt(1)!=null)
				markers.getAt(1).setMap(null);
			markers.setAt(1,marker);
			path.setAt(1,point);
			try {
				document.getElementById('legendArrivee').style.display = 'block';
			} catch(err){}
		}
	});
	traceParcours.setPath(path);

	setTimeout(function(){
		var distance = google.maps.geometry.spherical.computeLength(path); 
		try{
			document.getElementById("distance").value = parseInt(Math.floor(distance)/1000);
		} catch(err){}
		getMilieu();
	},2500);

}

function getMilieu(){
	var lat = new Array();
	var lng = new Array();
	var i; 

	for( i = 0 ; i <= 1 ; i++){
		lat.push(markers.getAt(i).getPosition().lat());
		lng.push(markers.getAt(i).getPosition().lng());
	}
	map.panTo(new google.maps.LatLng(((lat[0]+lat[1])/2),((lng[0]+lng[1])/2)));
	map.fitBounds(bounds);
}

function searchLigne(){
	if(document.getElementById('search-advanced-ligne').style.display=="block"){
		document.getElementById('search-advanced-ligne').style.display="none";
		document.getElementById('boutonSearchLigne').innerHTML="Recherche détaillée";
	}
	else{
		document.getElementById('search-advanced-ligne').style.display="block";
		document.getElementById('boutonSearchLigne').innerHTML="Recherche simplifiée";

	}

}

function resetSearch(){
	document.getElementById('mot').value=" ";
	document.getElementById('Origine').value="0";
	document.getElementById('aeroportOrigine').value="0";
	document.getElementById('Depart').value="0";
	document.getElementById('aeroportDepart').value="0";
	document.getElementById('Arrivee').value="0";
	document.getElementById('aeroportArrivee').value="0";
	document.getElementById('dateDepart').value="";
	document.getElementById('dateArrivee').value="";
	document.getElementById('heureDepartMin').value="";
	document.getElementById('heureDepartMax').value="";
	document.getElementById('heureArriveeMin').value="";
	document.getElementById('heureArriveeMax').value="";
	document.getElementById('tarifMin').value="";
	document.getElementById('tarifMax').value="";
	document.getElementById('periodicite-1').checked=false;

}
