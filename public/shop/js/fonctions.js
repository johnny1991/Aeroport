function AffichageSousCategorie()
{
	AffichageDisplaySousCategorie();
	$.ajaxSetup ({
		// Disable caching of AJAX responses
		cache: false
	});	 
	$.ajax(
			{
		        async:  false,
		        cache: false,
				type: "POST",
				url: "/produit/affichage-sous-categorie",
				data: 'categorie='+categorie,
				success: function(msg) 
				{  
					$('#sousCategorie').empty();
					$('#sousCategorie').append(msg);	
				}
			}
	);
}

function AffichageDisplaySousCategorie(){
	categorie = document.getElementById('categorie').value;
	console.log(categorie);
	if(	document.getElementById('categorie').value != '0')
		document.getElementById('sousCategorie').style.display='block';
	else
		document.getElementById('sousCategorie').style.display='none';
}

function AffichageImage(element)
{
	var pathProduit = "img/Produits/";
	var  pathThumbnails = "img/Produits/Thumbnails/";
	var srcPrimary = document.getElementById('big_photo').src;
	var split = srcPrimary.split('/');
	for (var i=0;i<split.length;i++)
		nomPhotoPrimary = split[i];
	var srcSecondary = element.src;
	var split1 = srcSecondary.split('/');
	for (var i=0;i<split1.length;i++)
		nomPhotoSecondary = split1[i];
	document.getElementById('big_photo').src = pathProduit+nomPhotoSecondary;

}

function ChangeRecherche(){
	if (document.getElementById('mot').value == "")
		document.getElementById('mot').value="Rechercher ...";
	else if (document.getElementById('mot').value == "Rechercher ...")
		document.getElementById('mot').value="";
}

var OldChoixAdresse = "";

function chooseAdresse(id){
	try{
		document.getElementById(OldChoixAdresse).style.background="white";	
	}
	catch(err){}
	document.getElementById('choix-'+id).checked="checked";
	document.getElementById(id).style.background="#CDD5D4";
	OldChoixAdresse = id;
}

var OldChoixTransport = "";

function chooseModeLivraison(id){
	try{
		document.getElementById(OldChoixTransport).style.background="white";	
	}
	catch(err){}
	document.getElementById('choix-'+id).checked="checked";
	document.getElementById(id).style.background="#CDD5D4";
	OldChoixTransport = id;
}

var OldChoixPaiement = "";


function chooseModePaiement(id){
	try{
		document.getElementById(OldChoixPaiement).style.background="white";	
	}
	catch(err){}
	document.getElementById('choix-'+id).checked="checked";
	document.getElementById(id).style.background="#CDD5D4";
	OldChoixPaiement = id;
}