<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\SubCategoryService;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Validator;

class SubCategoryController extends Controller
{
    protected $subCategoryService;
    protected $folderName = 'subcategories';

    public function __construct(SubCategoryService $subCategoryService)
    {
        $this->subCategoryService = $subCategoryService;
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
            $subCategories = $this->subCategoryService->filter($request);
            return response()->json($subCategories);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to fetch sub categories.'], 500);
        }
    }

    public function categories()
    {
        try {
            $categories = Category::all();
            return response()->json([
                'message' => 'category fetch successfully.',
                'categories' => $categories
            ], 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to fetch categories.'], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'categoryId' => 'required',
            'name' => 'required|string|max:255|unique:sub_categories,name',
        ]);

        $file_url = null;

        if ($request->file) {
            $this->validate($request, [
                'file' => 'mimes:jpg,jpeg,png,webp|max:2048',
            ]);

            $file_url = imageUploadWithoutCrop($request->file, $this->folderName, null);
        }

        $category = SubCategory::create([
            'category_id' => $request->categoryId,
            'name' => $request->name,
            'image' => $file_url
        ]);

        return response()->json([
            'message' => 'sub-category added successfully.',
            'subCategory' => $category
        ], 201);
    }

    public function show($id)
    {
        $subcategory = SubCategory::with('category')->find($id);
        return response()->json([
            'message' => 'sub-category added successfully.',
            'subCategory' => $subcategory
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $subCategory = SubCategory::find($id);
        
        $this->validate($request, [
            'categoryId' => 'required',
            'name' => 'required|string|max:255|unique:sub_categories,name,' . $subCategory->id,
        ]);

        try {
            $file_url = $subCategory->image;

            if ($request->hasFile('file')) {
                $this->validate($request, [
                    'file' => 'mimes:jpg,jpeg,png,webp|max:2048',
                ]);
                $file_url = imageUploadWithoutCrop($request->file('file'), $this->folderName, $subCategory->image);
            }

            $subCategory->update([
                'category_id' => $request->categoryId,
                'name' => $request->name,
                'image' => $file_url,
            ]);

            return response()->json([
                'message' => 'sub Category updated successfully.',
                'subCategory' => $subCategory
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Failed to update sub category.',
                'details' => $e->getMessage()
            ], 500);
        }
    }


    public function destroy(SubCategory $subCategory)
    {
        try {
            if ($subCategory->image) {
                removeFile($subCategory->image);
            }
            $subCategory->delete();
            return response()->json([
                'message' => 'sub category deleted successfully.',
            ], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error deleting brand'], 500);
        }
    }

    /**
     * Deletes multiple categories based on the provided category IDs.
     *
     * @param Request $request The incoming request containing the category IDs to be deleted.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the success or failure of the operation.
     * @throws \Exception If an error occurs while deleting the categories.
     */
    public function multipleSubCategoryDelete(Request $request)
    {
        $subCategoryIds = $request->subCategory_ids;
        try {
            if (!is_array($subCategoryIds) || empty($subCategoryIds)) {
                return response()->json(['error' => 'Invalid sub category IDs provided.'], 400);
            }
            $subCategories = SubCategory::whereIn('id', $subCategoryIds)->get();

            foreach ($subCategories as $subCategory) {
                if ($subCategory->image) {
                    removeFile($subCategory->image);
                }
            }
            SubCategory::whereIn('id', $subCategoryIds)->delete();
            return response()->json(['message' => 'Sub Categories deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete categories.', 'details' => $e->getMessage()], 500);
        }
    }

    public function statusUpdate(Request $request, $subCategoryId)
    {
        try {
            $validatedData = Validator::make($request->all(), [
                'status' => 'required|in:1,0',
            ])->validate();

            $subCategory = SubCategory::findOrFail($subCategoryId);
            $subCategory->status = $validatedData['status'];
            $subCategory->save();
            return response()->json([
                'message' => 'Sub Category status updated successfully.',
                'subCategory' => $subCategory,
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Failed to update category status.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
