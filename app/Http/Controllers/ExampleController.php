<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="API Documentation",
 *     description="Documentation pour votre API"
 * )
 */
class ExampleController extends Controller
{
    /**
     * @OA\Get(
     *     path="/example",
     *     tags={"Example"},
     *     summary="Exemple de route",
     *     description="Récupérer les données de l'exemple.",
     *     @OA\Response(
     *         response=200,
     *         description="Réponse réussie"
     *     )
     * )
     */
    public function index()
    {
        return response()->json(['message' => 'Hello, Swagger!']);
    }
}
