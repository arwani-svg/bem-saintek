<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FotoProfil
{
    /** Folder penyimpanan di disk `public` — satu folder agar mudah dipantau. */
    public const DISK = 'public';

    public const FOLDER = 'foto-profil';

    /** Batas hemat storage: 1 MB (1024 KB). Cukup untuk foto profil 800px yang tajam. */
    public const MAX_KB = 1024;

    public const MIMES = 'jpg,jpeg,png,webp';

    /** Aturan validasi tunggal agar konsisten di semua controller. */
    public static function rules(bool $required = false): string
    {
        $wajib = $required ? 'required' : 'nullable';

        return "{$wajib}|image|mimes:".self::MIMES.'|max:'.self::MAX_KB;
    }

    /**
     * Simpan file baru + hapus file lama (hemat storage).
     * Return path relatif (mis. foto-profil/abc.jpg) atau null bila tak ada file.
     */
    public static function simpan(?UploadedFile $file, ?string $lama = null): ?string
    {
        if (! $file) {
            return $lama;
        }

        if ($lama) {
            self::hapus($lama);
        }

        return $file->store(self::FOLDER, self::DISK);
    }

    public static function hapus(?string $path): void
    {
        if ($path && Storage::disk(self::DISK)->exists($path)) {
            Storage::disk(self::DISK)->delete($path);
        }
    }

    public static function url(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        return Storage::disk(self::DISK)->url($path);
    }
}
