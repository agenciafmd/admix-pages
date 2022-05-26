@extends('agenciafmd/admix::partials.crud.index', [
    'route' => (request()->is('*/trash') ? route('admix.pages.trash') : route('admix.pages.index'))
])

@section('title')
    @if(request()->is('*/trash'))
        Lixeira de
    @endif
    {{ config('admix-pages.name') }}
@endsection

@section('actions')
    @if(request()->is('*/trash'))
        @include('agenciafmd/admix::partials.btn.back', ['url' => route('admix.pages.index')])
    @else
        @can('create', \Agenciafmd\Pages\Models\Page::class)
            @include('agenciafmd/admix::partials.btn.create', ['url' => route('admix.pages.create'), 'label' => config('admix-pages.name')])
        @endcan
        @can('restore', \Agenciafmd\Pages\Models\Page::class)
            @include('agenciafmd/admix::partials.btn.trash', ['url' => route('admix.pages.trash')])
        @endcan
    @endif
@endsection

@section('batch')
    @if(request()->is('*/trash'))
        @can('restore', \Agenciafmd\Pages\Models\Page::class)
            {{ Form::select('batch', ['' => 'com os selecionados', route('admix.pages.batchRestore') => '- restaurar'], null, ['class' => 'js-batch-select form-control custom-select']) }}
        @endcan
    @else
        @can('delete', \Agenciafmd\Pages\Models\Page::class)
            {{ Form::select('batch', ['' => 'com os selecionados', route('admix.pages.batchDestroy') => '- remover'], null, ['class' => 'js-batch-select form-control custom-select']) }}
        @endcan
    @endif
@endsection

@section('filters')
@endsection

@section('table')
    @if($items->count() > 0)
        <div class="table-responsive">
            <table class="table table-striped table-borderless table-vcenter card-table text-nowrap">
                <thead>
                <tr>
                    <th class="w-1 d-none d-md-table-cell">&nbsp;</th>
                    <th class="w-1">{!! column_sort('#', 'id') !!}</th>
                    <th>{!! column_sort('Nome', 'name') !!}</th>
                    <th class="w-1">{!! column_sort('Ativo', 'is_active') !!}</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($items as $item)
                    <tr>
                        <td class="d-none d-md-table-cell">
                            <label class="mb-1 custom-control custom-checkbox">
                                <input type="checkbox" class="js-check custom-control-input"
                                       name="check[]" value="{{ $item->id }}">
                                <span class="custom-control-label">&nbsp;</span>
                            </label>
                        </td>
                        <td><span class="text-muted">{{ $item->id }}</span></td>
                        <td>{{ $item->name }}</td>
                        <td>
                            @include('agenciafmd/admix::partials.label.star', ['star' => $item->star])
                        </td>
                        <td>
                            @livewire('admix::is-active', ['myModel' => get_class($item), 'myId' => $item->id])
                        </td>
                        @if(request()->is('*/trash'))
                            <td class="w-1 text-right">
                                @include('agenciafmd/admix::partials.btn.restore', ['url' => route('admix.pages.restore', $item->id)])
                            </td>
                        @else
                            <td class="w-1 text-center">
                                <div class="item-action dropdown">
                                    <a href="#" data-toggle="dropdown" class="icon">
                                        <i class="icon fe-more-vertical text-muted"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        @can('update', \Agenciafmd\Pages\Models\Page::class)
                                            @include('agenciafmd/admix::partials.btn.edit', ['url' => route('admix.pages.edit', $item->id)])
                                        @endcan
                                        @can('delete', \Agenciafmd\Pages\Models\Page::class)
                                            @include('agenciafmd/admix::partials.btn.remove', ['url' => route('admix.pages.destroy', $item->id)])
                                        @endcan
                                    </div>
                                </div>
                            </td>
                        @endif
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        {!! $items->appends(request()->except(['page']))->links() !!}
    @else
        @include('agenciafmd/admix::partials.info.not-found')
    @endif
@endsection
