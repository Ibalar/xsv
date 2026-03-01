<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportLog extends Model
{
    use HasFactory;

    public const FILE_TYPE_CSV = 'csv';
    public const FILE_TYPE_XML = 'xml';

    public const STATUS_PENDING = 'pending';
    public const STATUS_SUCCESS = 'success';
    public const STATUS_ERROR = 'error';

    public const FILE_TYPES = [
        self::FILE_TYPE_CSV => 'CSV',
        self::FILE_TYPE_XML => 'XML',
    ];

    public const STATUSES = [
        self::STATUS_PENDING => 'Ожидает',
        self::STATUS_SUCCESS => 'Успешно',
        self::STATUS_ERROR => 'Ошибка',
    ];

    protected $fillable = [
        'file_name',
        'file_type',
        'status',
        'message',
        'imported_at',
    ];

    protected function casts(): array
    {
        return [
            'imported_at' => 'datetime',
        ];
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeSuccess($query)
    {
        return $query->where('status', self::STATUS_SUCCESS);
    }

    public function scopeError($query)
    {
        return $query->where('status', self::STATUS_ERROR);
    }

    public function scopeByFileType($query, string $type)
    {
        return $query->where('file_type', $type);
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isSuccess(): bool
    {
        return $this->status === self::STATUS_SUCCESS;
    }

    public function isError(): bool
    {
        return $this->status === self::STATUS_ERROR;
    }

    public function getStatusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getFileTypeLabel(): string
    {
        return self::FILE_TYPES[$this->file_type] ?? $this->file_type;
    }

    public function markAsSuccess(?string $message = null): void
    {
        $this->update([
            'status' => self::STATUS_SUCCESS,
            'message' => $message,
            'imported_at' => now(),
        ]);
    }

    public function markAsError(string $message): void
    {
        $this->update([
            'status' => self::STATUS_ERROR,
            'message' => $message,
            'imported_at' => now(),
        ]);
    }

    public static function log(string $fileName, string $fileType, string $status, ?string $message = null): self
    {
        return self::create([
            'file_name' => $fileName,
            'file_type' => $fileType,
            'status' => $status,
            'message' => $message,
            'imported_at' => $status !== self::STATUS_PENDING ? now() : null,
        ]);
    }
}
