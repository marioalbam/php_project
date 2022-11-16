<?php

declare(strict_types=1);

namespace App\Controllers;

use App\View;
use App\Controllers\FileController;

class HomeController
{
    public static function index(): View
    {
        return View::make('index');
    }

    public function upload()
    {
        try {
            $file_path = dirname(__DIR__) . '/../storage/' . $_FILES['file']['name'];

            move_uploaded_file($_FILES['file']['tmp_name'], $file_path);

            $fileinfo = pathinfo($file_path) ?? header('Location: /public/index.php');

            if (array_key_exists('extension', $fileinfo)) {
                if ($fileinfo['extension'] !== 'csv') {
                    throw new \Exception("Wrong file type");
                }
            }

            if (is_file($file_path)) {
                FileController::make($fileinfo['basename'])->process();
            } else {
                header('Location: /public/index.php');
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}
