<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    protected $categoryService;
    protected $folderName = 'categories';

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }
    /**
     * Fetch and return a list of categories based on the provided request parameters.
     *
     * @param Request $request The incoming request containing filter parameters.
     * @return \Illuminate\Http\JsonResponse A JSON response containing the list of categories.
     * @throws Exception If an error occurs while fetching the categories.
     */
    public function index(Request $request)
    {
        try {
            $categories = $this->categoryService->filter($request); // pass all request to service and filter data
            return response()->json($categories);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to fetch categories.'], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        $file_url = null;

        if ($request->file) {
            $this->validate($request, [
                'file' => 'mimes:jpg,jpeg,png,webp|max:2048',
            ]);

            $file_url = imageUploadWithoutCrop($request->file, $this->folderName, null);
        }

        $category = Category::create([
            'name' => $request->name,
            'image' => $file_url
        ]);

        return response()->json([
            'message' => 'Category added successfully.',
            'category' => $category
        ], 201);
    }

    public function show(Category $category)
    {
        return $category;
    }

    public function update(Request $request, Category $category)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
        ]);

        try {
            $file_url = $category->image;

            if ($request->hasFile('file')) {
                $this->validate($request, [
                    'file' => 'mimes:jpg,jpeg,png,webp|max:2048',
                ]);
                $file_url = imageUploadWithoutCrop($request->file('file'), $this->folderName, $category->image);
            }

            $category->update([
                'name' => $request->name,
                'image' => $file_url,
            ]);

            return response()->json([
                'message' => 'Category updated successfully.',
                'category' => $category
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Failed to update category.',
                'details' => $e->getMessage()
            ], 500);
        }
    }


    public function destroy(Category $category)
    {
        try {
            $category->delete();
            return response()->json([
                'message' => 'Category deleted successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error deleting category'], 500);
        }
    }

    /**
     * Deletes multiple categories based on the provided category IDs.
     *
     * @param Request $request The incoming request containing the category IDs to be deleted.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the success or failure of the operation.
     * @throws \Exception If an error occurs while deleting the categories.
     */
    public function multipleCategoryDelete(Request $request)
    {
        $categoryIds = $request->category_ids;
        try {
            if (!is_array($categoryIds) || empty($categoryIds)) {
                return response()->json(['error' => 'Invalid category IDs provided.'], 400);
            }
            Category::whereIn('id', $categoryIds)->delete();
            return response()->json(['message' => 'Categories deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete categories.', 'details' => $e->getMessage()], 500);
        }
    }

    public function statusUpdate(Request $request, $categoryId)
    {
        try {
            $validatedData = Validator::make($request->all(), [
                'status' => 'required|in:1,0',
            ])->validate();

            $category = Category::findOrFail($categoryId);
            $category->status = $validatedData['status'];
            $category->save();
            return response()->json([
                'message' => 'Category status updated successfully.',
                'category' => $category,
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Failed to update category status.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // public function reviews($id)
    // {
    //     try {
    //         // Fetch reviews with user data and group them by rating in a single query
    //         $reviews = Review::with('user', 'reviewImages')
    //             ->where('product_id', $id)
    //             ->select('rating', \DB::raw('count(*) as count'))
    //             ->groupBy('rating')
    //             ->orderBy('rating', 'desc')
    //             ->get();

    //         // Calculate total reviews count and average rating
    //         $totalReviewsCount = $reviews->sum('count');
    //         $averageRating = $totalReviewsCount > 0
    //             ? Review::where('product_id', $id)->avg('rating')
    //             : 0;

    //         // Initialize the star counts array with 0s
    //         $starCounts = array_fill(1, 5, 0);

    //         // Fill in the star counts based on the grouped results
    //         foreach ($reviews as $review) {
    //             $starCounts[$review->rating] = $review->count;
    //         }

    //         // Fetch paginated reviews for frontend display
    //         $paginatedReviews = Review::with('user','reviewImages')->where('product_id', $id)->paginate(5);

    //         return response()->json([
    //             'data' => $paginatedReviews,
    //             'totalReviewsCount' => $totalReviewsCount,
    //             'average_rating' => round($averageRating, 1),
    //             'star_counts' => array_reverse($starCounts, true), // Reverse to maintain 5 to 1 order
    //         ], 200);
    //     } catch (\Throwable $e) {
    //         return response()->json([
    //             'message' => 'Failed to get reviews',
    //             'error' => $e->getMessage(),
    //         ], 500);
    //     }
    // }

    // public function storeReviews(Request $request)
    // {
    //     try {
    //         $validated = $request->validate([
    //             'product_id' => 'required|integer',
    //             'rating' => 'required|integer|between:1,5',
    //             'description' => 'required|string',
    //         ]);

    //         $validated['user_id'] = auth()->user()->id;

    //         $review = Review::create($validated);

    //         if ($request->hasFile('images')) {
    //             foreach ($request->file('images') as $image) {
    //                 $file_url = imageUploadWithoutCrop($image, 'reviewImages', null);
    //                 $review->reviewImages()->create([
    //                     'image_url' => $file_url,
    //                 ]);
    //             }
    //         }

    //         return response()->json([
    //             'message' => 'Review added successfully.',
    //         ], 201);
    //     } catch (\Exception $e) {
    //         // Handle the exception and return an error response
    //         return response()->json([
    //             'error' => 'An error occurred while adding the review.',
    //             'message' => $e->getMessage(),
    //         ], 500);
    //     }
    // }
}
