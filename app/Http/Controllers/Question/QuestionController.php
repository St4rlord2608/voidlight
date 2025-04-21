<?php

namespace App\Http\Controllers\Question;

use App\Http\Controllers\Controller;
use App\Models\Lobby\SubLobby;
use App\Models\Question\Category;
use App\Models\Question\Question;
use Illuminate\Http\Request;
use Inertia\Inertia;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $questions = Question::all()->load('categories');
            $categories = Category::all()->load('questions');
        }catch(\Exception $e){
            return Inertia::render('questions/Index', [
                'propQuestions' => null,
                'propCategories' => null,
                'propErrorMessage' => ['message' => 'An unexpected error occurred while creating the lobby.'.$e->getMessage()]
            ]);
        }
        return Inertia::render('questions/Index', [
            'propQuestions' => $questions,
            'propCategories' => $categories
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $subLobbies = SubLobby::all();
        return Inertia::render('questions/Edit', [
            'subLobbies' => $subLobbies,
            'questionId' => -1
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Question $question)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $questionId)
    {
        $subLobbies = SubLobby::all();
        return Inertia::render('questions/Edit', [
            'subLobbies' => $subLobbies,
            'questionId' => $questionId
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Question $question)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Question $question)
    {
        //
    }
}
