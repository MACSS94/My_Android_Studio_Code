<?php
// Incluir o arquivo de ligação à base de dados
include '../php/ligaBD.php';

// Definir o tipo de conteúdo da resposta como JSON
header('Content-Type: application/json');

// Obtém os dados da requisição em formato JSON
$data = json_decode(file_get_contents("php://input"));

// Verifica se os dados foram recebidos corretamente
if ($data) {
    // Atribui os valores do JSON às variáveis correspondentes
    $username_or_email = isset($data->username_or_email) ? trim($data->username_or_email) : ''; // Verifica se o 'username_or_email' foi enviado
    $password = isset($data->password) ? trim($data->password) : ''; // Verifica se a 'password' foi enviada

    // Verifica se os campos obrigatórios não estão vazios
    if (!empty($username_or_email) && !empty($password)) {
        // Prepara a consulta SQL para verificar se o 'username_or_email' existe na base de dados
        $stmt = $conn->prepare("SELECT * FROM registos WHERE (user = ? OR email = ?)");
        
        // Verifica se ocorreu algum erro ao preparar a consulta
        if ($stmt === false) {
            // Se ocorrer erro na preparação da consulta, retorna mensagem de erro em formato JSON
            echo json_encode(array("error" => "Erro ao preparar a consulta: " . $conn->error));
            exit; // Encerra a execução do código
        }

        // Vincula os parâmetros à consulta preparada
        $stmt->bind_param("ss", $username_or_email, $username_or_email); // 'ss' indica que os parâmetros são do tipo string

        // Executa a consulta
        $stmt->execute();

        // Obtém o resultado da consulta
        $result = $stmt->get_result();

        // Verifica se o resultado contém registros
        if ($result->num_rows > 0) {
            // Se o registo for encontrado, obtém os dados do utilizador
            $user = $result->fetch_assoc();

            // Verifica se a senha fornecida corresponde à senha armazenada na base de dados
            if ($password === $user['password']) { // Comparação simples de senha
                // Se a senha for correta, inicia a sessão e armazena o id_registos
                session_start();
                $_SESSION['id_registos'] = $user['id_registos']; // Armazena o id_registos na sessão

                // Retorna uma resposta de sucesso com o nome de utilizador
                echo json_encode(array("success" => "Login bem-sucedido!", "username" => $user['user']));
            } else {
                // Caso a senha esteja incorreta, retorna mensagem de erro
                echo json_encode(array("error" => "Senha incorreta!"));
            }
        } else {
            // Caso o utilizador ou email não sejam encontrados, retorna mensagem de erro
            echo json_encode(array("error" => "Usuário ou email não encontrado!"));
        }

        // Fecha a declaração preparada
        $stmt->close();
    } else {
        // Se algum campo obrigatório estiver vazio, retorna mensagem de erro
        echo json_encode(array("error" => "Todos os campos obrigatórios devem ser preenchidos!"));
    }
} else {
    // Caso o JSON não seja válido ou o método de requisição não seja o esperado, retorna erro
    echo json_encode(array("error" => "Método de requisição inválido ou JSON mal formatado!"));
}

// Fecha a ligação à base de dados para libertar recursos
$conn->close();
?>
