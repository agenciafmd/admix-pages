<?php

namespace Agenciafmd\Pages\Observers;

use Agenciafmd\Pages\Models\Page;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class PageObserver
{
    public function saving(Page $model)
    {
        $model->slug = Str::slug($model->name);
    }

    public function saved(Page $model)
    {
        if (!app()->runningInConsole()) {

            try {
                dispatch(function () use ($model) {
                    Artisan::call('page-cache:clear', [
                        'slug' => 'pc__index__pc',
                    ]);

                    Http::get(url('/'));
                })
                    ->delay(now()->addSeconds(5))
                    ->onQueue('low');
            } catch (\Exception $exception) {
                // não tem problema
            }
        }
    }
}
