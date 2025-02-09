<?php

namespace App\Services;

use App\Models\Utilisateur;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ApiService
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 30,
            'verify' => true,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ]
        ]);
    }

    /**
     * Effectue une requête HTTP générique
     *
     * @param string $url L'URL à appeler
     * @param string $method La méthode HTTP (GET, POST, PUT, DELETE, etc.)
     * @param array $data Les données à envoyer
     * @param array $headers Les headers supplémentaires
     * @param array $options Les options de requête supplémentaires
     *
     * @return array Un tableau contenant le code de statut et la réponse
     */
    public function envoyerRequete(
        string $url,
        string $method = 'GET',
        array $data = [],
        array $headers = [],
        array $options = []
    ): array {
        try {
            $options = array_merge($options, [
                'headers' => array_merge([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ], $headers),
                'json' => $data
            ]);

            $response = $this->client->request($method, $url, $options);

            return [
                'status' => $response->getStatusCode(),
                'data' => json_decode($response->getBody()->getContents(), true) ?? []
            ];

        } catch (RequestException $e) {
            $statusCode = $e->getResponse() ? $e->getResponse()->getStatusCode() : 500;
            $message = $e->getMessage();

            Log::error("Erreur API - URL: $url - Méthode: $method - Code: $statusCode - Message: $message");

            return [
                'status' => $statusCode,
                'error' => $message
            ];
        } catch (GuzzleException $e) {
            Log::error("Erreur API - URL: $url - Méthode: $method - Exception: " . $e->getMessage());

            return [
                'status' => 500,
                'error' => $e->getMessage()
            ];
        }
    }

    public function recupererInfoFirebase(string $token): ?Utilisateur
    {
        try {
            // 🔹 Récupération des informations utilisateur depuis Firebase Authentication
            $response = $this->envoyerRequete(
                'https://identitytoolkit.googleapis.com/v1/accounts:lookup?key=' . Cache::get('firebase_key'),
                'POST',
                ['idToken' => $token]
            );

            if (isset($response['error'])) {
                throw new \Exception('Impossible de récupérer les informations utilisateur Firebase : ' . PHP_EOL . $response['error']);
            }

            $status = $response['status'];
            $userData = $response['data'];

            if ($status !== 200 || empty($userData['users']) || !isset($userData['users'][0])) {
                throw new \Exception('Impossible de récupérer les informations utilisateur Firebase.');
            }

            $user = $userData['users'][0];
            $localId = $user['localId'] ?? null;
            $email = $user['email'] ?? null;

            if (!$localId) {
                throw new \Exception('Local ID manquant dans la réponse Firebase.');
            }

            $profilData = [];



            /********************* BAD CODE ZONE START *************************/
            $firestoreUrl = "https://firestore.googleapis.com/v1/projects/" .
                Cache::get('firebase_app_id') .
                "/databases/(default)/documents/profil/$localId";

            $firestoreHeaders = [
                'Authorization: Bearer ' . $token
            ];

            $firestoreOptions = [
                'http' => [
                    'method' => 'GET',
                    'header' => $firestoreHeaders,
                    'ignore_errors' => true,
                    'timeout' => 30
                ]
            ];

            // Exécution de la requête avec gestion détaillée des erreurs
            try {
                $firestoreContext = stream_context_create($firestoreOptions);
                $firestoreResponse = file_get_contents($firestoreUrl, false, $firestoreContext);

                if ($firestoreResponse === false) {
                    $error = error_get_last();
                    throw new \Exception("Erreur Firestore: " . ($error['message'] ?? 'Erreur inconnue'));
                }

                $profilData = json_decode($firestoreResponse, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \Exception("Erreur de parsing JSON: " . json_last_error_msg());
                }
            } catch (\Exception $e) {
                Log::error("Erreur Firestore - URL: $firestoreUrl - Message: " . $e->getMessage());
                dd($e->getMessage());
            }
            /********************* BAD CODE ZONE END *************************/



            $fields = $profilData['fields'];

            // 🔹 Vérification et extraction des données du profil
            $nom = $fields['nom']['stringValue'] ?? '';
            $prenom = $fields['prenom']['stringValue'] ?? '';
            $dateNaissance = isset($fields['dateNaissance']['stringValue'])
                ? Carbon::parse($fields['dateNaissance']['stringValue'])->format('Y-m-d')
                : null;
            $pdp = $fields['pdp']['stringValue'] ?? '';

            // 🔹 Création de l'objet Utilisateur
            return new Utilisateur([
                'id' => $localId,
                'email' => $email,
                'nom' => $nom,
                'prenom' => $prenom,
                'date_naissance' => $dateNaissance,
                'pdp' => $pdp,
                'tentatives_connexion' => 0
            ]);

        } catch (\Exception $e) {
            // 🔹 Log détaillé en cas d'erreur
            Log::error('Erreur Firebase:', [
                'message' => $e->getMessage(),
                'token' => substr($token, 0, 20) . '...',
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }
}
