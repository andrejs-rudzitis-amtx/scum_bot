<?php

namespace App\Http\Controllers;

use App\ChatGptQuotes;
use App\DiscordAnnouncements;
use Illuminate\Http\Request;

class DiscordAnnouncementsController extends Controller
{
    //
    public $serverName = 'Latvian Scummers';
    protected $httpClient;

    public function restartAnnouncement($minutes)
    {
        $content = $this->serverName . ' Server will restart in ' . $minutes . ' minutes! While server restarts here is a quote from ChatGpt - ' . $this->quotes(rand(0, 49));
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


}
