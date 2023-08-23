<?php

namespace App\Http\Controllers;

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
        $this->debug = env('DEBUG',false);

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
//        download loginFiles
        echo "Starting login file download";
        $loginFileList = $this->downloadFilesFromFtpByMask('login');
        foreach ($loginFileList as $i => $fileName) {
            echo " \r\n New File " . $fileName;
        }
        $parseList = $this->parse_login_files();
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
            if ($mask != null) {
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
            if ($this->serverRestarted == 1) {
                User::where('presence', '=', 'online')->update(['presence' => 'offline']);
            }
            foreach ($fileList as $file) {
                $i = 0;
                $contents = File::get($file);
                $contents = iconv('UTF-16LE', 'UTF-8', $contents);
                $lines = preg_split('/[\n\r]/', $contents);
                foreach ($lines as $line) {
                    //dump($line);
                    if (strpos($line, 'logged')) {
                        $array = explode(' ', $line);
                        $array2 = explode("'",$line);
                        //dump($array);
                        //dump($array2);

                        $scumIdHelper = explode('(', $array2[1]);
                        $scumId = (is_array($scumIdHelper) && isset($scumIdHelper[1])) ? substr($scumIdHelper[1], 0, strpos($scumIdHelper[1], ')')) : null;
                        $ignHelper = explode(':', $array2[1]);
                        $steamId64 = explode(" ",$ignHelper[0]);
                        $ign = $this->stripallslashes(substr($ignHelper[1], 0, strpos($ignHelper[1], '(')));
                        //dump($scumId,$ign,$steamId64[1]);


                        User::updateOrCreate(
                            [
                                'scumId' => $scumId
                            ],
                            [
                                'scumId' => $scumId,
                                'ign' => $ign,
                                'steamId64' => $steamId64,
                                'presence' => ($array[4] === 'in') ? 'online' : 'offline',
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
}
