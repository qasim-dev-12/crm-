<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\ServiceType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LeadsController extends Controller
{
    /**
     * Display a listing of leads
     */
public function index(Request $request)
{
    $perPage = $request->get('perPage', 10);
    $status  = $request->get('status');
    $term    = $request->get('term');

    $query = Lead::with('serviceType')->latest();

    if ($status !== null && $status !== 'all') {
        $query->where('status', $status);
    }

    if ($term) {
        $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('mobile', 'like', "%{$term}%")
              ->orWhere('area', 'like', "%{$term}%")
              ->orWhere('vehicle_number', 'like', "%{$term}%")
              ->orWhere('status', 'like', "%{$term}%")
              ->orWhereHas('serviceType', function ($s) use ($term) {
                  $s->where('name', 'like', "%{$term}%");
              });
        });
    }

    // ✅ IMPORTANT: return paginator directly
    return $query->paginate($perPage);
}


    /**
     * Search leads
     */
    // public function search(Request $request)
    // {
    //     $term     = $request->get('term');
    //     $status   = $request->get('filterType');
    //     $perPage  = $request->get('perPage', 10);

    //     $query = Lead::with('serviceType');

    //     if ($term) {
    //         $query->where(function ($q) use ($term) {
    //             $q->where('name', 'like', "%$term%")
    //               ->orWhere('mobile', 'like', "%$term%");
    //         });
    //     }

    //     if ($status !== null && $status !== 'all') {
    //         $query->where('status', $status);
    //     }

    //     return response()->json(
    //         $query->latest()->paginate($perPage)
    //     );
    // }

    /**
     * Store a new lead
     */
   public function store(Request $request)
{
    $validated = $request->validate([
        'salutation'       => 'nullable|string|max:10',
        'name'             => 'required|string|max:255',
        'service_type_id'  => 'required|exists:service_types,id',
        'area'             => 'nullable|string|max:255',
        'charges'          => 'nullable|numeric',
        'mobile'           => 'required|string|max:20',
        'status'           => 'required|string',
        'vehicle_number'  => 'required|string|max:255', // ✅
    ]);

    $validated['slug'] = Str::uuid(); // ✅ UNIQUE SLUG

    $lead = Lead::create($validated);

    return response()->json([
        'message' => 'Lead created successfully',
        'data'    => $lead
    ], 201);
}

    /**
     * Show a single lead
     */
   public function show($slug)
{
    $lead = Lead::with('serviceType')
        ->where('slug', $slug)
        ->firstOrFail();

    return response()->json([
        'data' => $lead
    ]);
}
public function update(Request $request, $slug)
{
    $lead = Lead::where('slug', $slug)->firstOrFail();
    $lead->update($request->all());

    return response()->json([
        'message' => 'Lead updated successfully'
    ]);
}

    /**
     * Update a lead
     */


    /**
     * Delete a lead
     */
    public function destroy($slug)
    {
        $lead = Lead::where('slug', $slug)->firstOrFail();
        $lead->delete();

        return response()->json(true);
    }

    /**
     * Convert lead
     */
   public function convert($slug)
{
    $lead = Lead::where('slug', $slug)->firstOrFail();

    // ✅ Always force Converted
    $lead->update([
        'status' => 'Converted',
    ]);

    return response()->json([
        'message' => 'Lead converted successfully',
        'data' => $lead,
    ]);
}

}
