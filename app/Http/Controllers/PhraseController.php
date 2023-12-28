<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Phrase;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StorePhraseRequest;
use App\Http\Requests\UpdatePhraseRequest;
use Illuminate\Http\Request;

class PhraseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

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
     * @param  \App\Http\Requests\StorePhraseRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePhraseRequest $request)
    {
        //
    }

    public function phrasesAll()
    {
        $phrases = Phrase::all();
        return response()->json(['phrases' => $phrases]);
    }

    public function myphrases()
    {
        $phrases = Phrase::where('user_id', Auth::id())->get();
        return response()->json(['myphrases' => $phrases]);
    }

    public function toggleFavorite(Request $request)
    {
        $input = $request->all();
        try {
            Phrase::create($input);

            return response()->json(['message' => 'success']);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'error', $th]);
        }
    }

    public function deletephrase(Request $request)
    {
        $input = $request->all();
        try {
            $phrase = Phrase::findOrFail($input['id']);
            $phrase->delete();

            return response()->json(['message' => 'success']);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'error', 'error' => $th->getMessage()]);
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Phrase  $phrase
     * @return \Illuminate\Http\Response
     */
    public function show(Phrase $phrase)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Phrase  $phrase
     * @return \Illuminate\Http\Response
     */
    public function edit(Phrase $phrase)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatePhraseRequest  $request
     * @param  \App\Models\Phrase  $phrase
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePhraseRequest $request, Phrase $phrase)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Phrase  $phrase
     * @return \Illuminate\Http\Response
     */
    public function destroy(Phrase $phrase)
    {
        //
    }
}
