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

        $path = self::path($path);
        foreach ($file['name'] as $key => $name) {
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
                    Alerts::danger(Lang::get('errors.file.size', ['current-size' => self::humanFileSize($file['size'][$key]), 'accept-size' => self::humanFileSize($options['size'])]));
                }

            if ($error) continue;

            $uploadName = "$path/" . self::createName($name);
            if (move_uploaded_file($file['tmp_name'][$key], $uploadName)) $files[$key] = self::removePublic($uploadName);
        }

        if (!count($files)) return false;
        return count($files) > 1 ? $files : @end($files);
    }

    /**
     * Download a file from public_path
     * @param string $file
     */
    public static function download(string $file)
    {
        $attachment_location = public_path($file);
        $filename = self::removePublic(@end(explode('/', str_replace('\\', '/', $file))));

        if (!file_exists($attachment_location)) abort(404, 'File not exists.');

        header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
        header("Cache-Control: public");
        header("Content-Type: application/zip");
        header("Content-Transfer-Encoding: Binary");
        header("Content-Length:" . filesize($attachment_location));
        header("Content-Disposition: attachment; filename=$filename");
        die(readfile($attachment_location));
    }

    /**
     * Resize a image
     * @param string $file
     * @param int $width
     * @param int $height
     * @param string $_target
     * @param bool $info_size
     * @return string
     */
    public static function resizeImage(string $file, int $width = 50, int $height = 50, string $_target = null, bool $info_size = false): string
    {

        $file = public_path($file);
        if (!is_file($file)) return false;

        $ext = strtolower(pathinfo($file)['extension']);

        list($image_width, $image_height) = getimagesize($file);

        $src_aspect = $image_width / $image_height;
        $dst_aspect = $width / $height;

        if ($src_aspect > $dst_aspect) {
            $height = $width / $src_aspect;
        } else {
            $width  = $height * $src_aspect;
        }

        $source = [
            'jpg' => function () use ($file) {
                return imagecreatefromjpeg($file);
            },
            'png' => function () use ($file) {
                return imagecreatefrompng($file);
            },
            'gif' => function () use ($file) {
                return imagecreatefromgif($file);
            },
            'webp' => function () use ($file) {
                return imagecreatefromwebp($file);
            },
            'bmp' => function () use ($file) {
                return imagecreatefrombmp($file);
            },
        ][$ext]();

        $target = imagecreatetruecolor($width, $height);
        imagecopyresampled($target, $source, 0, 0, 0, 0, $width, $height, $image_width, $image_height);

        $to_save = self::removePublic($file);
        if ($info_size) $to_save = str_replace(".$ext", '', $to_save) . "-$width" . "x" . "$height.$ext";
        if ($_target) {
            $get_filename = @end(explode('/', str_replace('\\', '/', $file)));
            $to_save      = "$_target/$get_filename";
        }

        imagewebp($target, public_path($to_save), 100);

        // clear cache
        imagedestroy($source);
        imagedestroy($target);
        //

        return self::removePublic($file);
    }

    /**
     * Show human readable file size
     * 
     * @param float $bytes
     * @param int $decimals
     * @return string
     */
    public static function humanFileSize(float $bytes, int $decimals = 2): string
    {
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'][$factor];
    }
}
