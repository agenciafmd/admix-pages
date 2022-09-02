<?php

namespace Agenciafmd\Payments\Models;

use Agenciafmd\Participants\Models\Participant;
use Agenciafmd\Payments\Database\Factories\PaymentFactory;
use Agenciafmd\Media\Traits\MediaTrait;
use Agenciafmd\Admix\Traits\TurboTrait;
use Agenciafmd\Plans\Models\Plan;
use Agenciafmd\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\Models\Media;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;

class Payment extends Model implements AuditableContract, HasMedia, Searchable
{
    use SoftDeletes, HasFactory, Auditable, MediaTrait, TurboTrait;

    protected $guarded = [
        'media',
    ];

    protected $casts = [
        'payment_date' => 'date',
    ];

    public $searchableType;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->searchableType = config('local-payments.name');
    }

    public function getSearchResult(): SearchResult
    {
        return new SearchResult(
            $this,
            "{$this->name}",
            route('admix.pages.edit', $this->id)
        );
    }

    public function scopeIsActive($query)
    {
        $query->where('is_active', 1);
    }

    public function scopeSort($query)
    {
        $sorts = default_sort(config('local-payments.default_sort'));

        foreach ($sorts as $sort) {
            $query->orderBy($sort['field'], $sort['direction']);
        }
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function newFactory()
    {
        if (class_exists(\Database\Factories\PaymentFactory::class)) {
            return \Database\Factories\PaymentFactory::new();
        }

        return PaymentFactory::new();
    }
}
