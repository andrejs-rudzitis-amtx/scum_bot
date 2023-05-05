<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class LogFileController extends Controller
{
    public $path,$parsedPath;

    public function __construct(){
        $this->path = storage_path('app/unProcessed');
        $this->parsedPath = storage_path('app/Processed');
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
        foreach($loginFileList as $i => $fileName){
            echo " \r\n New File ".$fileName;
        }
        $parseList = $this->parse_files('login');
        foreach($parseList as $i => $fileName){
            echo " \r\n Parsed ".$fileName;
        }


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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }
    protected function downloadFilesFromFtpByMask($mask = null){
        try {
            $con = ftp_connect(env('FTP_HOST'),env('FTP_PORT'),30);
            if (false === $con) {
                throw new Exception('Unable to connect');
            }

            $loggedIn = ftp_login($con,  env('FTP_USER'),  env('FTP_PASSWORD'));
            ftp_pasv($con,true);
            if (true === $loggedIn) {
                echo "\r\n FTP connection Successfull!";
            } else {
                throw new Exception('Unable to log in');
            }
            $fileList = ftp_nlist($con,env('FTP_ROOT'));

            if (!File::isDirectory($this->path));{
                File::makeDirectory($this->path,0777,true,true);
            }
            if(!File::isDirectory($this->parsedPath));{
                File::makeDirectory($this->parsedPath,0777,true,true);
            }
            if($mask != null){
                foreach($fileList as $key => $one) {
//                    check for specific mask
                    if(strpos($one, $mask) === false)
                        unset($fileList[$key]);
//                    check for already processed files TODO test this
                    if(Storage::disk('local')->exists('/processed/'.$one)){
                        unset($fileList[$key]);
                    }
                }
            }





            foreach($fileList as $i => $path){
                ftp_get($con, $this->path.'/'.$path,env('FTP_ROOT').$path,FTP_BINARY);
            }

            ftp_close($con);
        } catch (Exception $e) {
            echo "Failure: " . $e->getMessage();
        }
        return($fileList);
    }
    protected function parse_files($type = null){
        $fileList = File::allFiles($this->path);
        if(is_array($fileList)){
            if($type != null){
                foreach($fileList as $key => $one) {
                    if(strpos($one, 'login') === false)
                        unset($fileList[$key]);
                }
            }
            $_k=0;
            $_prevFile = '';
            foreach($fileList as $file){
                $i=0;
                $contents = File::get($file);
                $contents = iconv('UTF-16LE','UTF-8',$contents);
                $lines = preg_split('/[\n\r]/',$contents);
                    foreach($lines as $line) {
                        if (strpos($line, 'logged in') || strpos($line, 'logged out')) {
                            $array = explode(' ', $line);
                            $scumIdHelper =explode ('(',$array[2]);
                            $scumId =substr($scumIdHelper[1],0,strpos($scumIdHelper[1],')'));
                            $ignHelper = explode(':',$array[2]);
                            $steamId64 = $ignHelper[0];
                            $ign = substr($ignHelper[1],0,strpos($ignHelper[1],'('));
                            dump($ign);
                            dump($array);

                            User::updateOrCreate(
                                [
                                    'scumId' => $scumId
                                ],
                                [
                                    'scumId' => $scumId,
                                    'ign' => $ign,
                                    'steamId64' => $steamId64,
                                    'presence' => ($array[4]==='in')?'online':'offline',
                                    'updatedAt' => gmdate("Y-m-d H:i:s", time()),
                                    'presenceUpdatedAt' => gmdate("Y-m-d H:i:s", time()),
                            ]);
                            $i++;
                        }
                        echo "\n Parsed " . $i . "  lines from file: " . $file;
                    }
                //TODO Test this part as well
                if($_k++ > 0){
                    File::move($_prevFile,$this->parsedPath(basename($_prevFile)));
                }
                $_k++;
                $_prevFile = $file;
            }
        }
        return($fileList);
    }
}
