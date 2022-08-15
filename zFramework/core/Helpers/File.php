<?php

namespace zFramework\Core\Helpers;

use zFramework\Core\Facades\Alerts;
use zFramework\Core\Facades\Lang;

class File
{
    /**
     * Get path, if not exists create and get path.
     * @param string $path
     * @return string
     */
    private static function path(string $path): string
    {
        $path = public_path($path);
        @mkdir($path, 0777, true);
        return $path;
    }
    /**
     * Create unique file name
     * @param string $name
     * @return string
     */
    private static function createName(string $name): string
    {
        $ext = @end(explode(".", $name));
        return "$name---" . uniqid('file-', true) . ".$ext";
    }

    /**
     * Remove public_path string
     * @return string
     */
    private static function removePublic(string $name): string
    {
        return str_replace(public_path(), '', $name);
    }

    /**
     * Save a file
     * @param string $path
     * @param string $file
     * @return string
     */
    public static function save(string $path, string $file): string
    {
        $uploadName = self::path($path) . "/" . self::createName(end(explode('/', $file)));
        file_put_contents($uploadName, file_get_contents($file));
        return self::removePublic($uploadName);
    }

    /**
     * Upload files
     * @param string $path
     * @param array $file
     * @param array $options
     * @return bool|array
     */
    public static function upload(string $path, array $file, array $options = [])
    {
        $files = [];

        if (gettype($file['name']) === 'string') foreach ($file as $key => $val) $file[$key] = [$val];

        foreach ($file['name'] as $key => $name) {
            $path = self::path($path);
            $name = $file['name'][$key];
            $error = 0;

            if (isset($options['accept'])) {
                $ext = @end(explode('.', $name));
                if (!in_array($ext, $options['accept'])) {
                    $error++;
                    Alerts::danger(Lang::get('errors.file.type', ['file_types' => implode(', ', $options['accept'])]));
                }
            }

            if (isset($options['size']) && is_numeric($options['size']))
                if ($file['size'][$key] > $options['size']) {
                    $error++;
                    Alerts::danger(Lang::get('errors.file.size', ['current-size' => human_filesize($file['size'][$key]), 'accept-size' => human_filesize($options['size'])]));
                }

            if ($error) continue;

            $uploadName = "$path/" . self::createName($name);
            if (move_uploaded_file($file['tmp_name'][$key], $uploadName)) $files[] = self::removePublic($uploadName);
        }

        if (!count($files)) return false;
        return count($files) > 1 ? $files : $files[0];
    }

    /**
     * Resize a image
     * @param string $file
     * @param int $width
     * @param int $height
     * @return string
     */
    public static function resizeImage(string $file, int $width = 50, int $height = 50): string
    {
        $file = public_path($file);
        if (!is_file($file)) return false;

        // Yeni boyutları hesaplayalım
        list($image_width, $image_height) = getimagesize($file);

        // Görüntüyü örnekleyelim
        $target = imagecreatetruecolor($width, $height);
        $source = imagecreatefromjpeg($file);
        imagecopyresampled($target, $source, 0, 0, 0, 0, $width, $height, $image_width, $image_height);


        $ext = @end(explode('.', $file));

        // Görüntüyü çıktılayalım
        imagejpeg($target, str_replace(".$ext", '', $file) . "-$width" . "x" . "$height.$ext", 100);

        return self::removePublic($file);
    }
}
