<?php

namespace Agenciafmd\Pages\Http\Components\Aside;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Illuminate\Support\Facades\Gate;
use Agenciafmd\Pages\Models\Page as PageModel;

class Page extends Component
{
    public function __construct(
        public string $icon = '',
        public string $label = '',
        public string $url = '',
        public bool   $active = false,
        public bool   $visible = false,
    )
    {
    }

    public function render(): View
    {
        $this->icon = __(config('admix-pages.icon'));
        $this->label = __(config('admix-pages.name'));
        $this->url = route('admix.pages.index');
        $this->active = request()?->currentRouteNameStartsWith('admix.pages');
        $this->visible = Gate::allows('view', PageModel::class);

        return view('admix::components.aside.item');
    }
}
