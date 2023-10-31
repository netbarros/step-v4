<?php
// Versão -  Fórmula de Haversine aplicada em PHP*/

function distancia($lat1, $lon1, $lat2, $lon2) {

$lat1 = deg2rad($lat1);
$lat2 = deg2rad($lat2);
$lon1 = deg2rad($lon1);
$lon2 = deg2rad($lon2);

$dist = (6371 * acos( cos( $lat1 ) * cos( $lat2 ) * cos( $lon2 - $lon1 ) + sin( $lat1 ) * sin($lat2) ) );
$dist = number_format($dist, 2, '.', '');
return $dist;
}

//echo "Fórmula de Haversine: ".distancia(-23.50904,-47.493690, -23.55547,-47.524635) . " Km<br />";

// 0.92 Km


 
?>