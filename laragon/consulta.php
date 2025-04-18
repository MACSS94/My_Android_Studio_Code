<?php
// Definir o tipo de conteúdo da resposta como JSON
header('Content-Type: application/json');

// Incluir o ficheiro de ligação à base de dados
include '../php/ligaBD.php';

// Verificar se o método HTTP da requisição é GET
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // SQL para buscar todos os medicamentos na tabela 'medicamentos'
    $sql = "SELECT id_medicamento, nome_medicamento, data_inicio, data_fim, numero_vezes_dia, notas FROM medicamentos";
    
    // Executar a consulta SQL na base de dados
    $result = $conn->query($sql);

    // Verificar se existem resultados (registos) na consulta
    if ($result->num_rows > 0) {
        // Se existirem registos, converte os resultados para um array associativo
        $registos = $result->fetch_all(MYSQLI_ASSOC);
        
        // Retorna os dados dos medicamentos em formato JSON
        echo json_encode($registos);
    } else {
        // Se não houverem registos, retorna uma mensagem de erro
        echo json_encode(["message" => "Nenhum medicamento encontrado"]);
    }
}

// Fechar a ligação à base de dados
$conn->close();
?>
