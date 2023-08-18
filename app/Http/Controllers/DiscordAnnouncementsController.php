<?php

namespace App\Http\Controllers;

use App\ChatGptQuotes;
use App\DiscordAnnouncements;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class DiscordAnnouncementsController extends Controller
{
    //
    public $serverName = 'Latvian Scummers';
    protected $httpClient;

    public function restartAnnouncement($minutes)
    {
        $content = $this->serverName . ' Server will restart in ' . $minutes . ' minutes! While server restarts here is a quote froom ChatGpt - ' . $this->quotes(rand(0, 49));
        DiscordAnnouncements::create([
            'content'=>$content
        ]);
    }

    protected function quotes($id = false)
    {

        if (!$id) {
            $id = rand(0, 99);
        }
        $quotes = ChatGptQuotes::all();
        return ($quotes[$id]->quotes);

    }

    public function getQuoteFromChatGpt($message = "Generate new funny quote about game server restart"){
        $this->httpClient = new Client([
            'base_uri' => 'https://api.openai.com/v1/',
            'headers' => [
                'Authorization' => 'Bearer ' . env('CHATGPT_API_KEY'),
                'Content-Type' => 'application/json',
            ],
        ]);


        $response = $this->httpClient->post('chat/completions', [
            'json' => [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are'],
                    ['role' => 'user', 'content' => $message],
                ],
            ],
        ]);

        return json_decode($response->getBody(), true)['choices'][0]['message']['content'];


    }

}
