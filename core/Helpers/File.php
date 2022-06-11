<?php

namespace Core\Helpers;

class File
{
    private static function path($path)
    {
        $path = public_path($path);
        @mkdir($path, 0777, true);
        return $path;
    }

    private static function createName($name)
    {
        $ext = @end(explode(".", $name));
        return uniqid('file-', true) . ".$ext";
    }

    public static function save($path, $file)
    {
        $uploadName = self::path($path) . "/" . self::createName(end(explode('/', $file)));
        file_put_contents($uploadName, file_get_contents($file));
        return str_replace(public_path(), '', $uploadName);
    }

    public static function upload($_path, $file, $accept = [])
    {
        $path = self::path($_path);
        $name = $file['name'];

        if (count($accept)) {
            $ext = end(explode('.', $name));
            if (!in_array($ext, $accept)) return false;
        }

        $uploadName = "$path/" . self::createName($name);
        if (move_uploaded_file($file['tmp_name'], $uploadName)) return str_replace(public_path(), '', $uploadName);
        return false;
    }

    public static function resizeImage($file, $width = 50, $height = 50)
    {
        $file = public_path($file);
        // Yeni boyutları hesaplayalım
        list($image_width, $image_height) = getimagesize($file);

        // Görüntüyü örnekleyelim
        $target = imagecreatetruecolor($width, $height);
        $source = imagecreatefromjpeg($file);
        imagecopyresampled($target, $source, 0, 0, 0, 0, $width, $height, $image_width, $image_height);

        // Görüntüyü çıktılayalım
        imagejpeg($target, $file, 100);

        return str_replace(public_path(), '', $file);
    }
}
