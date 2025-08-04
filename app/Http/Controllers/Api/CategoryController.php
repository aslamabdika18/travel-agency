<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $categories = Category::withCount('travelPackages')
                ->orderBy('name')
                ->get()
                ->map(function ($category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name,
                        'description' => $category->description,
                        'travel_packages_count' => $category->travel_packages_count,
                        'created_at' => $category->created_at,
                        'updated_at' => $category->updated_at,
                    ];
                });

            return response()->json([
                'success' => true,
                'message' => 'Categories retrieved successfully',
                'data' => $categories
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve categories',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified category.
     *
     * @param Category $category
     * @return JsonResponse
     */
    public function show(Category $category): JsonResponse
    {
        try {
            $category->loadCount('travelPackages');
            
            return response()->json([
                'success' => true,
                'message' => 'Category retrieved successfully',
                'data' => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'description' => $category->description,
                    'travel_packages_count' => $category->travel_packages_count,
                    'created_at' => $category->created_at,
                    'updated_at' => $category->updated_at,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get travel packages by category.
     *
     * @param Category $category
     * @param Request $request
     * @return JsonResponse
     */
    public function travelPackages(Category $category, Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 10);
            $perPage = min($perPage, 50); // Limit to 50 items per page
            
            $travelPackages = $category->travelPackages()
                ->with(['category'])
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Travel packages retrieved successfully',
                'data' => $travelPackages
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve travel packages',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}