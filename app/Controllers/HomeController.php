<?php

declare(strict_types=1);

namespace App\Controllers;

use App\View;
use App\Controllers\FileController;
use App\Controllers\CSvController;

class HomeController
{
    public static function index(): View
    {
        return View::make('index');
    }

    public function upload()
    {
        try {
            if (! array_filter($_POST['columnId'])) {
                throw new \Exception('Column Id missing');
            }

            $file_path = dirname(__DIR__) . '/../storage/' . $_FILES['file']['name'];

            move_uploaded_file($_FILES['file']['tmp_name'], $file_path);

            if (! is_file($file_path)) {
                throw new \Exception('File is missing');
            }

            $fileinfo = pathinfo($file_path);

            if (array_key_exists('extension', $fileinfo)) {
                if ($fileinfo['extension'] !== 'csv') {
                    throw new \Exception('Wrong file type');
                }
            }

            $file = FileController::make($fileinfo['basename'])->extract();
            $updated_file = CSvController::make($file)->process($_POST['columnId']);
            $file->write($updated_file);

            $_SESSION['file_path'] = $file_path;
            $_SESSION['file_name'] = $fileinfo['basename'];

            header('Location: /public/index.php/download');
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function download()
    {
        if ($_SESSION) {
            header('Content-type: text/csv');
            header('Content-Disposition: attachment; filename=' . $_SESSION['file_name']);
            readfile($_SESSION['file_path']);

            unlink($_SESSION['file_path']);
        } else {
            header('Location: /public/index.php');
        }
    }
}
