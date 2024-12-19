<?php

namespace App\Http\Controllers;

use App\Http\Responses\SuccessResponseContent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class UtilisateurController
{

    public function modification(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'nom'            => ['required', 'string', 'max:75'],
            'prenom'         => ['required', 'string', 'max:75'],
            'date_naissance' => ['required', 'date'],
            'mot_de_passe'   => ['required', 'string', 'min:6']
        ]);

        $utilisateur = $request->get('utilisateur');

        $utilisateur->nom = $validatedData['nom'];
        $utilisateur->prenom = $validatedData['prenom'];
        $utilisateur->date_naissance = $validatedData['date_naissance'];
        $utilisateur->mot_de_passe = Hash::make($validatedData['mot_de_passe']);

        $utilisateur->save();

        return (new SuccessResponseContent(Response::HTTP_OK, 'Vos informations ont été mis à jour.'))
            ->createJsonResponse();
    }

}
