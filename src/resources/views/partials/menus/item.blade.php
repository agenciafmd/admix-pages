@can('view', \Agenciafmd\Pages\Models\Page::class)
    <li class="nav-item">
        <a class="nav-link {{ (Str::startsWith(request()->route()->getName(), 'admix.pages')) ? 'active' : '' }}"
           href="{{ route('admix.pages.index') }}"
           aria-expanded="{{ (Str::startsWith(request()->route()->getName(), 'admix.pages')) ? 'true' : 'false' }}">
        <span class="nav-icon">
            <i class="icon {{ config('admix-pages.icon') }}"></i>
        </span>
            <span class="nav-text">
            {{ config('admix-pages.name') }}
        </span>
        </a>
    </li>
@endcan
