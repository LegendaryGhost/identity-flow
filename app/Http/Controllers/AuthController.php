<?php

namespace App\Http\Controllers;

use App\Models\Token;
use App\Models\Utilisateur;
use App\Models\UtilisateurTemporaire;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Str;

class AuthController extends Controller
{
    public function inscription(Request $request)
    {
        $validatedData = $request->validate([
            'email'          => ['required', 'string', 'email', 'max:75', 'unique:utilisateur'],
            'nom'            => ['required', 'string', 'max:75'],
            'prenom'         => ['required', 'string', 'max:75'],
            'date_naissance' => ['required', 'date'],
            'mot_de_passe'   => ['required', 'string', 'min:6']
        ]);

        $tokenVerification = Str::random(60);
        UtilisateurTemporaire::create([
            'email'              => $validatedData['email'],
            'nom'                => $validatedData['nom'],
            'prenom'             => $validatedData['prenom'],
            'date_naissance'     => $validatedData['date_naissance'],
            'mot_de_passe'       => bcrypt($validatedData['mot_de_passe']),
            'token_verification' => $tokenVerification
        ]);

        // Envoi d'email

        return response()->json([
            'status_code' => 201,
            'status'      => 'created',
            'message'     => 'Un email de vérification vous a été envoyé'
        ], 201);
    }

    public function verificationEmail(string $tokenVerification)
    {
        $utilisateurTemporaire = UtilisateurTemporaire::where('token_verification', $tokenVerification)->first();
        if (!$utilisateurTemporaire)
            return response()->json([
                'status_code' => 404,
                'status'      => 'not found',
                'message'     => 'Token de vérification invalide'
            ], 404);

        $utilisateur = Utilisateur::create([
            'email' => $utilisateurTemporaire->email,
            'nom' => $utilisateurTemporaire->nom,
            'prenom' => $utilisateurTemporaire->prenom,
            'date_naissance' => $utilisateurTemporaire->date_naissance,
            'mot_de_passe' => $utilisateurTemporaire->mot_de_passe
        ]);
        $utilisateurTemporaire->delete();

        $valeurToken = Str::random(60);
        $token = Token::create([
            'valeur'         => hash('sha256', $valeurToken),
            'utilisateur_id' => $utilisateur->id,
        ]);

        return response()->json([
            'status_code' => 200,
            'status'      => 'success',
            'message'     => 'Votre email a été vérifié. Votre compte a été créé avec succès',
            'data'        => ['token' => $token]
        ]);
    }

    public function connexion(Request $request)
    {

    }
}
