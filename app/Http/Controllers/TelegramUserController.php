<?php

namespace App\Http\Controllers;

use App\Models\TelegramUser;
use Illuminate\Http\Request;

class TelegramUserController extends Controller
{
    /**
     * Получить список пользователей телеграм бота
     */
    public function index()
    {
        return TelegramUser::all();
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
    public function store(Request $request)
    {
        //
    }
    /**
     * Вебхук для телеграм бота
     */
    public function webhook(Request $request)
    {
        $data = $request->all();
        $chatId = $data['message']['chat']['id'];
        $text = $data['message']['text'];
        $allData = http_build_query($data);

        if ($text == '/start')
        {
            if (isset($data['message']['from']['last_name']))
            {
                $name = $data['message']['from']['first_name'] . " " . $data['message']['from']['last_name'];
            }
            else {
                $name = $data['message']['from']['first_name'];
            }

            $text = "Привет $name. Теперь вы будете получать заказы с сайта";
            $data = http_build_query([
                'chat_id' => $chatId,
                'text' => $text
            ]);
            file_get_contents("https://api.telegram.org/bot6720731238:AAGcZ4QSSFRVWYrL8BzuRbGYiMRoWQR8oAA/sendMessage?$data");
            TelegramUser::updateOrCreate(['chat_id' => $chatId], ['chat_id' => $chatId, 'name' => $name]);
        }

        return response('Ok', 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(TelegramUser $telegramUser)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TelegramUser $telegramUser)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TelegramUser $telegramUser)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TelegramUser $telegramUser)
    {
        //
    }
}
