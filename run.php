<?php
// Conexão com o banco de dados (ajuste as credenciais conforme necessário)
$host = 'localhost';
$dbname = 'teste';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

   
    $stmt = $pdo->query("SELECT id, caption FROM catalog_pages");
    $captions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($captions as $row) {
        $id = $row['id'];
        $caption = $row['caption'];

      
        $translatedCaption = translateToPortuguese($caption);

       
        $updateStmt = $pdo->prepare("UPDATE catalog_pages SET caption = :caption WHERE id = :id");
        $updateStmt->execute([':caption' => $translatedCaption, ':id' => $id]);

        echo "Traduzido: '$caption' -> '$translatedCaption'<br>";
    }
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
}


function translateToPortuguese($text) {
    $url = "https://translate.googleapis.com/translate_a/single?client=gtx&sl=en&tl=pt&dt=t&q=" . urlencode($text);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);
    return $result[0][0][0] ?? $text; // Retorna o texto traduzido ou o original se falhar
}
