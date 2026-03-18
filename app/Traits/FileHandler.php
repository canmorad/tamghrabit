<?php
namespace App\Traits;

use Exception;

trait FileHandler
{
    protected function deleteFile($fullPath)
    {
        if (!empty($fullPath) && file_exists($fullPath) && !str_contains($fullPath, 'default.png')) {
            return unlink($fullPath);
        }
        return false;
    }

    protected function uploadFile($file, $destinationPath)
    {
        if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
            throw new Exception("Fichier temporaire introuvable.");
        }

        if (!move_uploaded_file($file['tmp_name'], $destinationPath)) {
            throw new Exception("Erreur lors du déplacement du fichier.");
        }

        return true;
    }
}