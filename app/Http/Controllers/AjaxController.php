<?php

namespace App\Http\Controllers;

use App\Models\Module;
use Illuminate\Http\Request;

class AjaxController extends Controller
{
    public function getModules($formationId)
    {
        // Kanakhdo ga3 les modules li 3andhom had l'formation_id
        // w kan loadio m3ahom l'consultant (user) li m9arihom
        $modules = Module::with('user')
                         ->where('formation_id', $formationId)
                         ->get();

        // Kanreje3 les modules f format JSON
        return response()->json([
            'modules' => $modules,
        ]);
    }
}
