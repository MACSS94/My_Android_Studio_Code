<?php
// Arquivo PHP: regista_user.php
// Inclui o arquivo de ligação à base de dados
include '../php/ligaBD.php';

// Define que o tipo de conteúdo da resposta será em formato JSON
header('Content-Type: application/json');

// Lê o conteúdo da requisição e decodifica o JSON
$data = json_decode(file_get_contents("php://input"));

// Verifica se os dados foram recebidos corretamente
if ($data) {
    // Extrai os dados recebidos no JSON e faz a remoção de espaços em branco
    $user = isset($data->user) ? trim($data->user) : '';       // Nome de utilizador
    $email = isset($data->email) ? trim($data->email) : '';     // Endereço de email
    $password = isset($data->password) ? trim($data->password) : ''; // Senha do utilizador

    // Verifica se todos os campos obrigatórios foram preenchidos
    if (!empty($user) && !empty($email) && !empty($password)) {
        // Prepara a consulta para verificar se o nome de utilizador, email ou senha já estão registados na base de dados
        $stmt = $conn->prepare("SELECT * FROM registos WHERE user = ? OR email = ? OR password = ?");
        
        // Verifica se houve erro na preparação da consulta
        if ($stmt === false) {
            // Caso haja erro, retorna uma mensagem de erro em formato JSON
            echo json_encode(array("error" => "Erro ao preparar a consulta: " . $conn->error));
            exit;  // Encerra o script para evitar execução de código adicional
        }

        // Vincula os parâmetros à consulta (o nome de utilizador, email e senha)
        $stmt->bind_param("sss", $user, $email, $password);
        
        // Executa a consulta
        $stmt->execute();
        
        // Obtém o resultado da consulta
        $result = $stmt->get_result();

        // Verifica se algum registo já existe na base de dados
        if ($result->num_rows > 0) {
            // Se já houver algum registo, retorna uma mensagem de erro
            echo json_encode(array("error" => "Usuário, email ou senha já estão em uso!"));
        } else {
            // Caso contrário, prepara a inserção de um novo utilizador na base de dados
            $insert_stmt = $conn->prepare("INSERT INTO registos (user, email, password) VALUES (?, ?, ?)");
            
            // Verifica se houve erro na preparação da consulta de inserção
            if ($insert_stmt === false) {
                // Caso haja erro, retorna uma mensagem de erro em formato JSON
                echo json_encode(array("error" => "Erro ao preparar a consulta de inserção: " . $conn->error));
                exit;  // Encerra o script
            }

            // Vincula os parâmetros para inserção (nome de utilizador, email e senha)
            $insert_stmt->bind_param("sss", $user, $email, $password);

            // Executa a inserção na base de dados
            if ($insert_stmt->execute()) {
                // Se a inserção for bem-sucedida, retorna uma mensagem de sucesso
                echo json_encode(array("success" => "Utilizador registado com sucesso!"));
            } else {
                // Caso ocorra um erro na inserção, retorna uma mensagem de erro
                echo json_encode(array("error" => "Erro ao registrar: " . $insert_stmt->error));
            }

            // Fecha a declaração de inserção
            $insert_stmt->close();
        }

        // Fecha a declaração de verificação
        $stmt->close();
    } else {
        // Caso algum campo obrigatório não tenha sido preenchido
        echo json_encode(array("error" => "Todos os campos obrigatórios devem ser preenchidos!"));
    }
} else {
    // Caso o corpo da requisição não seja um JSON válido
    echo json_encode(array("error" => "Método de requisição inválido ou JSON mal formatado!"));
}

// Fecha a conexão com a base de dados para libertar os recursos
$conn->close();
?>
