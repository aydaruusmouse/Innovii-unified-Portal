<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Offer;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class OfferController extends Controller
{
    public function index(Request $request)
    {
        try {
            Log::info('OfferController: Starting to fetch offers');
            
            // Add CORS headers for local development
            $headers = [
                'Access-Control-Allow-Origin' => 'http://127.0.0.1:8000',
                'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS',
                'Access-Control-Allow-Headers' => 'Content-Type, Authorization',
                'Access-Control-Allow-Credentials' => 'true'
            ];
            
            // Check if we can connect to the database
            try {
                DB::connection()->getPdo();
                Log::info('OfferController: Database connection successful');
            } catch (\Exception $e) {
                Log::error('OfferController: Database connection failed', ['error' => $e->getMessage()]);
                return Response::json([
                    'error' => 'Database connection failed',
                    'message' => $e->getMessage()
                ], 500)->withHeaders($headers);
            }

            // Get search query if present
            $searchQuery = $request->input('search');
            
            // Build the query
            $query = Offer::select('id', 'name', 'status', 'date', 'validity', 'app_id', 'short_code', 'message')
                ->orderBy('id', 'desc');

            // Apply search filter if query exists
            if ($searchQuery) {
                $query->where(function($q) use ($searchQuery) {
                    $q->where('name', 'like', "%{$searchQuery}%")
                      ->orWhere('short_code', 'like', "%{$searchQuery}%");
                });
            }

            // Paginate the results
            $offers = $query->paginate(10);
            
            Log::info('OfferController: Fetched offers count', ['count' => $offers->total()]);
            
            if ($offers->isEmpty()) {
                Log::info('OfferController: No offers found in database');
            }

            $offersData = $offers->map(function ($offer) {
                return [
                    'id' => $offer->id,
                    'name' => $offer->name ?? 'N/A',
                    'status' => $offer->status ?? 'inactive',
                    'date' => $offer->date ?? now()->format('Y-m-d'),
                    'validity' => $offer->validity ?? 'N/A',
                    'app_id' => $offer->app_id ?? 'N/A',
                    'short_code' => $offer->short_code ?? 'N/A',
                    'message' => $offer->message ?? 'N/A'
                ];
            });
            
            // Calculate statistics
            $totalOffers = $offers->total();
            $activeOffers = Offer::where('status', 'ACTIVE')->count();
            
            // Get top 3 offers based on subscription base count
           // ... existing code ...
            // Get top 3 offers based on subscription base count
            $topOffers = DB::table('offers')
                ->select('offers.*', 'sb.base_count')
                ->join(DB::raw('(SELECT name, MAX(base_count) as base_count 
                               FROM subscription_base 
                               GROUP BY name) as sb'), 
                      'offers.name', '=', 'sb.name')
                ->where('offers.status', 'ACTIVE')
                ->orderBy('sb.base_count', 'desc')
                ->take(3)
                ->get()
                ->map(function ($offer) {
                    return [
                        'id' => $offer->id,
                        'name' => $offer->name ?? 'N/A',
                        'status' => $offer->status ?? 'inactive',
                        'date' => $offer->date ?? now()->format('Y-m-d'),
                        'validity' => $offer->validity ?? 'N/A',
                        'app_id' => $offer->app_id ?? 'N/A',
                        'short_code' => $offer->short_code ?? 'N/A',
                        'message' => $offer->message ?? 'N/A',
                        'subscriber_count' => $offer->base_count ?? 0
                    ];
                });
// ... existing code ...
            $response = [
                'offers' => $offersData,
            'totalOffers' => $totalOffers,
            'activeOffers' => $activeOffers,
                'topOffers' => $topOffers,
                'pagination' => [
                    'current_page' => $offers->currentPage(),
                    'last_page' => $offers->lastPage(),
                    'per_page' => $offers->perPage(),
                    'total' => $offers->total(),
                    'links' => $offers->links()->toHtml()
                ]
            ];

            Log::info('OfferController: Sending response', ['response' => $response]);
            
            return Response::json($response)->withHeaders($headers);
        } catch (\Exception $e) {
            Log::error('OfferController: Error occurred', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return Response::json([
                'error' => 'Failed to fetch offers data',
                'message' => $e->getMessage()
            ], 500)->withHeaders($headers);
        }
    }

    public function show($id)
    {
        try {
            $offer = DB::table('offers')
                ->where('id', $id)
                ->first();

            if (!$offer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Offer not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $offer
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch offer details',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
