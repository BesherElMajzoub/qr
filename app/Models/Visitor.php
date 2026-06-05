<?php

namespace App\Models;

use Database\Factories\VisitorFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{
    /** @use HasFactory<VisitorFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'qr_raw_data',
        'name',
        'email',
        'phone',
        'ip_address',
        'user_agent',
        'scanned_at',
        'notes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'scanned_at' => 'datetime',
        ];
    }
}
