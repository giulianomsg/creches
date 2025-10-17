<?php

namespace App\Helpers;

class UploadHelper
{
    public static function handle(array $file, array $config, string $folder): ?string
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        if ($file['size'] > $config['max_size']) {
            throw new \RuntimeException('Arquivo excede o tamanho máximo permitido.');
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);

        if (!in_array($mime, $config['allowed_mime'], true)) {
            throw new \RuntimeException('Tipo de arquivo não permitido.');
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('doc_', true) . '.' . strtolower($extension);
        $destination = rtrim($folder, '/') . '/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            throw new \RuntimeException('Falha ao salvar o arquivo.');
        }

        return $filename;
    }
}
