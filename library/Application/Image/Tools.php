<?php
class Application_Image_Tools
{
	static public function enregistrerImages($source, $idProduit, $numeroPhoto, $extension){
		$chaine = explode('/', $extension);
		$extension=$chaine[1];
		$cheminPublic = Zend_Registry::get('config')->site->public;
		
		$path = APPLICATION_PATH."/../".$cheminPublic."/img/Produits/";
		$nomPhoto = $idProduit."_".$numeroPhoto.".".$extension;
		$destination = $path.$nomPhoto;
		switch ( $extension ) {
			case 'jpg':
			case 'jpeg':
			case 'JPG':
			case 'JPEG':
				imagejpeg (imagecreatefromjpeg ( $source ),$destination) ;
				break ;
			case 'png':
			case 'PNG':
				$image = imagecreatefrompng($source);
				imagepng ($image,$destination) ;
				break ;
		}
		return $nomPhoto;
	}

	static public function modifierTaille($source,$extension, $maxWidth = 325, $maxHeight = 400){
		$chaine = explode('/', $extension);
		$extension=$chaine[1];
		$TailleImage = getimagesize ($source);

		if(($TailleImage[0]/$TailleImage[1])>($maxWidth/$maxHeight))
		{
			$width = $maxWidth;
			$height = $TailleImage[1] * $width / $TailleImage[0];
		}
		else if(($TailleImage[0]/$TailleImage[1])<($maxWidth/$maxHeight))
		{
			$height = $maxHeight;
			$width = $TailleImage[0] * $height / $TailleImage[1];
		}
		else
		{
			$width = $maxWidth;
			$height = $maxHeight;
		}
		$ImageEnCouleursVraies = imagecreatetruecolor($width, $height);

		switch ( $extension ) {
			case 'jpg':
			case 'jpeg':
			case 'JPG':
			case 'JPEG':
				$NouvelleImage = imagecreatefromjpeg($source);
				imagecopyresampled($ImageEnCouleursVraies, $NouvelleImage, 0, 0, 0, 0, $width, $height, $TailleImage[0], $TailleImage[1]);
				imagejpeg($ImageEnCouleursVraies, $source, 100);
				break ;
			case 'png':
			case 'PNG':
				$NouvelleImage = imagecreatefrompng($source);
				imagecopyresampled($ImageEnCouleursVraies, $NouvelleImage, 0, 0, 0, 0, $width, $height, $TailleImage[0], $TailleImage[1]);
				imagepng($ImageEnCouleursVraies, $source);
				break ;
		}
		imagedestroy($NouvelleImage);
	}

	static public function nouvelleThumbnail($source, $idProduit, $numeroPhoto, $extension, $maxWidth = 101, $maxHeight = 124){
		$cheminPublic = Zend_Registry::get('config')->site->public;
		
		$path = APPLICATION_PATH."/../".$cheminPublic."/img/Produits/Thumbnails/";
		$chaine = explode('/', $extension);
		$extension=$chaine[1];
		$TailleImage = getimagesize ($source);
		$nomPhotoThumbnail = $idProduit."_".$numeroPhoto.".".$extension;
		$destination = $path.$nomPhotoThumbnail;

		if(($TailleImage[0]/$TailleImage[1])>($maxWidth/$maxHeight))
		{
			$width = $maxWidth;
			$height = $TailleImage[1] * $width / $TailleImage[0];
		}
		else if(($TailleImage[0]/$TailleImage[1])<($maxWidth/$maxHeight))
		{
			$height = $maxHeight;
			$width = $TailleImage[0] * $height / $TailleImage[1];
		}
		else
		{
			$width = $maxWidth;
			$height = $maxHeight;
		}
		$ImageEnCouleursVraies = imagecreatetruecolor($width, $height);

		switch ( $extension ) {
			case 'jpg':
			case 'jpeg':
			case 'JPG':
			case 'JPEG':
				$NouvelleImage = imagecreatefromjpeg($source);
				imagecopyresampled($ImageEnCouleursVraies, $NouvelleImage, 0, 0, 0, 0, $width, $height, $TailleImage[0], $TailleImage[1]);
				imagejpeg($ImageEnCouleursVraies, $destination, 100);
				break ;
			case 'png':
			case 'PNG':
				$NouvelleImage = imagecreatefrompng($source);
				imagecopyresampled($ImageEnCouleursVraies, $NouvelleImage, 0, 0, 0, 0, $width, $height, $TailleImage[0], $TailleImage[1]);
				imagepng($ImageEnCouleursVraies, $destination);
				break ;
		}
		imagedestroy($NouvelleImage);
	}

	static public function adaptationTailleImage($source, $maxWidth = 202, $maxHeight = 140){
		$TailleImage = getimagesize ($source);

		if(($TailleImage[0]/$TailleImage[1])>($maxWidth/$maxHeight))
		{
			$NewTailleImage[0] = $maxWidth;
			$NewTailleImage[1] = $TailleImage[1] * $NewTailleImage[0] / $TailleImage[0];
		}
		else if(($TailleImage[0]/$TailleImage[1])<($maxWidth/$maxHeight))
		{
			$NewTailleImage[1] = $maxHeight;
			$NewTailleImage[0] = $TailleImage[0] * $NewTailleImage[1] / $TailleImage[1];
		}
		else
		{
			$NewTailleImage[0] = $maxWidth;
			$NewTailleImage[1] = $maxHeight;
		}
		return $NewTailleImage;
	}
}
