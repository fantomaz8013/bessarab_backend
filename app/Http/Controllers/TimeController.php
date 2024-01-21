<?php

namespace App\Http\Controllers;

use App\Http\Requests\TimeStoreRequest;
use App\Models\Time;
use Illuminate\Http\Request;

class TimeController extends Controller
{
    /**
     * Получить список расписаний
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
     * Создать расписание
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
     * Изменить расписание
     */
    public function update(TimeStoreRequest $request, Time $time)
    {
        $data = $request->validated();
        $time->update($data);
        $time->save();
        return response()->json(["result" => "Ok"]);
    }

    /**
     * Удалить расписание
     */
    public function destroy(Time $time)
    {
        $time->delete();
        return response()->json(["result" => "Ok"]);
    }
}
