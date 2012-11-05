function initialisation(){
	initializeMap();
	initializeAeroport();

//	RechercheAdresse("depart","CDG");
//	RechercheAdresse("arrivee","CDG");
	affichePeriodicite();
	//setTimeout(showAdresse, 1000);

}

function initializeAeroport()
{
	if(document.getElementById('aeroportOrigine').value=="")
		RechercheAeroport("origine",250);
	if(document.getElementById('aeroportDepart').value=="")
		RechercheAeroport("depart",250);
	else
		RechercheAdresse("depart",document.getElementById('aeroportDepart').value);
	if(document.getElementById('aeroportArrivee').value=="")
		RechercheAeroport("arrivee",250);
	else
		RechercheAdresse("arrivee",document.getElementById('aeroportArrivee').value);
	showAdresse();
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



$(function(){
	//$('#basic_example_3').datetimepicker({
	$("input[name=dateDepart]").datepicker({
		dateFormat: "dd-mm-yy",
		timeFormat: "hh:mm:ss t",
		ampm: false
	});

	$("input[name=dateArrivee]").datepicker({
		dateFormat: "dd-mm-yy",
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

function affichePeriodicite(){
	if(document.getElementById('periodicite-1').checked==true){
		console.log("periodique");
		document.getElementById("fieldset-periodique").style.display = 'block';
		document.getElementById("fieldset-carte").style.display = 'none';
		//document.getElementById("globalDate2").style.display = 'none';
		//document.getElementById("globalTarif").style.display = 'none';
	}
	else if (document.getElementById('periodicite-0').checked==true)
	{
		console.log("carte");

		document.getElementById("fieldset-periodique").style.display = 'none';
		document.getElementById("fieldset-carte").style.display = 'block';
		//document.getElementById("globalDate2").style.display = 'block';
		//document.getElementById("globalTarif").style.display = 'block';
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
	}
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

function showAdresse(){
	var newAdresseDepart=document.getElementById("adresseDepart").value;
	var newAdresseArrivee=document.getElementById("adresseArrivee").value;
	geocoder.geocode( { 'address': newAdresseDepart}, function(results, status) {
		if (status == google.maps.GeocoderStatus.OK) {
			point=new google.maps.LatLng(results[0].geometry.location.lat(),results[0].geometry.location.lng());
			bounds.extend(point);
			var myMarkerImage = new google.maps.MarkerImage('/img/map-icon1.png');
			marker = new google.maps.Marker({
				position: point,
				map: map,
				icon : myMarkerImage,
				title: document.getElementById('aeroportDepart').options[document.getElementById('aeroportDepart').selectedIndex].text
			});
			if(markers.getAt(0)!=null)
				markers.getAt(0).setMap(null);
			markers.setAt(0,marker);
			path.setAt(0,point);
			document.getElementById('legendDepart').style.display = 'block';
		}
	});

	geocoder.geocode( { 'address': newAdresseArrivee}, function(results, status) {
		if (status == google.maps.GeocoderStatus.OK) {
			point=new google.maps.LatLng(results[0].geometry.location.lat(),results[0].geometry.location.lng());
			bounds.extend(point);
			var myMarkerImage = new google.maps.MarkerImage('/img/map-icon.png');
			marker = new google.maps.Marker({
				position: point,
				map: map,
				icon : myMarkerImage,
				title: document.getElementById('aeroportArrivee').options[document.getElementById('aeroportArrivee').selectedIndex].text
			});
			if(markers.getAt(1)!=null)
				markers.getAt(1).setMap(null);
			markers.setAt(1,marker);
			path.setAt(1,point);
			document.getElementById('legendArrivee').style.display = 'block';
		}
	});

	traceParcours.setPath(path);

	setTimeout(function(){
		var distance = google.maps.geometry.spherical.computeLength(path); 
		document.getElementById("distance").value = Math.floor( distance );
		getMilieu();
	},500);

}

function getMilieu(){
	var lat = new Array();
	var lng = new Array();
	for(var i=0;i<=1;i++){
		lat.push(markers.getAt(i).getPosition().lat());
		lng.push(markers.getAt(i).getPosition().lng());
	}
	map.panTo(new google.maps.LatLng(((lat[0]+lat[1])/2),((lng[0]+lng[1])/2)));
	map.fitBounds(bounds);
}


