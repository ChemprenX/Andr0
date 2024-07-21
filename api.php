<?php
include 'config.php';
require 'vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$headers = getallheaders();
$authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : '';

if ($authHeader) {
    list($jwt) = sscanf($authHeader, 'Bearer %s');

    if ($jwt) {
        try {
            $decoded = JWT::decode($jwt, new Key($jwt_secret_key, 'HS256'));
            $userRole = $decoded->data->role;

            if ($userRole === 'admin') {
                include 'header.php';
                include 'sidebar.php';
                ?>
                <section>
                    <h2>API Management</h2>
                    <?php
                    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                        $apiName = $_POST['api_name'];
                        $apiKey = bin2hex(random_bytes(32)); // Generate a random API key
                        $params = $_POST['params'] ?? []; // Retrieve parameters

                        // Insert API Key
                        $sql = "INSERT INTO api_keys (api_name, api_key) VALUES ('$apiName', '$apiKey')";
                        if ($conn->query($sql) === TRUE) {
                            // Insert Parameters
                            foreach ($params as $param) {
                                $paramName = $conn->real_escape_string($param['name']);
                                $paramValue = $conn->real_escape_string($param['value']);
                                $sql = "INSERT INTO api_parameters (api_key, parameter_name, parameter_value) 
                                        VALUES ('$apiKey', '$paramName', '$paramValue')";
                                $conn->query($sql);
                            }
                            echo '<p>API created successfully. Key: ' . htmlspecialchars($apiKey) . '</p>';
                        } else {
                            echo '<p>Error creating API: ' . $conn->error . '</p>';
                        }
                    }
                    ?>

                    <form method="post">
                        <label for="api_name">API Name:</label>
                        <input type="text" id="api_name" name="api_name" required>
                        
                        <h3>Parameters</h3>
                        <div id="parameters">
                            <div class="parameter">
                                <label for="param_name_1">Parameter Name:</label>
                                <input type="text" name="params[0][name]" required>
                                <label for="param_value_1">Parameter Value:</label>
                                <input type="text" name="params[0][value]" required>
                            </div>
                        </div>
                        <button type="button" onclick="addParameter()">Add More Parameters</button>
                        <button type="submit">Create API</button>
                    </form>

                    <script>
                    let paramCount = 1;

                    function addParameter() {
                        const container = document.getElementById('parameters');
                        const newParamDiv = document.createElement('div');
                        newParamDiv.className = 'parameter';
                        newParamDiv.innerHTML = `
                            <label for="param_name_${paramCount}">Parameter Name:</label>
                            <input type="text" name="params[${paramCount}][name]" required>
                            <label for="param_value_${paramCount}">Parameter Value:</label>
                            <input type="text" name="params[${paramCount}][value]" required>
                        `;
                        container.appendChild(newParamDiv);
                        paramCount++;
                    }
                    </script>

                </section>
                <?php
                include 'footer.php';
            } else {
                echo 'Access denied. Admins only.';
            }

        } catch (Exception $e) {
            echo json_encode([
                'message' => 'Access denied',
                'error' => $e->getMessage()
            ]);
        }
    } else {
        echo json_encode([
            'message' => 'No token provided'
        ]);
    }
} else {
    echo json_encode([
        'message' => 'Authorization header not found'
    ]);
}
?>
