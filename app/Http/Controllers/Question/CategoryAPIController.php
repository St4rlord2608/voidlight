<?php

namespace App\Http\Controllers\Question;

use App\Http\Controllers\Controller;
use App\Models\Question\Category;
use Illuminate\Http\Request;

class CategoryAPIController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'localId' => 'required',
            'name' => 'required',
            'userId' => 'required',
        ]);
        $attributes = [
            'local_id' => $validated['localId'],
            'name' => $validated['name'],
            'user_id' => $validated['userId'],
        ];
        try{
            $category = Category::firstOrCreate($attributes);

            if($category->wasRecentlyCreated){
                $statusCode = 201;
            }else{
                $statusCode = 200;
            }

            return response()->json($category, $statusCode);
        }catch(\Exception $e){
            return response()->json(['message' => 'Failed to store category'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        //
    }
}
