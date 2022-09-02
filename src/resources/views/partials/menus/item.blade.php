@can('view', \Agenciafmd\Payments\Models\Payment::class)
    <li class="nav-item">
        <a class="nav-link {{ (Str::startsWith(request()->route()->getName(), 'admix.payments')) ? 'active' : '' }}"
           href="{{ route('admix.payments.index') }}"
           aria-expanded="{{ (Str::startsWith(request()->route()->getName(), 'admix.payments')) ? 'true' : 'false' }}">
        <span class="nav-icon">
            <i class="icon {{ config('local-payments.icon') }}"></i>
        </span>
            <span class="nav-text">
            {{ config('local-payments.name') }}
        </span>
        </a>
    </li>
@endcan
