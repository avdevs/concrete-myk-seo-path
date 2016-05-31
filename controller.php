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
            $fh = \Core::make('helper/file');
            echo $fh->getContents($file->getURL());
        });
    }
}