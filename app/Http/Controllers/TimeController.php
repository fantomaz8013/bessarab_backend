<?php

namespace App\Http\Controllers;

use App\Http\Requests\TimeStoreRequest;
use App\Models\Time;
use Illuminate\Http\Request;

class TimeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Time::all();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TimeStoreRequest $request)
    {
        $data = $request->validated();
        Time::create($data);
        return response()->json(["result" => "Ok"]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Time $time)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Time $time)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TimeStoreRequest $request, Time $time)
    {
        $data = $request->validated();
        $time->update($data);
        $time->save();
        return response()->json(["result" => "Ok"]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Time $time)
    {
        $time->delete();
        return response()->json(["result" => "Ok"]);
    }
}
