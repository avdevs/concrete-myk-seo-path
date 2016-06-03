<?php
namespace Concrete\Package\MykSeoPath;
use Package;
use Route;

class Controller extends Package {
    protected $pkgHandle = 'myk_seo_path';
    protected $appVersionRequired = '5.7.1';
    protected $pkgVersion = '0.0.1';
    public function getPackageDescription() {
        return t("Package for SEO friendly path");
    }
    public function getPackageName() {
        return t(" SEO friendly path for files");
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
                header( 'Content-Type: '.$file->getMimeType());
                echo $fh->getContents(DIR_FILES_UPLOADED_STANDARD . '/' . $file->getFileResource()->getPath());
            }
        });
    }
}