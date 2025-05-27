<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google\Cloud\Dialogflow\V2\SessionsClient;
use Google\Cloud\Dialogflow\V2\QueryInput;
use Google\Cloud\Dialogflow\V2\TextInput;

class ChatbotController extends Controller
{
    public function handleRequest(Request $request)
    {
        // Verificar si la variable de entorno está configurada
        $credentialsPath = getenv('GOOGLE_APPLICATION_CREDENTIALS');
        if (!$credentialsPath) {
            return response()->json(['error' => 'GOOGLE_APPLICATION_CREDENTIALS environment variable is not set.']);
        }
        if (!file_exists($credentialsPath)) {
            return response()->json(['error' => 'Credentials file not found at ' . $credentialsPath]);
        }
        // Asegurarse de que el mensaje sea una cadena
        $userMessage = $request->input('message');
        if (!is_string($userMessage)) {
            return response()->json(['error' => 'Invalid input, message should be a string.']);
        }
        
        $userMessage = $request->input('message');
        $response = $this->detectIntent($userMessage);
        return response()->json(['response' => $response]);
    }

    private function detectIntent($text)
    {
        $projectId = env('DIALOGFLOW_PROJECT_ID');
        $sessionId = uniqid();
        $languageCode = 'es';

        $sessionsClient = new SessionsClient();
        $session = $sessionsClient->sessionName($projectId, $sessionId);

        $textInput = new TextInput();
        $textInput->setText($text);
        $textInput->setLanguageCode($languageCode);

        $queryInput = new QueryInput();
        $queryInput->setText($textInput);

        $response = $sessionsClient->detectIntent($session, $queryInput);
        $queryResult = $response->getQueryResult();

        $sessionsClient->close();

        return $queryResult->getFulfillmentText();
    }
}
