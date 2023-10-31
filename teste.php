<?php
function calcularDistancia($lat1, $lon1, $lat2, $lon2) {
    $earthRadius = 6371; // Raio da Terra em km

    // Converte as coordenadas de graus para radianos
    $lat1 = deg2rad(floatval($lat1));
    $lon1 = deg2rad(floatval($lon1));
    $lat2 = deg2rad(floatval($lat2));
    $lon2 = deg2rad(floatval($lon2));

    // Aplica a fórmula de Haversine
    $latDelta = $lat2 - $lat1;
    $lonDelta = $lon2 - $lon1;
    $a = sin($latDelta / 2) * sin($latDelta / 2) + cos($lat1) * cos($lat2) * sin($lonDelta / 2) * sin($lonDelta / 2);
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

    // Calcula a distância em metros
    $distancia_calculada = $earthRadius * $c * 1000;

    return $distancia_calculada;
}

// Valores de latitude e longitude
$lat1 = -23.4637742; 
$lon1 = -46.5169921;
$lat2 = -23.463723;
$lon2 = -46.517033;

// Calcula a distância e imprime o resultado
$distancia = calcularDistancia($lat1, $lon1, $lat2, $lon2);
echo "A distância entre os dois pontos é: " . round($distancia,3) . " metros.";


?>