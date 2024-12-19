<?php

namespace App\Http\Controllers;

use App\Http\Responses\ErrorResponseContent;
use App\Http\Responses\SuccessResponseContent;
use App\Models\Utilisateur;
use App\Utils;
use Cache;
use Hash;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    private const TEMPORARY_USER_KEY = 'temp_user_';

    public function inscription(Request $request)
    {
        $validatedData = $request->validate([
            'email'          => ['required', 'string', 'email', 'max:75', 'unique:utilisateur'],
            'nom'            => ['required', 'string', 'max:75'],
            'prenom'         => ['required', 'string', 'max:75'],
            'date_naissance' => ['required', 'date'],
            'mot_de_passe'   => ['required', 'string', 'min:6']
        ]);

        Cache::put(self::TEMPORARY_USER_KEY . Utils::generateToken(), [
            'email'          => $validatedData['email'],
            'nom'            => $validatedData['nom'],
            'prenom'         => $validatedData['prenom'],
            'date_naissance' => $validatedData['date_naissance'],
            'mot_de_passe'   => Hash::make($validatedData['mot_de_passe'])
        ], /* TODO: Paramétrable depuis la configuration */60);

        // Envoi d'email

        return (new SuccessResponseContent(Response::HTTP_CREATED, 'Un email de vérification vous a été envoyé'))
            ->createJsonResponse();
    }

    public function verificationEmail(string $tokenVerification)
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

    public function connexion(Request $request)
    {

    }
}
