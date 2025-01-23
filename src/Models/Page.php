<?php

namespace Agenciafmd\Pages\Models;

use Agenciafmd\Pages\Database\Factories\PageFactory;
use Agenciafmd\Media\Traits\MediaTrait;
use Agenciafmd\Admix\Traits\TurboTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\Models\Media;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;

class Page extends Model implements AuditableContract, HasMedia, Searchable
{
    use SoftDeletes, HasFactory, Auditable, MediaTrait, TurboTrait;

    protected $guarded = [
        'media',
    ];

    public $searchableType;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->searchableType = config('admix-pages.name');
    }

    public function getSearchResult(): SearchResult
    {
        return new SearchResult(
            $this,
            "{$this->name}",
            route('admix.pages.edit', $this->id)
        );
    }

    public function scopeIsActive($query): void
    {
        $query->where('is_active', 1);
    }

    public function scopeSort($query): void
    {
        $sorts = default_sort(config('admix-pages.default_sort'));

        foreach ($sorts as $sort) {
            $query->orderBy($sort['field'], $sort['direction']);
        }
    }

    protected static function newFactory(): PageFactory|\Database\Factories\PageFactory
    {
        if (class_exists(\Database\Factories\PageFactory::class)) {
            return \Database\Factories\PageFactory::new();
        }

        return PageFactory::new();
    }
}
