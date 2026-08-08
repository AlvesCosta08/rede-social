<?php
$host = '50.116.87.140';
$port = 3306;
$user = 'adtc2m99_igreja_conectada';
$pass = 'Da74@812'; // ⚠️ SUBSTITUA PELA SENHA CORRETA
$db   = 'adtc2m99_igreja_conectada';

echo "====================================\n";
echo "🔍 TESTE DE CONEXÃO COM BANCO\n";
echo "====================================\n\n";

echo "Host: $host\n";
echo "Port: $port\n";
echo "Usuário: $user\n";
echo "Banco: $db\n\n";

// Tenta conectar usando MySQLi
$conn = mysqli_connect($host, $user, $pass, $db, $port);

if (!$conn) {
    echo "❌ ERRO DE CONEXÃO!\n";
    echo "Erro: " . mysqli_connect_error() . "\n";
    
    // Mostra dicas
    echo "\n====================================\n";
    echo "🔧 DICAS DE SOLUÇÃO:\n";
    echo "====================================\n";
    echo "1. Verifique se a senha está correta\n";
    echo "2. Verifique se o usuário tem permissão\n";
    echo "3. Verifique se o host permite conexões remotas\n";
    echo "4. Verifique se o banco de dados existe\n";
    echo "5. Verifique as credenciais no arquivo .env\n";
} else {
    echo "✅ CONEXÃO BEM SUCEDIDA!\n\n";
    
    // Tenta listar tabelas
    $result = mysqli_query($conn, "SHOW TABLES");
    if ($result) {
        echo "Tabelas no banco '$db':\n";
        while ($row = mysqli_fetch_array($result)) {
            echo "  - " . $row[0] . "\n";
        }
    }
    
    mysqli_close($conn);
}
?>