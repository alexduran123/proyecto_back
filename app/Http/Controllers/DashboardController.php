<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function index(): JsonResponse
    {
        // Estos son los datos que viajarán a tu diseño de Figma en React
        return response()->json([
            'resumen' => [
                'unidades' => 124,
                'alertas_pendientes' => 5, // Tu icono de alerta
                'mensajes_nuevos' => 3    // Tu icono de chat
            ],
            'status' => 'success'
        ]);
    }
}