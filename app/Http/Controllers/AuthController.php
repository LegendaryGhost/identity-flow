<?php

namespace App\Http\Controllers;

use App\Mail\AuthMultiFacteur;
use App\Models\Configuration;
use App\Models\Token;
use App\Utils\Tools;
use Illuminate\Support\Carbon;

use App\Models\CodePin;
use App\Models\Utilisateur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

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
            'email' => 'required|email',
            'mot_de_passe' => 'required'
        ]);

        $utilisateur = Utilisateur::where('email', $email)->first();
        //!Hash::check($request["mot_de_passe"], $utilisateur->motDePasse)
        if (!$utilisateur) {
            return response([
                'message' => ['Ces informations d\'identification ne correspondent pas à nos enregistrements.']
            ], 404);
        }
        $pin = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $configuration = Configuration::where("cle", "duree_vie_pin")->first();
        $codePin = [
            "valeur" => $pin,
            "date_heure_expiration" => Carbon::now()->addSeconds($configuration->valeur),
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
            'email' => 'required|email',
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
        $token = Tools::tokenGenerateur();

        $configuration = Configuration::where("cle", "duree_vie_token")->first();

        $tokenData = [
            "valeur" => $token,
            "date_heure_creation" => Carbon::now(),
            "date_heure_expiration" => Carbon::now()->addSeconds($configuration->valeur),
            "id_utilisateur" => $utilisateur->id
        ];
        Token::create($tokenData);
        return response([
            'message' => 'utilisateur authentifiée avec succes'
        ], 200);
    }
}
