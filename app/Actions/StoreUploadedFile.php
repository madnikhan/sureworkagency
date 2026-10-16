<?php

namespace App\Actions;

use App\Models\FileAsset;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;

class StoreUploadedFile
{
    /**
     * @param  array<int, string>  $allowed
     */
    public static function run(UploadedFile $file, ?string $subjectType, ?int $subjectId, array $allowed): FileAsset
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $mime = (string) $file->getMimeType();

        if (! in_array($extension, $allowed, true) || ! self::mimeAllowed($mime)) {
            throw ValidationException::withMessages(['file' => 'That file type is not allowed.']);
        }

        if ($file->getSize() > 10 * 1024 * 1024) {
            throw ValidationException::withMessages(['file' => 'Files must be 10 MB or smaller.']);
        }

        $path = $file->store('uploads', 'local');

        return FileAsset::query()->create([
            'uploaded_by' => auth()->id(),
            'subject_type' => $subjectType,
            'subject_id' => $subjectId,
            'disk' => 'local',
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime' => $mime,
            'size' => $file->getSize(),
        ]);
    }

    private static function mimeAllowed(string $mime): bool
    {
        return in_array($mime, [
            'application/pdf',
            'image/png',
            'image/jpeg',
            'image/webp',
            'text/plain',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ], true);
    }
}
