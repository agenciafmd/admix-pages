@extends('agenciafmd/admix::partials.crud.form')

@inject('planService', '\Agenciafmd\Plans\Services\PlanService')
@inject('userService', '\Agenciafmd\Users\Services\UsersService')

@section('form')
    {{ Form::bsOpen(['model' => optional($model), 'create' => route('admix.payments.store'), 'update' => route('admix.payments.update', ['payment' => ($model->id) ?? 0])]) }}
    <div class="card-header bg-gray-lightest">
        <h3 class="card-title">
            @if(request()->is('*/create'))
                Criar
            @elseif(request()->is('*/edit'))
                Editar
            @endif
            {{ config('local-payments.name') }}
        </h3>
        <div class="card-options">
            @include('agenciafmd/admix::partials.btn.save')
        </div>
    </div>
    <ul class="list-group list-group-flush">

        @if (optional($model)->id)
            {{ Form::bsText('Código', 'id', null, ['disabled' => true]) }}
        @endif

        {{ Form::bsIsActive('Ativo', 'is_active', null, ['required']) }}

        {{ Form::bsText('Nome', 'name', null, []) }}

        {{ Form::bsText('Código Adesão', 'code', null, []) }}

        {{ Form::bsSelect('Status', 'status', ['-' => '', 'Pago' => 'Pago', 'Não Pago' => 'Não Pago'] , null, ['required']) }}

        {{ Form::bsSelect('Usuário', 'user_id', $userService->lists()->prepend('-', '') , null, ['required']) }}

        {{ Form::bsSelect('Plano', 'plan_id', $planService->lists()->prepend('-', '') , null, ['required']) }}

        {{ Form::bsText('Número do Cartão', 'payment_card_number', null, []) }}

        {{ Form::bsDateTime('Data do Pagamento', 'payment_date', null, []) }}

        {{ Form::bsText('Valor', 'value', null, ['required']) }}

        {{ Form::bsTextarea('Descrição', 'description', null) }}

    </ul>
    <div class="card-footer bg-gray-lightest text-right">
        <div class="d-flex">
            @include('agenciafmd/admix::partials.btn.back')
            @include('agenciafmd/admix::partials.btn.save')
        </div>
    </div>
    {{ Form::close() }}
@endsection
