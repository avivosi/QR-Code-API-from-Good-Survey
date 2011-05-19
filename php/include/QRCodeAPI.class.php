<?php
class QRCodeAPI
{
	protected $baseUrl = "http://qrcode.good-survey.com/api/v2";
	
	/**************************************************************************************//**
	 * Generates a QR Code image using 'generate' API method (HTTP GET).
	 * @param content		Content to be encoded in QR Code. This parameter is required.
	 * @param format		Target image format: png (default), jpg, bmp, tif, xaml, svg, eps, txt, html, zip
	 * @param version 		Defines capacity and overall image size; values 1..40, unspecified or empty value for auto (default value)
	 * @param size			Size of a single QR code "pixel" (module) in real pixels; values 1..20, default: 8
	 * @param padding 		Border thickness in QR code "pixels" (modules); values 0..20, default: 4
	 * @param em			Encoding mode, defines what kind of characters can encode and affects total image size; values:
	 * 						* byte - can store any data, default value
	 * 						* numeric - only numbers are allowed 0-9
	 * 						* alphanumeric - numbers 0-9, letters A-Z, subset of punctuation .$%/*:-
	 * @param ec			Error Correction level, defines how much code can be damaged but still recoverable, affects capacity and image size; values: M (default), H, L, Q
	 * @param attachment	Flag indicating whether to return response as downloadable file, values "true" or "false", default: false 
	 * @return Returns image of the specified format or PNG image by default.
	 ******************************************************************************************/
	public function generate($content, $format = "png", $version = null, $size = null, $padding = null, $em = null, $ec = null, $attachment = false)
	{
		$query = "content=" . urlencode($content);
		if (!empty($format)) $query .= "format=$format";
		if (!empty($version)) $query .= "version=$version";
		if (!empty($size)) $query .= "size=$size";
		if (!empty($padding)) $query .= "padding=$padding";
		if (!empty($em)) $query .= "em=$em";
		if (!empty($ec)) $query .= "ec=$ec";
		if (!empty($attachment) && $attachment) $query .= "attachment=true";
		$url = $this->baseUrl . "/generate?" . $query;
		$result = file_get_contents($url);
		return $result;
	}
	
	/**************************************************************************************//**
	 * Generates a QR Code image using 'encode' API method (HTTP POST).
	 * @param content		Content to be encoded in QR Code. This parameter is required.
	 * @param format		Target image format: png (default), jpg, bmp, tif, xaml, svg, eps, txt, html, zip
	 * @param version 		Defines capacity and overall image size; values 1..40, unspecified or empty value for auto (default value)
	 * @param size			Size of a single QR code "pixel" (module) in real pixels; values 1..20, default: 8
	 * @param padding 		Border thickness in QR code "pixels" (modules); values 0..20, default: 4
	 * @param em			Encoding mode, defines what kind of characters can encode and affects total image size; values:
	 * 						* byte - can store any data, default value
	 * 						* numeric - only numbers are allowed 0-9
	 * 						* alphanumeric - numbers 0-9, letters A-Z, subset of punctuation .$%/*:-
	 * @param ec			Error Correction level, defines how much code can be damaged but still recoverable, affects capacity and image size; values: M (default), H, L, Q
	 * @param attachment	Flag indicating whether to return response as downloadable file, values "true" or "false", default: false 
	 * @return Returns image of the specified format or PNG image by default.
	 ******************************************************************************************/
	public function encode($content, $format = "png", $version = null, $size = null, $padding = null, $em = null, $ec = null)
	{
		$url = $this->baseUrl . "/encode";
		
		$headers = array();
		$headers[] = "Content-Type: application/json";
		$headers[] = "Accept: image/png";
		
		$model = array();
		$model["content"] = $content;
		if (!empty($format)) $model["format"] = $format;
		if (!empty($version)) $model["version"] = $version;
		if (!empty($size)) $model["size"] = $size;
		if (!empty($padding)) $model["padding"] = $padding;
		if (!empty($em)) $model["em"] = $em;
		if (!empty($ec)) $model["ec"] = $ec;
		$json = json_encode($model);
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		$result = curl_exec($ch);
		curl_close($ch);
		
		return $result;
	}
	
	/**************************************************************************************//**
	 * Decodes a QR Code image using 'decode' API method (HTTP POST).
	 * @param file	Path to local QR Code image file.
	 * @return Returns a content decoded from a QR Code image. 
	 ******************************************************************************************/
	public function decode($file)
	{
		$url = $this->baseUrl . "/decode?format=png";
		
		$headers = array();
		$headers[] = "Content-Type: image/png";
		$headers[] = "Accept: application/json";
		
		$hfile = fopen($file, "r");
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		curl_setopt($ch, CURLOPT_POST, 1);
		//curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data); //This line is here for warning, won't work that way
		curl_setopt($ch, CURLOPT_UPLOAD, 1);
		curl_setopt($ch, CURLOPT_INFILE, $hfile);
		curl_setopt($ch, CURLOPT_INFILESIZE, filesize($file));
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST"); //Force POST method

		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		//curl_setopt($ch, CURLOPT_HEADER, TRUE); //Show header content, only when debugging

		$result = curl_exec($ch);

		fclose($hfile);
		curl_close($ch);
		return $result;
	}
}
?>