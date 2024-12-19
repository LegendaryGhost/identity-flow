<?php

namespace App\Http\Controllers;

use App\Mail\AuthMultiFacteur;
use App\Mail\ResetTentative;
use App\Mail\ValidationInscription;
use App\Models\Token;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

use App\Models\CodePin;
use App\Models\Utilisateur;
use App\Http\Responses\ErrorResponseContent;
use App\Http\Responses\SuccessResponseContent;
use App\Utils;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    private const TEMPORARY_USER_KEY = 'temp_user_';

    public function inscription(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'email'          => ['required', 'string', 'email', 'max:75', 'unique:utilisateur'],
            'nom'            => ['required', 'string', 'max:75'],
            'prenom'         => ['required', 'string', 'max:75'],
            'date_naissance' => ['required', 'date'],
            'mot_de_passe'   => ['required', 'string', 'min:6']
        ]);

        $tokenVerification =  Utils::generateToken();
        Cache::put(self::TEMPORARY_USER_KEY .$tokenVerification, [
            'email'          => $validatedData['email'],
            'nom'            => $validatedData['nom'],
            'prenom'         => $validatedData['prenom'],
            'date_naissance' => $validatedData['date_naissance'],
            'mot_de_passe'   => Hash::make($validatedData['mot_de_passe'])
        ], 120);

        $linkValidation = url("http://localhost:8000/api/auth/verification-email/".$tokenVerification);
        Mail::to($validatedData['email'])->send(new ValidationInscription($validatedData['nom'],$linkValidation));

        return (new SuccessResponseContent(Response::HTTP_CREATED, 'Un email de vérification vous a été envoyé'))
            ->createJsonResponse();
    }

    public function verificationEmail(string $tokenVerification): JsonResponse
    {
        $utilisateurTemporaire = Cache::pull(self::TEMPORARY_USER_KEY . $tokenVerification);
        if (!$utilisateurTemporaire)
            return (new ErrorResponseContent(Response::HTTP_NOT_FOUND, 'Token de vérification invalide'))
                ->createJsonResponse();

        Utilisateur::create([
            'email'          => $utilisateurTemporaire['email'],
            'nom'            => $utilisateurTemporaire['nom'],
            'prenom'         => $utilisateurTemporaire['prenom'],
            'date_naissance' => $utilisateurTemporaire['date_naissance'],
            'mot_de_passe'   => $utilisateurTemporaire['mot_de_passe']
        ]);

        return (new SuccessResponseContent((int) null,'Votre email a été vérifié. Votre compte a été créé avec succès'))
            ->createJsonResponse();
    }

    public function connexion(Request $request): JsonResponse
    {
        $email = $request['email'];
        $request->validate([
            'email' => ['required','email'],
            'mot_de_passe' => 'required'
        ]);
        $utilisateur = Utilisateur::where('email', $email)->first();
        $nombreTentative = Cache::get('nombre_tentative');
        if ($utilisateur->tentatives_connexion == $nombreTentative) {
            $dureeVieTentaive = Cache::get('duree_vie_tentative');
            // cree un token de reinitialisation
            $token_tentative = Utils::generateToken();
            Cache::put("reinitialisation_tentative_{$utilisateur->email}", $token_tentative, Carbon::now()->addSeconds($dureeVieTentaive));
            $restLink = url("http://127.0.0.1:8000/api/auth/reinitialisation-tentative?email={$utilisateur->email}&token={$token_tentative}");
            Mail::to($utilisateur->email)->send(new ResetTentative($utilisateur->nom,$restLink));
            return (new ErrorResponseContent(Response::HTTP_LOCKED,
                'Votre compte est verrouillé. Vérifiez vos e-mails pour le déverrouiller.'))
                ->createJsonResponse();
        }
        if (!$utilisateur || !Hash::check($request["mot_de_passe"], $utilisateur->mot_de_passe)) {
            $utilisateur->tentatives_connexion++;
            $utilisateur->save();
            return (new ErrorResponseContent(Response::HTTP_NOT_FOUND,
                'Ces informations d\'identification ne correspondent pas à nos enregistrements.'))
                ->createJsonResponse();
        }
        $pin = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $dureeViePin = Cache::get('duree_vie_pin');
        $codePin = [
            "valeur" => $pin,
            "date_heure_expiration" => Carbon::now()->addSeconds($dureeViePin),
            "id_utilisateur" => $utilisateur->id
        ];
        CodePin::create($codePin);
        Mail::to($utilisateur->email)->send(new AuthMultiFacteur($utilisateur->nom, str_split($pin)));
        return (new SuccessResponseContent(Response::HTTP_CREATED, "code de validation envoyer a " . $utilisateur->email))
            ->createJsonResponse();
    }

    public function verificationPin(Request $request): JsonResponse
    {
        $email = $request['email'];
        $pinValeur = $request['code_pin'];
        $request->validate([
            'email' => ['required','email'],
            'code_pin' => 'required|string',
        ]);
        $utilisateur = Utilisateur::where('email', $email)->first();
        $codePin = CodePin::where('id_utilisateur', $utilisateur->id)
            ->orderBy('date_heure_expiration', 'desc')
            ->first();
        $expiration = Carbon::parse($codePin->date_heure_expiration);

        if (!$utilisateur || $codePin->valeur !== $pinValeur ||  $expiration->isBefore(Carbon::now())) {
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
        return (new SuccessResponseContent(Response::HTTP_OK, 'Utilisateur authentifié avec succès',["token"=>$token]))
            ->createJsonResponse();

    }

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
