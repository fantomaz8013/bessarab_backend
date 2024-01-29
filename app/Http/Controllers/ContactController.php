<?php

namespace App\Http\Controllers;

use App\Filters\ContactListFilter;
use App\Http\Requests\ContactRequest;
use App\Models\Contact;
use App\Models\TelegramUser;
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
        $contact = Contact::create($data);
        $telegramUsers = TelegramUser::where('is_work', 1)
            ->get();

        $text = "<b>У вас запрос на сотрудничество </b>\n
<b>Имя:</b> {$contact->name} \n
<b>Номер телефона:</b> {$contact->phone} \n
<b>Email:</b> {$contact->email} \n
<b>Тип обращения:</b> {$this->getTypeContact($contact->type_id)} \n
<b>Комментарий:</b>\n{$contact->description}
";
        foreach ($telegramUsers as $telegramUser)
        {
            $data = http_build_query([
                'chat_id' => $telegramUser->chat_id,
                'text' => $text,
                'parse_mode' => 'html'
            ]);
            file_get_contents("https://api.telegram.org/bot6720731238:AAGcZ4QSSFRVWYrL8BzuRbGYiMRoWQR8oAA/sendMessage?$data");
        }
        return response('Ok', 200);
    }


    private function getTypeContact($type_id)
    {
        switch ($type_id)
        {
            case 1:
                return "Оптовые продажи";
            case 2:
                return "Салон";
            case 3:
                return "Частный мастер";
            default:
                return "Другое";
        }
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
