<?php
namespace Concrete\Package\MykSeoPath;
use Package;
use Route;

class Controller extends Package {
    protected $pkgHandle = 'myk_seo_path';
    protected $appVersionRequired = '5.7.1';
    protected $pkgVersion = '0.0.1';
    public function getPackageDescription() {
        return t('Package for SEO friendly path');
    }
    public function getPackageName() {
        return t('SEO friendly path for files');
    }
    public function install() {
        $pkg = parent::install();
    }
    public function upgrade() {
        $pkg = parent::upgrade();
    }
    public function on_start() {
        Route::register('/files/{fID}/{keywords}', function ($fID, $keywords) {
            $file = \File::getByID($fID);
            if($file) {
                $fh = \Core::make('helper/file');
                $path = DIR_FILES_UPLOADED_STANDARD . '/' . $file->getFileResource()->getPath();

                //getallheaders() create issue on nginx
                //A request header of Owner-ID is going to be passed to php as $_SERVER['HTTP_OWNER_ID'] by nginx
                if (!function_exists('getallheaders'))
                {
                    function getallheaders()
                    {
                        $headers = '';
                        foreach ($_SERVER as $name => $value)
                        {
                            if (substr($name, 0, 5) == 'HTTP_')
                            {
                                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
                            }
                        }
                        return $headers;
                    }
                }

                $headers = getallheaders();
                if (isset($headers['If-Modified-Since']) && (strtotime($headers['If-Modified-Since']) == filemtime($path))) {
                    header('HTTP/1.0 304 Not Modified');
                    exit;
                }
                header('Content-Type: '.$file->getMimeType());
                header('Cache-Control: public');
                header('Last-Modified: '. gmdate('D, d M Y H:i:s', filemtime($path)).' GMT');
                header('Expires:' . date('r',time() + (60*60*24*30))); // Date in the past
                echo $fh->getContents($path);
            }
        });
    }
}