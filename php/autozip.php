<?php
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    $charset = 'utf8mb4';
    $host = getenv('DB_HOST');
    $dbname = getenv('DB_NAME');
    $username = getenv('DB_USERNAME');
    $password = getenv('DB_PASSWORD');
    $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];
    try {
        $pdo = new PDO($dsn, $username, $password, $options);
    } catch (\PDOException $e) {
        http_response_code(500);
        $errorDetails = [
            'error' => 'Database connection failed',
            'message' => $e->getMessage(),
            'code' => $e->getCode()
        ];
        echo json_encode($errorDetails);
        exit;
    }
    $query = isset($_GET['query']) ? trim($_GET['query']) : '';
    if ($query === '') {
        http_response_code(400);
        echo json_encode(['error' => 'Missing query parameter']);
        exit;
    }
    if (ctype_digit($query)) {
        $stmt = $pdo->prepare("SELECT * FROM zip_city_se WHERE zip = ?");
        $stmt->execute([$query]);
        $results = $stmt->fetchAll();
        echo json_encode([
            'input' => $query,
            'type' => 'zip',
            'results' => $results
        ], JSON_PRETTY_PRINT);
    }
?>