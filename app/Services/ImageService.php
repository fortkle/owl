<?php namespace Owl\Services;

use Owl\Repositories\ImageRepositoryInterface;

class ImageService extends Service
{
    protected $imageRepo;

    public function __construct(ImageRepositoryInterface $imageRepo)
    {
        $this->imageRepo = $imageRepo;
    }

    public function makeTag($image)
    {
        $host = $_SERVER["HTTP_HOST"];
        return "![{$image->alt_text}](//{$host}/images/{$image->external_path}{$image->external_name})";
    }

    public function moveImage()
    {
        $ds = "/";
        $orgImage = \Input::file('image');
        $exImgPath = $this->createExternalImagePath();
        $exImgName = $this->createExternalImageFileName($orgImage->getClientOriginalName());
        $orgImage->move(public_path().$ds."images".$ds.$exImgPath, $exImgName);

        $object = app('stdClass');
        $object->alt_text = $orgImage->getClientOriginalName();
        $object->external_path = $exImgPath;
        $object->external_name = $exImgName;
        $image = $this->imageRepo->createImage($object);
        return $image;
    }

    private function createExternalImageFileName($filename)
    {
        $t = microtime(true);
        $micro = sprintf("%06d", ($t - floor($t)) * 1000000);

        $extname = md5($filename.date('YmdHis').$micro);
        if (preg_match('/([0-9a-z]{8})([0-9a-z]{4})([0-9a-z]{5})([0-9a-z]{4})([0-9a-z]{11})/', $extname, $m)) {
            return "{$m[1]}-{$m[2]}-{$m[3]}-{$m[4]}-{$m[5]}".$extname[rand(0, 31)].".".$this->getExtension($filename);
        }
    }

    private function getExtension($filename)
    {
        if (preg_match('/(jpe?g|png|gif)/i', $filename, $m)) {
            return strtolower($m[1]);
        }
        return false;
    }

    private function createExternalImagePath($ds = '/')
    {
        return rand(0, 9).$ds.sprintf('%04d', rand(0, 9999)).$ds;
    }
}
