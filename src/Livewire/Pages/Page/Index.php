<?php

namespace Agenciafmd\Pages\Livewire\Pages\Page;

use Agenciafmd\Admix\Livewire\Pages\Base\Index as BaseIndex;
use Agenciafmd\Pages\Models\Page;

class Index extends BaseIndex
{
    protected $model = Page::class;

    protected string $indexRoute = 'admix.pages.index';

    protected string $trashRoute = 'admix.pages.trash';

    protected string $creteRoute = 'admix.pages.create';

    protected string $editRoute = 'admix.pages.edit';

    public function configure(): void
    {
        $this->packageName = __(config('admix-pages.name'));

        parent::configure();
    }
}