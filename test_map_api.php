<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Http\Controllers\Api\PublicAlertController;
use Illuminate\Http\Request;

echo "=== TESTING MAP API ENDPOINT ===\n\n";

// Create a controller instance and test the mapData method
$controller = new PublicAlertController();
$request = new Request();

try {
    $response = $controller->mapData($request);
    $responseData = json_decode($response->content(), true);

    echo "Response Status Code: " . $response->status() . "\n";
    echo "Response Success: " . ($responseData['success'] ? 'true' : 'false') . "\n";
    echo "Number of alerts: " . count($responseData['data']) . "\n\n";

    echo "=== ALERT DETAILS ===\n";
    foreach ($responseData['data'] as $index => $alert) {
        echo "Alert " . ($index + 1) . ":\n";
        echo "  ID: " . $alert['id'] . "\n";
        echo "  Title: " . $alert['title'] . "\n";
        echo "  Disease: " . $alert['disease'] . "\n";
        echo "  Municipality: " . $alert['municipality'] . "\n";
        echo "  Alert Level: " . $alert['alert_level'] . "\n";
        echo "  Cases: " . $alert['cases'] . "\n";
        echo "  Coordinates: " . $alert['coordinates']['latitude'] . ", " . $alert['coordinates']['longitude'] . "\n";
        echo "  Date: " . $alert['date'] . "\n";
        echo "  Updated: " . $alert['updated_at'] . "\n";
        echo "  Affected Barangays: " . implode(', ', $alert['affected_barangays']) . "\n";
        echo "  ---\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
