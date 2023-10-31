<?php
// Defina o tipo de conteúdo como JSON
header('Content-Type: application/json');

// Verifique se o método de solicitação é POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtenha o conteúdo da solicitação POST
    $data = file_get_contents('php://input');

    // Decodifique o JSON enviado pelo navegador
    $report = json_decode($data, true);

    // Agora você pode processar o relatório de violação como desejar
    // Por exemplo, você pode armazená-lo em um banco de dados, enviá-lo por e-mail, etc.
    // Neste exemplo, apenas gravamos o relatório em um arquivo chamado 'csp-violations.log'
    file_put_contents('csp-violations.log', json_encode($report, JSON_PRETTY_PRINT) . PHP_EOL, FILE_APPEND);
} else {
    // Se a solicitação não for POST, retorne um erro
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
}
