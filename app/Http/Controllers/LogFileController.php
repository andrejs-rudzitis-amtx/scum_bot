<?php

namespace App\Http\Controllers;

use App\chats;
use App\kills;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class LogFileController extends Controller
{
    public $path, $parsedPath, $serverRestarted, $debug;

    public function __construct()
    {
        $this->path = storage_path('app/unProcessed');
        $this->parsedPath = storage_path('app/Processed');
        $this->serverRestarted = false;
        $this->debug = env('DEBUG', false);

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
//        download loginFiles
        echo "Starting log file download";
        $loginFileList = $this->downloadFilesFromFtpByMask(['login', 'kill', 'chat']);
        foreach ($loginFileList as $i => $fileName) {
            echo " \r\n New File " . $fileName;
        }
        $parseList = $this->parse_login_files();
        foreach ($parseList as $i => $fileName) {
            echo " \r\n Parsed " . $fileName;
        }
        $parseList = $this->parseKillFeedFiles();
        foreach ($parseList as $i => $fileName) {
            echo " \r\n Parsed " . $fileName;
        }
        $parseList = $this->parseChatFiles();
        foreach ($parseList as $i => $fileName) {
            echo " \r\n Parsed " . $fileName;
        }


    }

    protected function downloadFilesFromFtpByMask($mask = null)
    {
        try {
            $con = ftp_connect(env('FTP_HOST'), env('FTP_PORT'), 30);
            if (false === $con) {
                throw new Exception('Unable to connect');
            }

            $loggedIn = ftp_login($con, env('FTP_USER'), env('FTP_PASSWORD'));
            ftp_pasv($con, true);
            if (true === $loggedIn) {
                echo "\r\n FTP connection Successfull!";
            } else {
                throw new Exception('Unable to log in');
            }
            $fileList = ftp_nlist($con, env('FTP_ROOT'));

            if (!File::isDirectory($this->path)) ;
            {
                File::makeDirectory($this->path, 0777, true, true);
            }
            if (!File::isDirectory($this->parsedPath)) ;
            {
                File::makeDirectory($this->parsedPath, 0777, true, true);
            }
            if ($mask != null && is_array($mask)) {
                foreach ($fileList as $key => $one) {
//                    check for specific mask
                    if ($this->strposa($one, $mask) === false)
                        unset($fileList[$key]);
//                    check for already processed files
                    if (Storage::disk('local')->exists('/Processed/' . $one)) {
                        $this->serverRestarted = 1;
                        unset($fileList[$key]);
                    }
                }
            } elseif ($mask != null) {
                foreach ($fileList as $key => $one) {
//                    check for specific mask
                    if (strpos($one, $mask) === false)
                        unset($fileList[$key]);
//                    check for already processed files
                    if (Storage::disk('local')->exists('/Processed/' . $one)) {
                        $this->serverRestarted = 1;
                        unset($fileList[$key]);
                    }
                }
            }


            foreach ($fileList as $i => $path) {
                ftp_get($con, $this->path . '/' . $path, env('FTP_ROOT') . $path, FTP_BINARY);
            }

            ftp_close($con);
        } catch (Exception $e) {
            echo "Failure: " . $e->getMessage();
        }
        return ($fileList);
    }

    /**
     * @param $type
     * @return \Symfony\Component\Finder\SplFileInfo[]
     */
    protected function parse_login_files()
    {
        $fileList = File::allFiles($this->path);
        if (is_array($fileList)) {

            foreach ($fileList as $key => $one) {
                if (strpos($one, 'login') === false)
                    unset($fileList[$key]);
            }

            $_k = 0;
            $_prevFile = '';

            foreach ($fileList as $file) {
                if ($this->serverRestarted == 1 || $_k > 0) {
                    User::where('presence', '=', 'online')->update(['presence' => 'offline']);
                }

                $i = 0;
                $contents = File::get($file);
                $contents = iconv('UTF-16LE', 'UTF-8', $contents);
                $lines = preg_split('/[\n\r]/', $contents);
                foreach ($lines as $line) {
                    //dump($line);
                    if (strpos($line, 'logged')) {
                        $array = explode(' ', $line);
                        $array2 = explode("'", $line);
                        //dump($array);
                        //dump($array2);

                        $scumIdHelper = explode('(', $array2[1]);
                        $scumId = (is_array($scumIdHelper) && isset($scumIdHelper[1])) ? substr($scumIdHelper[1], 0, strpos($scumIdHelper[1], ')')) : null;
                        $ignHelper = explode(':', $array2[1]);
                        $steamId64 = explode(" ", $ignHelper[0]);
                        $ign = substr($ignHelper[1], 0, strpos($ignHelper[1], '('));
//                        dump($ign,$steamId64[1]);


                        User::updateOrCreate(
                            [
                                'scumId' => $scumId
                            ],
                            [
                                'scumId' => $scumId,
                                'ign' => $ign,
                                'steamId64' => $steamId64[1],
                                'presence' => (strpos($line, 'logged in')) ? 'online' : 'offline',
                                'updatedAt' => gmdate("Y-m-d H:i:s", time()),
                                'presenceUpdatedAt' => gmdate("Y-m-d H:i:s", time()),
                            ]);
                        $i++;
                    }
                    echo "\n Parsed " . $i . "  lines from file: " . $file;
                }

                if ($_k++ > 0 && !$this->debug) {
                    File::move($_prevFile, $this->parsedPath . '/' . basename($_prevFile));
                }
                $_k++;
                $_prevFile = $file;
            }
        }
        return ($fileList);
    }

    public function parseKillFeedFiles()
    {
        $fileList = File::allFiles($this->path);

        foreach ($fileList as $key => $one) {
            if (strpos($one, 'kill') === false)
                unset($fileList[$key]);
        }

        $_k = 0;
        $_prevFile = '';
        if (is_array($fileList)) {
            foreach ($fileList as $file) {

                $i = 0;
                $contents = File::get($file);
//                dump(mb_detect_encoding($contents,['UTF-16LE', 'UTF-8']));
                $contents = iconv('UTF-16LE', 'UTF-8', $contents);
                $lines = preg_split('/[\n\r]/', $contents);
                foreach ($lines as $line) {
                    $arr = explode(":", $line, 2) ?? false;
                    if ($arr and count($arr) >= 2) {
                        if ($this->isJson($arr[1])) {

                            $obj = json_decode($arr[1]);
                            $weaponHelper = explode(' ', $obj->Weapon);
                            $weapon = $weaponHelper[0];

                            $weaponDamage = (count($weaponHelper) > 1) ? trim(str_replace('[', '', str_replace(']', '', $weaponHelper[1]))) : '';

                            kills::updateOrCreate(
                                [
                                    'killerName' => $obj->Killer->ProfileName,
                                    'victimName' => $obj->Victim->ProfileName,
                                    'timeOfDay' => $obj->TimeOfDay,
                                    'logTimeStamp' => $arr[0],
                                ],
                                [
                                    'killerName' => $obj->Killer->ProfileName,
                                    'killerInEvent' => $obj->Killer->IsInGameEvent,
                                    'killerServerX' => $obj->Killer->ServerLocation->X,
                                    'killerServerY' => $obj->Killer->ServerLocation->Y,
                                    'killerServerZ' => $obj->Killer->ServerLocation->Z,
                                    'killerClientX' => $obj->Killer->ClientLocation->X,
                                    'killerClientY' => $obj->Killer->ClientLocation->Y,
                                    'killerClientZ' => $obj->Killer->ClientLocation->Z,
                                    'killerSteamId64' => $obj->Killer->UserId,
                                    'killerImmortal' => $obj->Killer->HasImmortality,
                                    'victimName' => $obj->Victim->ProfileName,
                                    'victimServerX' => $obj->Victim->ServerLocation->X,
                                    'victimServerY' => $obj->Victim->ServerLocation->Y,
                                    'victimServerZ' => $obj->Victim->ServerLocation->Z,
                                    'victimClientX' => $obj->Victim->ClientLocation->X,
                                    'victimClientY' => $obj->Victim->ClientLocation->Y,
                                    'victimClientZ' => $obj->Victim->ClientLocation->Z,
                                    'victimSteamId64' => $obj->Victim->UserId,
                                    'weaponName' => $weapon ?? null,
                                    'weaponDamage' => $weaponDamage ?? null,
                                    'timeOfDay' => $obj->TimeOfDay,
                                    'logTimeStamp' => $arr[0],
                                    'victimUserId' => User::where('steamId64', $obj->Killer->UserId)->orderByDesc('id')->first()->id,
                                    'killerUserId' => User::where('steamId64', $obj->Victim->UserId)->orderByDesc('id')->first()->id,
                                    'distance' => $this->distance($obj->Killer->ServerLocation->X, $obj->Killer->ServerLocation->Y, $obj->Victim->ServerLocation->X, $obj->Victim->ServerLocation->Y)
                                ]);


                        }
                        if (strpos($arr[1], 'suicide')) {
                            $userHelper = explode('User:', $arr[1], 2);
                            $user = trim(substr($userHelper[1], 0, strpos($userHelper[1], '(') - 1));
                            $steamId64Helper = explode('(', $userHelper[1], 2);
                            $userId = trim(substr($steamId64Helper[1], 0, strpos($steamId64Helper[1], ',')));
                            $steamId64 = trim(substr($steamId64Helper[1], strpos($steamId64Helper[1], ',') + 1, strpos($steamId64Helper[1], ')') - strpos($steamId64Helper[1], ',') - 1));
                            $locationHelper = explode('Location:', $arr[1], 2);
                            $xHelper = explode('X=', $locationHelper[1]);
                            $x = trim(substr($xHelper[1], 0, strpos($xHelper[1], ' ')));
                            $yHelper = explode('Y=', $locationHelper[1]);
                            $y = trim(substr($yHelper[1], 0, strpos($yHelper[1], ' ')));
                            $zHelper = explode('Z=', $locationHelper[1]);
                            $z = trim(substr($zHelper[1], 0, -1));
                            $distance = $this->distance($x, $y, $x, $y);
//                            dump($arr[1],$user,$steamId64,$x,$y,$z,$distance,$userId);

                            kills::updateOrCreate(
                                [
                                    'killerName' => $user,
                                    'victimName' => $user,
                                    'timeOfDay' => null,
                                    'logTimeStamp' => $arr[0],
                                ],
                                [
                                    'killerName' => $user,
                                    'killerInEvent' => false,
                                    'killerServerX' => $x,
                                    'killerServerY' => $y,
                                    'killerServerZ' => $z,
                                    'killerClientX' => $x,
                                    'killerClientY' => $y,
                                    'killerClientZ' => $z,
                                    'killerSteamId64' => $steamId64,
                                    'killerImmortal' => false,
                                    'victimName' => $user,
                                    'victimServerX' => $x,
                                    'victimServerY' => $y,
                                    'victimServerZ' => $z,
                                    'victimClientX' => $x,
                                    'victimClientY' => $y,
                                    'victimClientZ' => $z,
                                    'victimSteamId64' => $steamId64,
                                    'weaponName' => null,
                                    'weaponDamage' => null,
                                    'timeOfDay' => null,
                                    'logTimeStamp' => $arr[0],
                                    'victimUserId' => User::where('scumId', $userId)->orderByDesc('id')->first()->id,
                                    'killerUserId' => User::where('scumId', $userId)->orderByDesc('id')->first()->id,
                                    'distance' => $distance
                                ]);


                        }
                    }


                    $i++;
                }

                if ($_k++ > 0 && !$this->debug) {
                    File::move($_prevFile, $this->parsedPath . '/' . basename($_prevFile));
                }
                $_prevFile = $file;
            }
        }
        return ($fileList);
    }

    public function parseChatFiles()
    {
        $fileList = File::allFiles($this->path);

        foreach ($fileList as $key => $one) {
            if (strpos($one, 'chat') === false)
                unset($fileList[$key]);
        }

        $_k = 0;
        $_prevFile = '';
        if (is_array($fileList)) {
            foreach ($fileList as $file) {

                $i = 0;
                $contents = File::get($file);
//                dump(mb_detect_encoding($contents,['UTF-16LE', 'UTF-8']));
                $contents = iconv('UTF-16LE', 'UTF-8', $contents);
                $lines = preg_split('/[\n\r]/', $contents);
                foreach ($lines as $line) {
                    $arr = explode(":", $line, 2) ?? false;
                    if ($arr and count($arr) >= 2) {

                        $chatContent = explode("'", $arr[1], 4);

                        if (count($chatContent) > 1) {
                            $steam64 = substr($chatContent[1], 0, strpos($chatContent[1], ':'));
                            $user = substr($chatContent[1], strpos($chatContent[1], ':') + 1, strpos($chatContent[1], '(') - strpos($chatContent[1], ':') - 1);
                            $messageHelper = explode(":", $chatContent[3], 2);
                            $chat = trim($messageHelper[0]);
                            $message = trim(substr($messageHelper[1], 0, -1));

                            $mentionAdmins = (strpos(strtolower($message), 'admin')) ? 1 : 0;
                            $userData = User::where('steamId64', $steam64)->orderByDesc('id')->first();

                            $userId = $userData->id??0;

                            Chats::updateOrCreate(
                                [
                                    'sentAt'=>$arr[0],
                                    'context'=>$chat,
                                    'content'=>$message,
                                ],
                                [
                                    'sentAt'=>$arr[0],
                                    'context'=>$chat,
                                    'content'=>$message,
                                    'authorSteamId64'=>$steam64,
                                    'authorIgn'=>$user,
                                    'authorScumId'=>$userId,
                                    'mentionAdmins'=>$mentionAdmins
                                ]
                            );
//                            dump(trim($arr[1]), $chatContent, $steam64, $user, $chat, $message, $mentionAdmins, $userId);
                        }


                    }


                    $i++;
                }

                if ($_k++ > 0 && !$this->debug) {
                    File::move($_prevFile, $this->parsedPath . '/' . basename($_prevFile));
                }
                $_prevFile = $file;
            }
        }
        return ($fileList);
    }

    /**
     *
     *
     * HELPERS
     *
     *
     *
     */


    /**
     * @param $string
     * @return mixed|string
     */
    function stripallslashes($string)
    {

        while (strchr($string, '\\')) {

            $string = stripslashes($string);

        }
        return ($string);

    }

    function isJson($string)
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }

    function strposa(string $haystack, array $needles, int $offset = 0): bool
    {
        foreach ($needles as $needle) {
            if (strpos($haystack, $needle, $offset) !== false) {
                return true; // stop on first true result
            }
        }

        return false;
    }

    function distance($x1 = 0, $y1 = 0, $x2 = 0, $y2 = 0)
    {
        return (int)Round(sqrt((($x2 - $x1) * ($x2 - $x1) + ($y2 - $y1) * ($y2 - $y1)) / 100), 0);
    }
}
