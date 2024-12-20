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
use App\Utils;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
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
            'mot_de_passe' => ['required', 'string', 'min:6']
        ]);

        $tokenVerification = Utils::generateToken();
        Cache::put(self::TEMPORARY_USER_KEY . $tokenVerification, [
            'email' => $validatedData['email'],
            'nom' => $validatedData['nom'],
            'prenom' => $validatedData['prenom'],
            'date_naissance' => $validatedData['date_naissance'],
            'mot_de_passe' => Hash::make($validatedData['mot_de_passe'])
        ], 120);

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
        if (!$utilisateurTemporaire)
            return (new ErrorResponseContent(Response::HTTP_NOT_FOUND, 'Token de vérification invalide'))
                ->createJsonResponse();

        Utilisateur::create([
            'email' => $utilisateurTemporaire['email'],
            'nom' => $utilisateurTemporaire['nom'],
            'prenom' => $utilisateurTemporaire['prenom'],
            'date_naissance' => $utilisateurTemporaire['date_naissance'],
            'mot_de_passe' => $utilisateurTemporaire['mot_de_passe']
        ]);

        return (new SuccessResponseContent((int)null, 'Votre email a été vérifié. Votre compte a été créé avec succès'))
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
     */
    public function connexion(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'email' => ['required', 'email'],
            'mot_de_passe' => 'required'
        ]);
        $utilisateur = Utilisateur::where('email', $validatedData['email'])->first();

        $nombreTentative = Cache::get('nombre_tentative');
        if ($utilisateur->tentatives_connexion == $nombreTentative) {
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

        if (!$utilisateur || !Hash::check($validatedData['mot_de_passe'], $utilisateur->mot_de_passe)) {
            $utilisateur->tentatives_connexion++;
            $utilisateur->save();

            return (new ErrorResponseContent(Response::HTTP_NOT_FOUND,
                'Ces informations d\'identification ne correspondent pas à nos enregistrements.'))
                ->createJsonResponse();
        }

        $pin = Utils::generateCodePin();
        $dureeViePin = Cache::get('duree_vie_pin');
        CodePin::create([
            "valeur" => $pin,
            "date_heure_expiration" => Carbon::now()->addSeconds($dureeViePin),
            "id_utilisateur" => $utilisateur->id
        ]);
        Mail::to($utilisateur->email)->send(new AuthMultiFacteur($utilisateur->nom, str_split($pin)));

        return (new SuccessResponseContent(Response::HTTP_CREATED, "code de validation envoyer a " . $utilisateur->email))
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
        $codePin = CodePin::where('id_utilisateur', $utilisateur->id)
            ->orderBy('date_heure_expiration', 'desc')
            ->first();
        $expiration = Carbon::parse($codePin->date_heure_expiration);

        if (!$utilisateur || $codePin->valeur !== $validatedData['code_pin'] || $expiration->isBefore(Carbon::now())) {
            return (new ErrorResponseContent(Response::HTTP_UNAUTHORIZED, 'Code Pin de vérification invalide ou expiré'))
                ->createJsonResponse();
        }
        // cree un token de reinitialisation
        $token = Utils::generateToken();

        $dureeVieToken = Cache::get('duree_vie_token');

        $tokenData = [
            "valeur" => $token,
            "date_heure_creation" => Carbon::now(),
            "date_heure_expiration" => Carbon::now()->addSeconds($dureeVieToken),
            "id_utilisateur" => $utilisateur->id
        ];
        Token::create($tokenData);
        return (new SuccessResponseContent(Response::HTTP_OK, 'Utilisateur authentifié avec succès', ["token" => $token]))
            ->createJsonResponse();

    }

    /**
     * @OA\Post(
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
}
