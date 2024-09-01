<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Services\BrandService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Jobs\SendBrandCreatedEmail;
use Illuminate\Support\Facades\Log;

class BrandController extends Controller
{
    protected $brandService;
    protected $folderName = 'brands';

    public function __construct(BrandService $brandService)
    {
        $this->brandService = $brandService;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $brands = $this->brandService->filter($request); // pass all request to service and filter data
            return response()->json($brands);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to fetch brands.'], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255|unique:brands,name',
                'file' => 'sometimes|mimes:jpg,jpeg,png,webp|max:2048',
            ]);

            $file_url = null;

            if ($request->file) {
                $file_url = imageUploadWithoutCrop($request->file, $this->folderName, null);
            }

            $brand = Brand::create([
                'name' => $request->name,
                'file_url' => $file_url
            ]);

            SendBrandCreatedEmail::dispatch($brand);

            return response()->json([
                'message' => 'Brand added successfully.'
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while adding the brand.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Brand $brand)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:brands,name,' . $brand->id,
            ]);

            $brand->update($validated);

            return response()->json([
                'message' => 'Brand updated successfully.',
                'brand' => $brand
            ], 200);
        } catch (Exception $e) {
            // Handle exceptions
            return response()->json(['error' => 'Failed to update brand.'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Brand $brand)
    {
        try {
            if ($brand->file_url) {
                removeFile($brand->file_url);
            }
            $brand->delete();
            return response()->json([
                'message' => 'brand deleted successfully.',
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
    public function multipleBrandDelete(Request $request)
    {
        $brandIds = $request->brand_ids;
        try {
            if (!is_array($brandIds) || empty($brandIds)) {
                return response()->json(['error' => 'Invalid brand IDs provided.'], 400);
            }
            Brand::whereIn('id', $brandIds)->delete();
            return response()->json(['message' => 'Brands deleted successfully.']);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete brands.', 'details' => $e->getMessage()], 500);
        }
    }

    public function statusUpdate(Request $request, $brandId)
    {
        try {
            $validatedData = Validator::make($request->all(), [
                'status' => 'required|in:1,0',
            ])->validate();

            $brand = Brand::findOrFail($brandId);
            $brand->status = $validatedData['status'];
            $brand->save();
            return response()->json([
                'message' => 'Brand status updated successfully.',
                'brand' => $brand,
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Failed to update brand status.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
