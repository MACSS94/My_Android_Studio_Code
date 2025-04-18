<?php
// Define o tipo de resposta como JSON
header("Content-Type: application/json");

// Incluir o arquivo de ligação à base de dados
include '../php/ligaBD.php';

// Verifica se a conexão à base de dados foi bem-sucedida
if ($conn->connect_error) {
    // Se houver erro na conexão, retorna uma mensagem em formato JSON
    echo json_encode(["message" => "Erro de ligação à base de dados: " . $conn->connect_error]);
    exit;  // Encerra a execução do script caso haja erro de conexão
}

// Verifica se o método HTTP utilizado na requisição é POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recebe os dados enviados no corpo da requisição (em formato JSON)
    $data = json_decode(file_get_contents("php://input"), true);

    // Verifica se os dados necessários foram enviados corretamente
    if (!isset($data['nome_medicamento'], $data['data_inicio'], $data['data_fim'], $data['numero_vezes_dia'], $data['notas'])) {
        // Se algum campo estiver em falta, retorna uma mensagem de erro
        echo json_encode(["message" => "Dados incompletos enviados!"]);
        exit;  // Encerra a execução do script
    }

    // Extrai os dados do JSON recebido
    $nome_medicamento = $data['nome_medicamento'];  // Nome do medicamento
    $data_inicio = $data['data_inicio'];  // Data de início do medicamento
    $data_fim = $data['data_fim'];  // Data de fim do medicamento
    $numero_vezes_dia = (int)$data['numero_vezes_dia'];  // Número de vezes que o medicamento deve ser tomado por dia
    $notas = $data['notas'];  // Notas adicionais sobre o medicamento

    // Prepara a consulta SQL para inserir os dados na tabela 'medicamentos'
    $stmt = $conn->prepare("INSERT INTO medicamentos (nome_medicamento, data_inicio, data_fim, numero_vezes_dia, notas) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) {
        // Se ocorrer um erro ao preparar a consulta, retorna uma mensagem de erro
        echo json_encode(["message" => "Erro ao preparar a consulta: " . $conn->error]);
        exit;  // Encerra a execução do script
    }

    // Associa os parâmetros à consulta SQL
    $stmt->bind_param("sssis", $nome_medicamento, $data_inicio, $data_fim, $numero_vezes_dia, $notas);

    // Executa a consulta SQL e verifica se a inserção foi bem-sucedida
    if ($stmt->execute()) {
        // Se a inserção for bem-sucedida, retorna uma mensagem de sucesso
        echo json_encode(["message" => "Medicamento registado com sucesso!"]);
    } else {
        // Se ocorrer um erro ao tentar inserir, retorna uma mensagem de erro
        echo json_encode(["message" => "Erro ao registar medicamento: " . $stmt->error]);
    }

    // Fecha a declaração preparada para libertar recursos
    $stmt->close();
} else {
    // Se o método HTTP utilizado não for POST, retorna uma mensagem de erro
    echo json_encode(["message" => "Método de requisição inválido!"]);
}

// Fecha a conexão com a base de dados
$conn->close();
?>
