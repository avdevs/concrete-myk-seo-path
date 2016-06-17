<?php
namespace Concrete\Package\MykSeoPath;
use Package;
use Route;
use Request;
use Concrete\Core\Http\FlysystemFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

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

                $fre = $file->getFileResource();
                $path = DIR_FILES_UPLOADED_STANDARD . '/' . $fre->getPath();
                $r = Request::getInstance();
                $ifModifiedSince = $r->headers->get('if-modified-since');
                if(isset($ifModifiedSince) && (strtotime($ifModifiedSince) == filemtime($path))) {
                    header('HTTP/1.0 304 Not Modified');
                    exit;
                }
                $fs = $file->getFile()->getFileStorageLocationObject()->getFileSystemObject();
                $response = new FlysystemFileResponse($fre->getPath(), $fs);
                $response->headers->set('Cache-Control','cache');
                $response->headers->set('Last-Modified',gmdate('D, d M Y H:i:s', filemtime($path)).' GMT');
                $response->headers->set('Expires',date('D, d M Y H:i:s',time() + (60*60*24*30)).' GMT');
                $response->headers->set('Pragma','cache');
                $response->prepare(\Request::getInstance());
                return $response->send();

            }else{
                header('HTTP/1.0 404 Not found');
                exit;
            }
        });

        /* For Thumbnail */
        Route::register('/pictures/{fID}/{thumbnailHandle}/{keywords}', function ($fID, $thumbnailHandle, $keywords) {
            $file = \File::getByID($fID);
            if($file) {

                $fre = $file->getFileResource();
                $path = DIR_FILES_UPLOADED_STANDARD . '/thumbnails/' . $thumbnailHandle . '/' . $fre->getPath();
                if(file_exists($path)) {

                    $r = Request::getInstance();
                    $ifModifiedSince = $r->headers->get('if-modified-since');
                    if (isset($ifModifiedSince) && (strtotime($ifModifiedSince) == filemtime($path))) {
                        header('HTTP/1.0 304 Not Modified');
                        exit;
                    }
                    $fs = $file->getFile()->getFileStorageLocationObject()->getFileSystemObject();
                    $response = new FlysystemFileResponse('/thumbnails/' . $thumbnailHandle . '/' . $fre->getPath(), $fs);
                    $response->headers->set('Cache-Control', 'cache');
                    $response->headers->set('Last-Modified', gmdate('D, d M Y H:i:s', filemtime($path)) . ' GMT');
                    $response->headers->set('Expires', date('D, d M Y H:i:s', time() + (60 * 60 * 24 * 30)) . ' GMT');
                    $response->headers->set('Pragma', 'cache');
                    $response->prepare(\Request::getInstance());
                    return $response->send();
                } else {
                    header('HTTP/1.0 404 Not found');
                    exit;
                }
            }else{
                header('HTTP/1.0 404 Not found');
                exit;
            }
        });
    }
}