<?php

namespace App\Http\Controllers;

use App\Filters\ContactListFilter;
use App\Http\Requests\ContactRequest;
use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * Получить список заявок ( форма на главной )
     * @queryParam  name string . Example: Иванов Иван
     * @queryParam  phone string . Example: 89999999999
     * @queryParam  email string . Example: Ivan@mail.ru
     * @queryParam  type integer . Example: 1
     * @queryParam  page int Страница. Example: 1
     * @queryParam  limit int Сколько выдать записей. Example: 10.
     */
    public function index(ContactListFilter $filter)
    {
        return Contact::filter($filter)
        ->get();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Создать заявку ( форма на главной )
     */
    public function store(ContactRequest $request)
    {
        $data = $request->validated();
        Contact::create($data);
        return response('Ok', 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Contact $contact)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Contact $contact)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Contact $contact)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contact $contact)
    {
        //
    }
}
