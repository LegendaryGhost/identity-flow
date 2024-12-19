<?php

namespace App\Http\Controllers;

use App\Http\Responses\ErrorResponse;
use App\Http\Responses\SuccessResponse;
use App\Models\Token;
use App\Models\Utilisateur;
use App\Models\UtilisateurTemporaire;
use App\Services\TokenService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Str;

class AuthController extends Controller
{
    private readonly TokenService $tokenService;

    public function __construct(TokenService $tokenService)
    {
        $this->tokenService = $tokenService;
    }

    public function inscription(Request $request)
    {
        $validatedData = $request->validate([
            'email'          => ['required', 'string', 'email', 'max:75', 'unique:utilisateur'],
            'nom'            => ['required', 'string', 'max:75'],
            'prenom'         => ['required', 'string', 'max:75'],
            'date_naissance' => ['required', 'date'],
            'mot_de_passe'   => ['required', 'string', 'min:6']
        ]);

        $tokenVerification = $this->tokenService->generateToken();
        UtilisateurTemporaire::create([
            'email'              => $validatedData['email'],
            'nom'                => $validatedData['nom'],
            'prenom'             => $validatedData['prenom'],
            'date_naissance'     => $validatedData['date_naissance'],
            'mot_de_passe'       => bcrypt($validatedData['mot_de_passe']),
            'token_verification' => $tokenVerification
        ]);

        // Envoi d'email

        return (new SuccessResponse(Response::HTTP_CREATED, 'Un email de vérification vous a été envoyé'))
            ->toJson();
    }

    public function verificationEmail(string $tokenVerification)
    {
        $utilisateurTemporaire = UtilisateurTemporaire::where('token_verification', $tokenVerification)->first();
        if (!$utilisateurTemporaire)
            return (new ErrorResponse(Response::HTTP_NOT_FOUND, 'Token de vérification invalide'))->toJson();

        $utilisateur = Utilisateur::create([
            'email'          => $utilisateurTemporaire->email,
            'nom'            => $utilisateurTemporaire->nom,
            'prenom'         => $utilisateurTemporaire->prenom,
            'date_naissance' => $utilisateurTemporaire->date_naissance,
            'mot_de_passe'   => $utilisateurTemporaire->mot_de_passe
        ]);
        $utilisateurTemporaire->delete();

        $valeurToken = $this->tokenService->generateToken();
        $token = Token::create([
            'valeur'         => $this->tokenService->hashToken($valeurToken),
            'utilisateur_id' => $utilisateur->id,
        ]);

        return (new SuccessResponse((int) null,
            'Votre email a été vérifié. Votre compte a été créé avec succès',
            ['token' => $token]))->toJson();
    }

    public function connexion(Request $request)
    {

    }
}
