<?php
	define('WEBSERVICE_URL', 'http://webservices.cotedor-tourisme.com/Services.asmx/');
	
	header('Content-Type: application/xml; charset=utf-8');
	
	$lat = isset($_GET['lat']) ? $_GET['lat'] : 47.310499;	// valeur par défaut = centre de Dijon
	$lng = isset($_GET['lng']) ? $_GET['lng'] : 5.069257;	// valeur par défaut = centre de Dijon
	$type = isset($_GET['type']) ? $_GET['type'] : '';	// valeur par défaut = tous les types
	$dist = isset($_GET['dist']) ? $_GET['dist'] : 5;		// valeur par défaut = 1km
	
	if ($type == 'restaurants')
	{
		$table = 'c_obj_res';
	}
	else if ($type == 'hotels')
	{
		$table = 'c_obj_hot';
	}
	else if ($type == 'chambre_hotes')
	{
		$table = 'c_obj_cht';
	}
	
	//-- pour pouvoir passer n'importe quel type, cf liste des types de donn�es ci jointe
	else if (!empty($type))
	{
		$table = 'c_obj_' . $type;
	}
	
	else
	{
		$table = 'c_obj';
	}
	
	print(getPointsNear($table, $lat, $lng, $dist));
	
	function getPointsNear($table, $lat, $lng, $dist)
	{
		$northWestLimit = getPointWithDistanceAndBearing($lat, $lng, $dist, 315);
		$southEastLimit = getPointWithDistanceAndBearing($lat, $lng, $dist, 135);
		$westLimit = getPointWithDistanceAndBearing($lat, $lng, $dist, 270);
		
		$northParam = '';
		$southParam = '';
		$eastParam = '';
		$westParam = '';
		
		$northParam = '{"concept":"obj_common","champvaleur":"lat","valeur":"' . $northWestLimit[0] . '","operateur":"<","foncteur":"AND"}';
		$southParam = '{"concept":"obj_common","champvaleur":"lat","valeur":"' . $southEastLimit[0] . '","operateur":">","foncteur":"AND"}';
		$westParam = '{"concept":"obj_common","champvaleur":"lng","valeur":"' . $northWestLimit[1] . '","operateur":">","foncteur":"AND"}';
		$eastParam = '{"concept":"obj_common","champvaleur":"lng","valeur":"' . $southEastLimit[1] . '","operateur":"<","foncteur":"AND"}';
		
		$params = urlencode('{"crit":['
		. $northParam 
		. ',' 
		. $southParam 
		. ','  
		. $westParam 
		. ',' 
		. $eastParam
		. ']}');
		//~ $params = '';
		
		$url = WEBSERVICE_URL . 'GetGeoDataXML?ipstrTable=' . $table . '&ipstrSupport=GP&ipstrId=all&ipstrtabParams=' . $params;
		
		//~ return $url;
		return file_get_contents($url);
	}
	
	function getPointWithDistanceAndBearing($lat, $lng, $dist, $bearing)
	{
		$R = 6378.1;				// rayon de la terre
		$brng = $bearing * pi() / 180;	// angle en radians
		$d = $dist;					// distance
		
		$lat1 = $lat * pi() / 180;
		$lon1 = $lng * pi() / 180;
		
		$lat2 = asin( sin($lat1)*cos($d/$R) +
		     cos($lat1)*sin($d/$R)*cos($brng));

		$lon2 = $lon1 + atan2(sin($brng)*sin($d/$R)*cos($lat1),
			     cos($d/$R)-sin($lat1)*sin($lat2));
		
		$lat2 = $lat2 * 180 / pi();
		$lon2 = $lon2 * 180 / pi();
		
		return array($lat2, $lon2);
	}
?>

