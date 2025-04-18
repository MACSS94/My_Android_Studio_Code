<?php
// Definir o tipo de conteúdo da resposta como JSON
header("Content-Type: application/json");

// Conectar à base de dados MySQL
$liga = new mysqli("localhost", "root", "M+1+g+u+e+l123", "medicamentos");

// Verificar se houve algum erro na conexão com a base de dados
if ($liga->connect_error) {
    // Caso haja erro de conexão, retorna uma mensagem de erro em formato JSON
    die(json_encode(["message" => "Conexão falhou: " . $liga->connect_error]));
}

// Verificar se o método HTTP da requisição é POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Receber os dados enviados no corpo da requisição no formato JSON
    $data = json_decode(file_get_contents("php://input"), true);

    // Validar se todos os campos necessários foram fornecidos no JSON
    if (
        isset($data['id']) &&                  // Verifica se 'id' foi enviado
        isset($data['nome_medicamento']) &&     // Verifica se 'nome_medicamento' foi enviado
        isset($data['data_inicio']) &&          // Verifica se 'data_inicio' foi enviado
        isset($data['data_fim']) &&             // Verifica se 'data_fim' foi enviado
        isset($data['numero_vezes_dia']) &&     // Verifica se 'numero_vezes_dia' foi enviado
        isset($data['notas'])                   // Verifica se 'notas' foi enviado
    ) {
        // Atribui os valores recebidos do JSON às variáveis
        $id = (int)$data['id'];                     // Converte o 'id' para inteiro
        $nome_medicamento = $data['nome_medicamento']; // Atribui o nome do medicamento
        $data_inicio = $data['data_inicio'];           // Atribui a data de início
        $data_fim = $data['data_fim'];                 // Atribui a data de fim
        $numero_vezes_dia = (int)$data['numero_vezes_dia']; // Converte o número de vezes por dia para inteiro
        $notas = $data['notas'];                        // Atribui as notas

        // Preparar a consulta SQL para atualizar os dados do medicamento na base de dados
        $stmt = $liga->prepare("UPDATE medicamentos SET nome_medicamento = ?, data_inicio = ?, data_fim = ?, numero_vezes_dia = ?, notas = ? WHERE id_medicamento = ?");
        
        // Vincular os parâmetros à consulta SQL (para evitar SQL Injection)
        $stmt->bind_param("sssisi", $nome_medicamento, $data_inicio, $data_fim, $numero_vezes_dia, $notas, $id);

        // Executar a consulta e verificar se foi bem-sucedida
        if ($stmt->execute()) {
            // Se a execução for bem-sucedida, retorna uma mensagem de sucesso em formato JSON
            echo json_encode(["message" => "Registo atualizado com sucesso!"]);
        } else {
            // Caso ocorra algum erro na execução, retorna uma mensagem de erro com detalhes
            echo json_encode(["message" => "Erro ao atualizar registo!", "error" => $stmt->error]);
        }

        // Fechar a declaração preparada para liberar recursos
        $stmt->close();
    } else {
        // Se algum campo obrigatório estiver ausente, retorna uma mensagem informando dados incompletos
        echo json_encode(["message" => "Dados incompletos fornecidos!"]);
    }
} else {
    // Caso o método da requisição não seja POST, retorna uma mensagem informando que o método não é permitido
    echo json_encode(["message" => "Método não permitido!"]);
}

// Fechar a conexão com a base de dados
$liga->close();
?>
