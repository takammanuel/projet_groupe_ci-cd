<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     title="API Gestion des Abonnés et Factures",
 *     version="1.0.0",
 *     description="API REST sécurisée pour la gestion des abonnés et de leurs factures de consommation d'eau",
 *     @OA\Contact(
 *         email="support@example.com"
 *     )
 * )
 *
 * @OA\Server(
 *     url="http://localhost:8000",
 *     description="Serveur de développement"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="token",
 *     description="Entrez votre token Bearer (obtenu via /api/login ou /api/register)"
 * )
 *
 * @OA\Tag(
 *     name="Authentification",
 *     description="Endpoints pour l'authentification des utilisateurs"
 * )
 *
 * @OA\Tag(
 *     name="Abonnés",
 *     description="Gestion des abonnés au service d'eau"
 * )
 *
 * @OA\Tag(
 *     name="Factures",
 *     description="Gestion des factures de consommation"
 * )
 */
abstract class Controller
{
    //
}
