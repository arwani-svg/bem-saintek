<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Http\Request;

class ActivityLog extends Model
{
    use HasFactory;
    /**
     * Log hanya punya created_at (append-only, tidak pernah di-update).
     */
    public const UPDATED_AT = null;

    public const LOGIN = 'login';

    public const LOGOUT = 'logout';

    public const CREATE = 'create';

    public const UPDATE = 'update';

    public const DELETE = 'delete';

    public const PENDAFTARAN = 'pendaftaran';

    public const STATUS = 'status';

    /** Daftar jenis aktivitas untuk filter & badge di halaman log. */
    public const ACTIVITIES = [
        self::LOGIN,
        self::LOGOUT,
        self::CREATE,
        self::UPDATE,
        self::DELETE,
        self::PENDAFTARAN,
        self::STATUS,
    ];

    /** Key yang tidak boleh masuk log (disamarkan bila nekat dikirim). */
    public const SENSITIVE_KEYS = [
        'password',
        'password_confirmation',
        'current_password',
        'new_password',
        'token',
        'remember_token',
        'api_token',
        'access_token',
        'secret',
        'otp',
        'pin',
    ];

    protected $fillable = [
        'user_id',
        'activity',
        'description',
        'subject_type',
        'subject_id',
        'properties',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Catat satu aktivitas penting.
     *
     * Aman dipanggil di mana saja: kegagalan tulis log tidak akan
     * merusak alur utama (dibungkam), dan data sensitif disaring.
     */
    public static function record(
        ?User $actor,
        string $activity,
        string $description,
        ?Model $subject = null,
        array $properties = [],
        ?Request $request = null,
    ): ?self {
        try {
            $request ??= request();

            return static::create([
                'user_id' => $actor?->getKey(),
                'activity' => mb_substr($activity, 0, 50),
                'description' => mb_substr($description, 0, 500),
                'subject_type' => $subject ? $subject->getMorphClass() : null,
                'subject_id' => $subject ? $subject->getKey() : null,
                'properties' => self::scrub($properties) ?: null,
                'ip_address' => $request?->ip(),
                'user_agent' => $request ? mb_substr((string) $request->userAgent(), 0, 255) : null,
            ]);
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Saring data sensitif (password, token, dsb.) secara rekursif.
     */
    public static function scrub(array $data): array
    {
        $clean = [];

        foreach ($data as $key => $value) {
            if (in_array(strtolower((string) $key), self::SENSITIVE_KEYS, true)) {
                $clean[$key] = '[disaring]';

                continue;
            }

            $clean[$key] = is_array($value) ? self::scrub($value) : $value;
        }

        return $clean;
    }
}
