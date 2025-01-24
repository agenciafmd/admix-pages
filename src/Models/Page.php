<?php

namespace Agenciafmd\Pages\Models;

use Agenciafmd\Admix\Traits\WithScopes;
use Agenciafmd\Admix\Traits\WithSlug;
use Agenciafmd\Pages\Database\Factories\PageFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Page extends Model implements AuditableContract
{
    use Auditable, HasFactory, Prunable, SoftDeletes, WithScopes, WithSlug;

    protected $guarded = [
        //
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected array $defaultSort = [
        'is_active' => 'desc',
        'name' => 'asc',
    ];

    public function prunable(): Builder
    {
        return self::where('deleted_at', '<=', now()->subYear());
    }

    protected static function newFactory(): PageFactory|\Database\Factories\PageFactory
    {
        if (class_exists(\Database\Factories\PageFactory::class)) {
            return \Database\Factories\PageFactory::new();
        }

        return PageFactory::new();
    }
}
