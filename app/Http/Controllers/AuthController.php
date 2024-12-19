<?php

namespace App\Http\Controllers;

use App\Mail\AuthMultiFacteur;
use App\Mail\ResetTentative;
use App\Models\Token;
use Illuminate\Support\Carbon;

use App\Models\CodePin;
use App\Models\Utilisateur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function register(Request $request)
    {

    }

    // authentification de l'utilisateur
    public function login(Request $request)
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
            $token_tentative = "vñmñkmvklr";
            Cache::put("reinitialisation_tentative_{$utilisateur->email}", $token_tentative, Carbon::now()->addSeconds($dureeVieTentaive));
            $restLink = url("http://127.0.0.1:8000/api/auth/reinitialisation-tentative?email={$utilisateur->email}&token={$token_tentative}");
            Mail::to($utilisateur->email)->send(new ResetTentative($utilisateur->nom,$restLink));
            return response()->json([
                'error' => 'Votre compte est verrouillé. Vérifiez vos e-mails pour le déverrouiller.'
            ], 423);
        }
        //!Hash::check($request["mot_de_passe"], $utilisateur->motDePasse)
        if (!$utilisateur || !($request["mot_de_passe"] == $utilisateur->mot_de_passe)) {
            $utilisateur->tentatives_connexion++;
            $utilisateur->save();
            return response([
                'message' => ['Ces informations d\'identification ne correspondent pas à nos enregistrements.']
            ], 404);
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
        return response([
            'message' => "code de validation envoyer a " . $utilisateur->email
        ], 200);
    }

    public function verificationPin(Request $request)
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
            return response()->json(['error' => 'Invalid or expired PIN'], 401);
        }
        // cree un token de reinitialisation
        $token = "eoifjvñkñvñk";

        $dureeVieToken = Cache::get('duree_vie_token');

        $tokenData = [
            "valeur" => $token,
            "date_heure_creation" => Carbon::now(),
            "date_heure_expiration" => Carbon::now()->addSeconds($dureeVieToken),
            "id_utilisateur" => $utilisateur->id
        ];
        Token::create($tokenData);
        return response([
            'message' => 'utilisateur authentifiée avec succes'
        ], 200);
    }

    public function reinitialisationTentative(Request $request)
    {
        $email = $request->input('email');
        $token = $request->input('token');

        $cachedToken = Cache::get("reinitialisation_tentative_{$email}");
        if (!$cachedToken || $cachedToken !== $token) {
            return response()->json(['message' => 'Token invalide ou expiré.'], 400);
        }

        $utilisateur = Utilisateur::where('email', $email)->first();
        if ($utilisateur) {
            $utilisateur->tentatives_connexion = 0;
            $utilisateur->save();
            Cache::forget("reinitialisation_tentative_{$email}");
            return response()->json(['message' => 'Tentatives de connexion réinitialisées.']);
        }
        return response()->json(['message' => 'Utilisateur non trouvé.'], 404);
    }
}
