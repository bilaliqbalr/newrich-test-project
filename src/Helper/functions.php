<?php

function handleApiRequest() {
    $requestMethod = $_SERVER['REQUEST_METHOD'];
    $requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    if (!in_array($requestMethod, ['GET', 'POST', 'PUT', 'DELETE'])) {
        http_response_code(405); // Method Not Allowed
        echo "Unsupported request method: " . $requestMethod;
        return;
    }

    switch ($requestMethod) {
        case 'GET':
            // Handle GET requests (e.g., retrieve data)
            // ... your logic for GET requests ...
            break;
        case 'POST':
            // Handle POST requests (e.g., create data)
            // ... your logic for POST requests ...
            break;
        case 'PUT':
            // Handle PUT requests (e.g., update data)
            // ... your logic for PUT requests ...
            break;
        case 'DELETE':
            // Handle DELETE requests (e.g., delete data)
            // ... your logic for DELETE requests ...
            break;
    }

    http_response_code(200); // OK
    echo "API request processed successfully!";
}

