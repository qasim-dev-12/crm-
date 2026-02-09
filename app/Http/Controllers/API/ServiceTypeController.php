<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceType;

class ServiceTypeController extends Controller
{
      public function index()
    {
        return response()->json([
            'data' => ServiceType::orderBy('name')->get()
        ]);
    }
}
