<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Models\SubCategory;
use Exception;

class SubCategoryService
{
    /**
     * Filters categories based on the provided request parameters.
     *
     * @param Request $request The request object containing the search and status parameters.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator A paginated collection of categories that match the given criteria.
     *
     * @throws Exception If an error occurs during the filtering process.
     */
    public function filter(Request $request)
    {
        $prePage = 10;
        try {
            $query = SubCategory::with('category');

            //filter by search input
            if ($request->has('search')) {
                $searchTerm = $request->search;
                $query->where('name', 'like', "%{$searchTerm}%");
            }

            //filter by status
            if ($request->has('searchStatus')) {
                $searchStatus = $request->searchStatus;
                //if status is 3 got then fetch all teh data otherwise get by searchStatus value
                if ($searchStatus != 3) {
                    $query->where('status', $searchStatus);
                }
            }

            return $query->latest()->paginate($prePage);
        } catch (Exception $e) {
            throw new Exception("Error filtering categories: " . $e->getMessage(), $e->getCode());
        }
    }
}
