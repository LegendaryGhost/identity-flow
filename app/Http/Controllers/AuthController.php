<?php

namespace App\Http\Controllers;

use App\Http\Responses\ErrorResponseContent;
use App\Http\Responses\SuccessResponseContent;
use App\Mail\AuthMultiFacteur;
use App\Mail\ResetTentative;
use App\Mail\ValidationInscription;
use App\Models\CodePin;
use App\Models\Token;
use App\Models\Utilisateur;
use App\Services\ApiService;
use App\Utils;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="API Documentation",
 *     description="Documentation pour votre API"
 * )
 */
class AuthController extends Controller
{
    private const TEMPORARY_USER_KEY = 'temp_user_';
    private ApiService $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * @OA\Post(
     *     path="/api/auth/inscription",
     *     summary="Inscription d'un utilisateur",
     *     description="Permet à un utilisateur de s'inscrire en fournissant les informations nécessaires.",
     *     tags={"Inscription"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "nom", "prenom", "date_naissance", "mot_de_passe"},
     *             @OA\Property(property="email", type="string", format="email", example="sarobidyraza101@gmail.com"),
     *             @OA\Property(property="nom", type="string", example="Dupont"),
     *             @OA\Property(property="prenom", type="string", example="Jean"),
     *             @OA\Property(property="date_naissance", type="string", format="date", example="1990-01-01"),
     *             @OA\Property(property="mot_de_passe", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Inscription réussie, un email de vérification a été envoyé."
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Données invalides."
     *     ),
     * @OA\Response(
     *         response=500,
     *         description="Erreur interne du serveur."
     *     )
     * ),
     * @OA\Response(
     *         response=404,
     *         description="Page non trouvée."
     *     )
     * ),
     */
    public function inscription(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'email' => ['required', 'string', 'email', 'max:75', 'unique:utilisateur'],
            'nom' => ['required', 'string', 'max:75'],
            'prenom' => ['required', 'string', 'max:75'],
            'date_naissance' => ['required', 'date'],
            'mot_de_passe' => ['required', 'string', 'min:6'],
            'pdp' => ['string']
        ]);

        $tokenVerification = Utils::generateToken();
        $dureeInscritpion = Cache::get("duree_vie_inscription");
        Cache::put(self::TEMPORARY_USER_KEY . $tokenVerification, [
            'email' => $validatedData['email'],
            'nom' => $validatedData['nom'],
            'prenom' => $validatedData['prenom'],
            'date_naissance' => $validatedData['date_naissance'],
            'mot_de_passe' => $validatedData['mot_de_passe'],
            'pdp' => $validatedData['pdp'] ?? ''
        ], $dureeInscritpion);

        Mail::to($validatedData['email'])->send(
            new ValidationInscription($validatedData['nom'],
                url('http://localhost:8000/api/auth/verification-email/' . $tokenVerification))
        );

        return (new SuccessResponseContent(Response::HTTP_CREATED, 'Un email de vérification vous a été envoyé'))
            ->createJsonResponse();
    }

    /**
     * @OA\Get(
     *     path="/api/auth/verification-email/{tokenVerification}",
     *     summary="Vérifie le token d'e-mail",
     *     description="Valide l'inscription de l'utilisateur avec un token fourni par e-mail.",
     *     tags={"Inscription"},
     *     @OA\Parameter(
     *         name="tokenVerification",
     *         in="path",
     *         required=true,
     *         description="Token de vérification envoyé par e-mail",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Utilisateur vérifié avec succès."
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Token de vérification invalide.",
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erreur interne du serveur."
     *     )
     * )
     */
    public function verificationEmail(string $tokenVerification): JsonResponse
    {
        $utilisateurTemporaire = Cache::pull(self::TEMPORARY_USER_KEY . $tokenVerification);
        if (!$utilisateurTemporaire) {
            return (new ErrorResponseContent(Response::HTTP_NOT_FOUND, 'Token de vérification invalide'))
                ->createJsonResponse();
        }

        list($statusCode, $reponseFirebase) = $this->creerUtilisateurFirebase($utilisateurTemporaire);
        if ($statusCode !== 200 || $reponseFirebase === null || isset($reponseFirebase['error'])) {
            return (new ErrorResponseContent(Response::HTTP_INTERNAL_SERVER_ERROR, "Erreur Firebase: " . ($reponseFirebase['error']['message'] ?? 'Erreur inconnue')))
                ->createJsonResponse();
        }

        list($firestoreStatusCode, $firestoreReponse) = $this->creerProfilFireStore($reponseFirebase, $utilisateurTemporaire);
        if ($firestoreStatusCode !== 200) {
            return (new ErrorResponseContent(Response::HTTP_INTERNAL_SERVER_ERROR, "Erreur Firestore: " . ($firestoreReponse['error']['message'] ?? 'Erreur inconnue')))
                ->createJsonResponse();
        }

        $this->creerUtilisateurIdentityFlow($utilisateurTemporaire, $reponseFirebase['localId']);

        return (new SuccessResponseContent(Response::HTTP_CREATED, 'Votre email a été vérifié. Votre compte a été créé avec succès'))
            ->createJsonResponse();
    }

    /**
     * @param mixed $nouvelUtilisateur
     * @param $localId
     * @return Utilisateur utilisateur créé
     */
    public function creerUtilisateurIdentityFlow(mixed $nouvelUtilisateur, $localId): Utilisateur
    {
        return Utilisateur::create([
            'id' => $localId,
            'email' => $nouvelUtilisateur['email'],
            'nom' => $nouvelUtilisateur['nom'] ?? '',
            'prenom' => $nouvelUtilisateur['prenom'] ?? '',
            'date_naissance' => $nouvelUtilisateur['date_naissance'] ?? Carbon::now()->format('Y-m-d'),
            'mot_de_passe' => Hash::make($nouvelUtilisateur['mot_de_passe'])
        ]);
    }

    /**
     * Crée un nouveau profil dans Firestore avec l'uid comme identifiant
     *
     * @param mixed $reponseFirebase Réponse de l'authentification Firebase
     * @param mixed $utilisateurTemporaire Données temporaires de l'utilisateur
     * @return array Tableau contenant le code d'état et la réponse Firestore
     */
    public function creerProfilFireStore(mixed $reponseFirebase, mixed $utilisateurTemporaire): array
    {
        // Utilisation du uid comme identifiant unique pour le document
        $firestoreId = $reponseFirebase['localId'];
        $firestoreUrl = "https://firestore.googleapis.com/v1/projects/" .
            Cache::get('firebase_app_id') .
            "/databases/(default)/documents/profil/$firestoreId";

        // Headers d'authentification
        $firestoreHeaders = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $reponseFirebase['idToken']
        ];

        // Données du profil avec les champs requis
        $profilData = [
            'fields' => [
                'dateNaissance' => [
                    'stringValue' => Carbon::parse($utilisateurTemporaire['date_naissance'])
                        ->format('Y-m-d\TH:i:s.v\Z')
                ],
                'fondsActuel' => [
                    'integerValue' => 0
                ],
                'nom' => [
                    'stringValue' => $utilisateurTemporaire['nom']
                ],
                'prenom' => [
                    'stringValue' => $utilisateurTemporaire['prenom']
                ],
                'pdp' => [
                    'stringValue' => $utilisateurTemporaire['pdp']
                ],
                'expoPushToken' => [
                    'stringValue' => ''
                ]
            ]
        ];

        // Configuration de la requête HTTP avec timeout
        $firestoreOptions = [
            'http' => [
                'method' => 'PATCH',
                'content' => json_encode($profilData),
                'header' => $firestoreHeaders,
                'ignore_errors' => false,
                'timeout' => 60
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

            $responseArray = json_decode($firestoreResponse, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception("Erreur de parsing JSON: " . json_last_error_msg());
            }

            return [http_response_code(), $responseArray];
        } catch (\Exception $e) {
            Log::error("Erreur Firestore - URL: $firestoreUrl - Message: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * @param mixed $utilisateurTemporaire
     * @return array
     */
    public function creerUtilisateurFirebase(mixed $utilisateurTemporaire): array
    {
        $url = "https://identitytoolkit.googleapis.com/v1/accounts:signUp?key=" . Cache::get('firebase_key');

        $donnees = [
            'email' => $utilisateurTemporaire['email'],
            'password' => $utilisateurTemporaire['mot_de_passe'],
            'returnSecureToken' => true
        ];

        $headers = [
            'Content-Type: application/json',
        ];

        $jsonDonnees = json_encode($donnees);

        $options = [
            'http' => [
                'method' => 'POST',
                'content' => $jsonDonnees,
                'header' => $headers,
                'ignore_errors' => true
            ]
        ];

        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);

        $statusCode = http_response_code();
        $reponseFirebase = json_decode($response, true);
        return array($statusCode, $reponseFirebase);
    }

    private function envoyerMailReinitialisation(Utilisateur $utilisateur): JsonResponse
    {
        $dureeVieTentative = Cache::get('duree_vie_tentative');
        // cree un token de reinitialisation
        $token_tentative = Utils::generateToken();
        Cache::put("reinitialisation_tentative_{$utilisateur->email}", $token_tentative, Carbon::now()->addSeconds($dureeVieTentative));
        $restLink = url("http://127.0.0.1:8000/api/auth/reinitialisation-tentative?email={$utilisateur->email}&token={$token_tentative}");
        Mail::to($utilisateur->email)->send(new ResetTentative($utilisateur->nom, $restLink));
        return (new ErrorResponseContent(Response::HTTP_LOCKED,
            'Votre compte est verrouillé. Vérifiez vos e-mails pour le déverrouiller.'))
            ->createJsonResponse();
    }

    /**
     * @OA\Post(
     *     path="/api/auth/connexion",
     *     summary="Connexion utilisateur",
     *     description="Authentifie un utilisateur avec un e-mail et un mot de passe.",
     *     tags={"Authentification"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "mot_de_passe"},
     *             @OA\Property(property="email", type="string", format="email", example="sarobidyraza101@gmail.com"),
     *             @OA\Property(property="mot_de_passe", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Code de validation envoyé à l'utilisateur."
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Identifiants incorrects ou utilisateur non trouvé.",
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erreur interne du serveur."
     *     )
     * )
     * @throws \Exception
     */
    public function connexion(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'email' => ['required', 'email'],
            'mot_de_passe' => 'required'
        ]);

        $url = "https://identitytoolkit.googleapis.com/v1/accounts:signInWithPassword?key=" . Cache::get('firebase_key');

        $donnees = [
            'email' => $validatedData['email'],
            'password' => $validatedData['mot_de_passe'],
            'returnSecureToken' => true
        ];

        $headers = [
            'Content-Type: application/json',
        ];

        $jsonDonnees = json_encode($donnees);

        $options = [
            'http' => [
                'method' => 'POST',
                'content' => $jsonDonnees,
                'header' => $headers,
                'ignore_errors' => true
            ]
        ];

        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);

        $statusCode = http_response_code();
        $reponseFirebase = json_decode($response, true);

        $utilisateur = Utilisateur::where('email', $validatedData['email'])->first();

        if ($statusCode !== Response::HTTP_OK) {
            if ($utilisateur) {
                $utilisateur->tentatives_connexion++;
                $utilisateur->save();
            }

            return (new ErrorResponseContent(Response::HTTP_NOT_FOUND,
                'Ces informations d\'identification ne sont pas valides.'))
                ->createJsonResponse();
        }

        $tokenFirebase = $reponseFirebase['idToken'] ?? null;
        $refreshToken = $reponseFirebase['refreshToken'] ?? null;
        $expirationDate = $reponseFirebase['expiresIn'] ?? null;

        // Vérification des informations reçues
        if (!$tokenFirebase || !$refreshToken || !$expirationDate) {
            return (new ErrorResponseContent(
                Response::HTTP_UNAUTHORIZED,
                'Ces informations d\'identification ne sont pas valides.'
            ))->createJsonResponse();
        }

        if (!$utilisateur) {
            $utilisateur = $this->apiService->recupererInfoFirebase($tokenFirebase);
            $utilisateur = $this->creerUtilisateurIdentityFlow(
                $utilisateur,
                $utilisateur['id']
            );
        }

        $nombreTentative = Cache::get('nombre_tentative');
        if ($utilisateur->tentatives_connexion == $nombreTentative) {
            return $this->envoyerMailReinitialisation($utilisateur);
        }

        $pin = Utils::generateCodePin();
        $dureeViePin = Cache::get('duree_vie_pin');
        CodePin::create([
            "valeur" => $pin,
            "date_heure_expiration" => Carbon::now()->addSeconds($dureeViePin),
            "id_utilisateur" => $utilisateur->id
        ]);
        Mail::to($utilisateur->email)->send(new AuthMultiFacteur($utilisateur->nom, str_split($pin)));

        // Créer un token
        $dureeVieToken = Cache::get('duree_vie_token');
        $tokenData = [
            "valeur" => $tokenFirebase,
            "date_heure_creation" => Carbon::now(),
            "date_heure_expiration" => Carbon::now()->addSeconds($dureeVieToken),
            "id_utilisateur" => $utilisateur->id
        ];
        Token::create($tokenData);

        return (new SuccessResponseContent(Response::HTTP_CREATED, "code de validation envoyer à " . $utilisateur->email))
            ->createJsonResponse();
    }

    /**
     * @OA\Post(
     *     path="/api/auth/verification-pin",
     *     summary="Vérifie le code PIN",
     *     description="Valide l'utilisateur avec un code PIN envoyé par e-mail.",
     *     tags={"Authentification"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "code_pin"},
     *             @OA\Property(property="email", type="string", format="email", example="sarobidyraza101@gmail.com"),
     *             @OA\Property(property="code_pin", type="string", example="123456")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Utilisateur authentifié avec succès."
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Code PIN invalide ou expiré.",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Utilisateur ou code PIN non trouvé.",
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erreur interne du serveur."
     *     )
     * )
     */
    public function verificationPin(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'email' => ['required', 'email'],
            'code_pin' => ['required', 'string'],
        ]);
        $utilisateur = Utilisateur::where('email', $validatedData['email'])->first();
        if (!$utilisateur) {
            return (new ErrorResponseContent(Response::HTTP_NOT_FOUND, "L'email n'est associé à aucun utilisateur."))
                ->createJsonResponse();
        }

        $codePin = CodePin::where('id_utilisateur', $utilisateur->id)
            ->orderBy('date_heure_expiration', 'desc')
            ->first();
        $expiration = Carbon::parse($codePin->date_heure_expiration);

        $nombreTentative = Cache::get('nombre_tentative');
        if ($utilisateur->tentatives_connexion == $nombreTentative) {
            return $this->envoyerMailReinitialisation($utilisateur);
        }

        if ($codePin->valeur !== $validatedData['code_pin']) {
            $utilisateur->tentatives_connexion++;
            $utilisateur->save();

            return (new ErrorResponseContent(Response::HTTP_UNAUTHORIZED, 'Code Pin de vérification invalide'))
                ->createJsonResponse();
        }

        if ($expiration->isBefore(Carbon::now())) {
            return (new ErrorResponseContent(Response::HTTP_UNAUTHORIZED, 'Code Pin de vérification expiré'))
                ->createJsonResponse();
        }

        $token = Token::where('id_utilisateur', $codePin['id_utilisateur'])
            ->latest('date_heure_expiration')
            ->first();
        $utilisateur->tentatives_connexion = 0;
        $utilisateur->save();

        return (new SuccessResponseContent(Response::HTTP_OK, 'Utilisateur authentifié avec succès', ["token" => $token["valeur"]]))
            ->createJsonResponse();
    }

    /**
     * @OA\Get(
     *     path="/api/auth/reinitialisation-tentative",
     *     summary="Réinitialise les tentatives de connexion",
     *     description="Réinitialise les tentatives de connexion après une vérification par e-mail.",
     *     tags={"Authentification"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "token"},
     *             @OA\Property(property="email", type="string", format="email", example="sarobidyraza101@gmail.com"),
     *             @OA\Property(property="token", type="string", example="resetToken123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tentatives de connexion réinitialisées avec succès."
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Token invalide ou expiré."
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Utilisateur non trouvé.",
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erreur interne du serveur."
     *     )
     * )
     */
    public function reinitialisationTentative(Request $request): JsonResponse
    {
        $email = $request->input('email');
        $token = $request->input('token');

        $cachedToken = Cache::get("reinitialisation_tentative_{$email}");
        if (!$cachedToken || $cachedToken !== $token) {
            return (new ErrorResponseContent(Response::HTTP_BAD_REQUEST, 'Token invalide ou expiré'))
                ->createJsonResponse();
        }

        $utilisateur = Utilisateur::where('email', $email)->first();
        if ($utilisateur) {
            $utilisateur->tentatives_connexion = 0;
            $utilisateur->save();
            Cache::forget("reinitialisation_tentative_{$email}");
            return (new SuccessResponseContent(Response::HTTP_OK, 'Tentatives de connexion réinitialisées.'))
                ->createJsonResponse();
        }
        return (new ErrorResponseContent(Response::HTTP_NOT_FOUND, 'Utilisateur non trouvé'))
            ->createJsonResponse();
    }

    public function deconnexion($token): JsonResponse|ErrorResponseContent
    {
        $tokenModel = Token::where("valeur",$token)->first();
        if (!$tokenModel ){
            return (new ErrorResponseContent(Response::HTTP_ACCEPTED,"vous etes deconnecter"));
        }
        $carbonExpirationToken = Carbon::parse($tokenModel->date_heure_expiration);
        if ($carbonExpirationToken->isBefore(Carbon::now())){
            return (new ErrorResponseContent(Response::HTTP_ACCEPTED,"vous etes deconnecter"));
        }
        $tokenModel->date_heure_expiration = Carbon::now();
        $tokenModel->save();
        return (new SuccessResponseContent(Response::HTTP_OK,"vous etes deconnecter"))
            ->createJsonResponse();
    }
}
