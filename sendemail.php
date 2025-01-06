<?php
// Allow CORS (Cross-Origin Resource Sharing) for testing purposes
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the raw POST data
    $data = json_decode(file_get_contents("php://input"));

    // Check if the necessary fields are provided
    if (isset($data->recipient) && isset($data->message)) {
        $recipientEmail = $data->recipient;
        $subject = isset($data->subject) ? $data->subject : "Task Management";  // Default subject if not provided
        $messageContent = $data->message;

        // SendGrid API key and URL
        $apiKey = 'yourAPI key';
        $url = 'https://api.sendgrid.com/v3/mail/send';

        // Prepare email data
        $emailData = [
            "personalizations" => [
                ["to" => [["email" => $recipientEmail]]]
            ],
            "from" => ["email" => "Your@gmail.com"],
            "subject" => $subject,
            "content" => [
                ["type" => "text/plain", "value" => $messageContent]
            ]
        ];

        // Send email using cURL
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $apiKey",
            "Content-Type: application/json"
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($emailData));

        $response = curl_exec($ch);
        if ($response === false) {
            // Return error if email sending fails
            echo json_encode(['status' => 'error', 'message' => curl_error($ch)]);
        } else {
            // Return success response
            echo json_encode(['status' => 'success', 'message' => 'Email sent successfully']);
        }
        curl_close($ch);
    } else {
        // Missing required fields
        echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
    }
} else {
    // Method not allowed
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>
