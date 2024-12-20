<?php

namespace App\Http\Controllers;

use App\Http\Responses\SuccessResponseContent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;

class UtilisateurController
{

    /**
     * @OA\Put(
     *     path="/utilisateurs",
     *     summary="Modifier les informations d'un utilisateur",
     *     description="Cette méthode permet de modifier les informations personnelles d'un utilisateur, y compris son nom, prénom, date de naissance et mot de passe.",
     *     tags={"Utilisateur"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nom", "prenom", "date_naissance", "password123"},
     *             @OA\Property(property="nom", type="string", example="Dupont", maxLength=75, description="Dupont"),
     *             @OA\Property(property="prenom", type="string", example="Jean", maxLength=75, description="Jean"),
     *             @OA\Property(property="date_naissance", type="string", format="date", example="1990-01-01"),
     *             @OA\Property(property="mot_de_passe", type="string", format="password", example="password12")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Vos informations ont été mis à jour.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation des données échouée",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Les données envoyées ne sont pas valides."),
     *             @OA\Property(property="errors", type="object", additionalProperties=@OA\Property(type="array", @OA\Items(type="string")))
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non autorisé",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Non autorisé.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erreur interne du serveur.",
     *     )
     *
     * )
     */
    public function modification(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'nom'            => ['string', 'max:75'],
            'prenom'         => ['string', 'max:75'],
            'date_naissance' => ['date'],
            'mot_de_passe'   => ['string', 'min:6']
        ]);

        $utilisateur = $request->get('utilisateur');

        $utilisateur->nom = $validatedData['nom'] ?? $utilisateur->nom;
        $utilisateur->prenom = $validatedData['prenom'] ?? $utilisateur->prenom;
        $utilisateur->date_naissance = $validatedData['date_naissance'] ?? $utilisateur->date_naissance;
        $utilisateur->mot_de_passe = $validatedData['mot_de_passe'] ? Hash::make($validatedData['mot_de_passe']) : $utilisateur->mot_de_passe;

        $utilisateur->save();

        return (new SuccessResponseContent(Response::HTTP_OK, 'Vos informations ont été mis à jour.'))
            ->createJsonResponse();
    }
}
