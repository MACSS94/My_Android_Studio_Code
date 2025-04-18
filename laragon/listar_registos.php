<?php

// Definir o tipo de conteúdo da resposta como JSON
header('Content-Type: application/json');

// Incluir o arquivo de ligação à base de dados
include '../php/ligaBD.php';  // Inclui o arquivo de ligação à base de dados

// SQL para buscar todos os registos de medicamentos na tabela 'medicamentos'
$sql = "SELECT * FROM medicamentos";

// Executa a consulta e armazena o resultado
$result = $conn->query($sql);

// Verifica se existem registos retornados pela consulta
if ($result->num_rows > 0) {
    // Se houver registos, cria um array para armazená-los
    $registos = array();
    
    // Percorre todos os registos e adiciona-os ao array $registos
    while ($row = $result->fetch_assoc()) {
        // Para cada registo, cria um array com as colunas da base de dados
        $registo = array(
            "id_medicamento" => $row["id_medicamento"], // Atribui o id do medicamento
            "nome_medicamento" => $row["nome_medicamento"], // Atribui o nome do medicamento
            "data_inicio" => $row["data_inicio"], // Atribui a data de início
            "data_fim" => $row["data_fim"], // Atribui a data de fim
            "numero_vezes_dia" => $row["numero_vezes_dia"], // Atribui o número de vezes que o medicamento deve ser tomado por dia
            "notas" => $row["notas"] // Atribui as notas associadas ao medicamento
        );
        
        // Adiciona o registo ao array $registos
        array_push($registos, $registo);
    }
    
    // Retorna todos os registos encontrados em formato JSON
    echo json_encode($registos);
} else {
    // Se não houver registos encontrados, retorna uma mensagem informando isso em formato JSON
    echo json_encode(array("message" => "Nenhum registo encontrado"));
}

// Desativa a exibição de erros (para evitar que erros sejam mostrados ao utilizador)
error_reporting(0);  // Desativa todos os relatórios de erros
ini_set('display_errors', 0); // Desativa a exibição de erros no browser

// Fecha a ligação à base de dados para libertar recursos
$conn->close();  // Fecha a conexão com a base de dados
?>
