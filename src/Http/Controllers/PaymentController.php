<?php

namespace Agenciafmd\Payments\Http\Controllers;

use Agenciafmd\Payments\Models\Payment;
use Agenciafmd\Payments\Http\Requests\PaymentRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        session()->put('backUrl', request()->fullUrl());

        $query = QueryBuilder::for(Payment::class)
            ->defaultSorts(config('local-payments.default_sort'))
            ->allowedSorts($request->sort)
            ->allowedFilters(array_merge((($request->filter) ? array_keys(array_diff_key($request->filter, array_flip(['id', 'is_active']))) : []), [
                AllowedFilter::exact('id'),
                AllowedFilter::exact('is_active'),
            ]));

        if ($request->is('*/trash')) {
            $query->onlyTrashed();
        }

        $view['items'] = $query->paginate($request->get('per_page', 50));

        return view('agenciafmd/payments::index', $view);
    }

    public function create(Payment $payment)
    {
        $view['model'] = $payment;

        return view('agenciafmd/payments::form', $view);
    }

    public function store(PaymentRequest $request)
    {
        if ($payment = Payment::create($request->validated())) {
            flash('Item inserido com sucesso.', 'success');
        } else {
            flash('Falha no cadastro.', 'danger');
        }

        return ($url = session()->get('backUrl')) ? redirect($url) : redirect()->route('admix.payments.index');
    }

    public function show(Payment $payment)
    {
        $view['model'] = $payment;

        return view('agenciafmd/payments::form', $view);
    }

    public function edit(Payment $payment)
    {
        $view['model'] = $payment;

        return view('agenciafmd/payments::form', $view);
    }

    public function update(Payment $payment, PaymentRequest $request)
    {
        if ($payment->update($request->validated())) {
            flash('Item atualizado com sucesso.', 'success');
        } else {
            flash('Falha na atualização.', 'danger');
        }

        return ($url = session()->get('backUrl')) ? redirect($url) : redirect()->route('admix.payments.index');
    }

    public function destroy(Payment $payment)
    {
        if ($payment->delete()) {
            flash('Item removido com sucesso.', 'success');
        } else {
            flash('Falha na remoção.', 'danger');
        }

        return ($url = session()->get('backUrl')) ? redirect($url) : redirect()->route('admix.payments.index');
    }

    public function restore($id)
    {
        $payment = Payment::onlyTrashed()
            ->find($id);

        if (!$payment) {
            flash('Item já restaurado.', 'danger');
        } elseif ($payment->restore()) {
            flash('Item restaurado com sucesso.', 'success');
        } else {
            flash('Falha na restauração.', 'danger');
        }

        return ($url = session()->get('backUrl')) ? redirect($url) : redirect()->route('admix.payments.index');
    }

    public function batchDestroy(Request $request)
    {
        if (Payment::destroy($request->get('id', []))) {
            flash('Item removido com sucesso.', 'success');
        } else {
            flash('Falha na remoção.', 'danger');
        }

        return ($url = session()->get('backUrl')) ? redirect($url) : redirect()->route('admix.payments.index');
    }

    public function batchRestore(Request $request)
    {
        $payment = Payment::onlyTrashed()
            ->whereIn('id', $request->get('id', []))
            ->restore();

        if ($payment) {
            flash('Item restaurado com sucesso.', 'success');
        } else {
            flash('Falha na restauração.', 'danger');
        }

        return ($url = session()->get('backUrl')) ? redirect($url) : redirect()->route('admix.payments.index');
    }
}
