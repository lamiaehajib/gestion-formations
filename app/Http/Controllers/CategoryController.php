<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{

      public function __construct()
    {
        $this->middleware('auth'); // Ensure user is authenticated for all category actions

        // Permissions for category management
        $this->middleware('permission:category-list')->only(['index', 'show', 'getActiveCategories']);
        $this->middleware('permission:category-create')->only(['create', 'store']);
        $this->middleware('permission:category-edit')->only(['edit', 'update']);
        $this->middleware('permission:category-delete')->only(['destroy']);
        $this->middleware('permission:category-toggle-status')->only(['toggleStatus']);
        $this->middleware('permission:category-export')->only(['export']);
        $this->middleware('permission:category-bulk-action')->only(['bulkAction']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
{
    // Modify the query to count the formations for each category
    $query = Category::query()->withCount('formations');

    // Filter by status if provided
    if ($request->has('status') && $request->status !== '') {
        $query->where('is_active', $request->status === 'active');
    }

    // Search by name if provided
    if ($request->has('search') && $request->search !== '') {
        $query->where('name', 'like', '%' . $request->search . '%');
    }

    // Order by creation date (newest first)
    $categories = $query->orderBy('created_at', 'desc')->paginate(10);

    return view('categories.index', compact('categories'));
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string|max:1000',
            'icon' => 'nullable|string|max:255',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            Category::create([
                'name' => $request->name,
                'description' => $request->description,
                'icon' => $request->icon,
                'is_active' => $request->has('is_active') ? 1 : 0
            ]);

            return redirect()->route('categories.index')
                ->with('success', 'Catégorie créée avec succès');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erreur lors de la création de la catégorie')
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        $category->load(['formations' => function($query) {
            $query->where('status', 'published')
                  ->select('id', 'title', 'price', 'duration_hours', 'category_id', 'start_date', 'end_date');
        }]);

        // Statistics
        $stats = [
            'total_formations' => $category->formations()->count(),
            'published_formations' => $category->formations()->where('status', 'published')->count(),
            'draft_formations' => $category->formations()->where('status', 'draft')->count(),
            'completed_formations' => $category->formations()->where('status', 'completed')->count(),
        ];

        return view('categories.show', compact('category', 'stats'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'name')->ignore($category->id)
            ],
            'description' => 'nullable|string|max:1000',
            'icon' => 'nullable|string|max:255',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $category->update([
                'name' => $request->name,
                'description' => $request->description,
                'icon' => $request->icon,
                'is_active' => $request->has('is_active') ? 1 : 0
            ]);

            return redirect()->route('categories.index')
                ->with('success', 'Catégorie modifiée avec succès');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erreur lors de la modification de la catégorie')
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        try {
            // Check if category has formations
            $formationsCount = $category->formations()->count();
            
            if ($formationsCount > 0) {
                return redirect()->back()
                    ->with('error', "Impossible de supprimer cette catégorie. Elle contient {$formationsCount} formation(s).");
            }

            $category->delete();

            return redirect()->route('categories.index')
                ->with('success', 'Catégorie supprimée avec succès');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erreur lors de la suppression de la catégorie');
        }
    }

    /**
     * Toggle category status (active/inactive)
     */
    public function toggleStatus(Category $category)
    {
        try {
            $category->update([
                'is_active' => !$category->is_active
            ]);

            $status = $category->is_active ? 'activée' : 'désactivée';

            return redirect()->back()
                ->with('success', "Catégorie {$status} avec succès");

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erreur lors de la modification du statut');
        }
    }

    /**
     * Get active categories for dropdown/select
     */
    public function getActiveCategories()
    {
        $categories = Category::where('is_active', true)
            ->orderBy('name', 'asc')
            ->get(['id', 'name', 'description', 'icon']);

        return response()->json($categories);
    }

    /**
     * Bulk actions for categories
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'categories' => 'required|array',
            'categories.*' => 'exists:categories,id'
        ]);

        try {
            $categories = Category::whereIn('id', $request->categories);

            switch ($request->action) {
                case 'activate':
                    $categories->update(['is_active' => true]);
                    $message = 'Catégories activées avec succès';
                    break;
                    
                case 'deactivate':
                    $categories->update(['is_active' => false]);
                    $message = 'Catégories désactivées avec succès';
                    break;
                    
                case 'delete':
                    // Check if any category has formations
                    $categoriesWithFormations = $categories->withCount('formations')
                        ->get()
                        ->filter(function($category) {
                            return $category->formations_count > 0;
                        });

                    if ($categoriesWithFormations->count() > 0) {
                        return redirect()->back()
                            ->with('error', 'Certaines catégories ne peuvent pas être supprimées car elles contiennent des formations.');
                    }

                    $categories->delete();
                    $message = 'Catégories supprimées avec succès';
                    break;
            }

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erreur lors de l\'exécution de l\'action groupée');
        }
    }

    /**
     * Export categories to CSV
     */
    public function export()
    {
        $categories = Category::all();

        $filename = 'categories_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($categories) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for proper Excel encoding
            fwrite($file, "\xEF\xBB\xBF");
            
            // Add headers
            fputcsv($file, ['ID', 'Nom', 'Description', 'Icône', 'Statut', 'Date de création']);

            foreach ($categories as $category) {
                fputcsv($file, [
                    $category->id,
                    $category->name,
                    $category->description,
                    $category->icon,
                    $category->is_active ? 'Actif' : 'Inactif',
                    $category->created_at->format('d/m/Y H:i')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}