<?php
// Variáveis de ligação à base de dados
$servername = "localhost";  // Endereço do servidor da base de dados
$user = "root";             // Nome de utilizador (usuário)
$passwd = "M+1+g+u+e+l123"; // Palavra-passe (senha) para a conexão com a base de dados
$bd = "medicamentos";       // Nome da base de dados a que nos pretendemos conectar

// Estabelece a ligação à base de dados utilizando as variáveis definidas acima
$conn = mysqli_connect($servername, $user, $passwd, $bd);

// Verifica se a ligação à base de dados foi bem-sucedida
if (!$conn) {
    // Caso a ligação falhe, exibe uma mensagem detalhada de erro
    $error = mysqli_connect_error();  // Captura o erro da ligação
    echo json_encode(array(
        "error" => "A ligação com a base de dados falhou",  // Mensagem de erro
        "details" => $error  // Detalhes do erro
    ));
    exit(); // Encerra o script para evitar a execução do código seguinte
}
?>
