<?php

namespace App\Http\Controllers\Jeopardy;

use App\Http\Controllers\Controller;
use App\Models\Jeopardy\JeopardyBoardCell;
use App\Models\Lobby\Lobby;
use App\Models\Question\Category;
use App\Models\Question\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JeopardyBoardCellAPIController extends Controller
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
    public function store($lobbyCode, Request $request)
    {
        $validated = $request->validate([
            'category' => 'required',
            'category.localId' => 'required',
            'category.name' => 'required',

            'question' => 'required',
            'question.localId' => 'required',
            'question.question' => 'required',
            'question.answer' => 'required',

            'question.clues' => 'sometimes',
            'question.clues.*.value' => 'required_with:question.clues',
            'question.clues.*.order' => 'required_with:question.clues',

            'points' => 'required',
            'userId' => 'required',
        ]);

        try{
            $boardCell = DB::transaction(function () use ($validated, $lobbyCode) {
                $localCat = $validated['category'];
                $dbCategory = Category::firstOrCreate([
                    'local_id' => $localCat['localId'],
                    'name' => $localCat['name'],
                    'user_id' => $validated['userId']
                    ]);

                $localQuestion = $validated['question'];
                $dbQuestion = Question::firstOrCreate([
                    'local_id' => $localQuestion['localId'],
                    'question' => $localQuestion['question'],
                    'answer' => $localQuestion['answer'],
                    'user_id' => $validated['userId'],
                ]);

                if(isset($localQuestion['clues'])){
                    $cluesToSync = [];
                    foreach($localQuestion['clues'] as $localClue){
                        $clue = $dbQuestion->clues()->updateOrCreate([
                            'value' => $localClue['value'],
                            'order' => $localClue['order'],
                        ]);
                        $cluesToSync[] = $clue->id;
                    }
                }

                $existingCell = JeopardyBoardCell::where('jeopardy_lobby_id', $lobbyCode)
                    ->where('category_id', $dbCategory->id)
                    ->where('question_id', $dbQuestion->id)
                    ->first();
                if($existingCell){
                    DB::rollBack();
                    return response()->json([$existingCell]);
                }

                $lobby = Lobby::where('lobby_code', $lobbyCode)->firstOrFail()->load('jeopardy_lobby');
                $jeopardyLobby = $lobby->jeopardy_lobby;

                $boardCell = JeopardyBoardCell::create([
                    'jeopardy_lobby_id' => $jeopardyLobby->id,
                    'category_id' => $dbCategory->id,
                    'question_id' => $dbQuestion->id,
                    'points' => $validated['points'],
                    'answered' => false,
                ]);
                return $boardCell->load('category', 'question', 'question.clues');
            });
            return response()->json([$boardCell]);
        }catch (\Exception $e){
            return response()->json([$e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(JeopardyBoardCell $jeopardyBoardCell)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, JeopardyBoardCell $jeopardyBoardCell)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JeopardyBoardCell $jeopardyBoardCell)
    {
        //
    }
}
