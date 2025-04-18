<?php

// Incluir o arquivo de conexão com a base de dados
include 'ligaBD.php'; // Inclui o arquivo ligaBD.php para a ligação à base de dados

// Verifica se o parâmetro 'id' foi fornecido na URL e se não está vazio
if (isset($_GET['id']) && !empty($_GET['id'])) {
    // Obtém o parâmetro 'id' de forma segura
    $id = $_GET['id'];

    // Prepara a consulta SQL utilizando uma declaração preparada para evitar injeção SQL
    $sql = "DELETE FROM medicamentos WHERE id_medicamento = ?";

    // Prepara a declaração SQL
    if ($stmt = $conn->prepare($sql)) {
        // Vincula o parâmetro (id) à declaração preparada
        $stmt->bind_param("i", $id); // 'i' indica que o parâmetro é um inteiro

        // Executa a consulta SQL
        if ($stmt->execute()) {
            // Se a exclusão for bem-sucedida
            echo "Registo excluído com sucesso!";
        } else {
            // Se houver erro na execução da consulta SQL
            echo "Erro ao excluir registo: " . $stmt->error;
        }

        // Fecha a declaração preparada para liberar recursos
        $stmt->close();
    } else {
        // Se não for possível preparar a consulta SQL
        echo "Erro na preparação da consulta: " . $conn->error;
    }
} else {
    // Se o parâmetro 'id' não for fornecido ou for inválido
    echo "ID inválido ou não fornecido!";
}

// Fecha a ligação à base de dados
$conn->close();

?>
