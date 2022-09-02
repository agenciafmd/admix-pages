<?php

namespace Agenciafmd\Payments\Observers;

use Agenciafmd\Payments\Models\Payment;
use Agenciafmd\Users\Models\User;

class PaymentObserver
{
    public function saving(Payment $model)
    {
        $model->name = 'Pagamento: Usuário: '.$model->user->name.' - Plano: '.$model->plan->name.' - Status: '.$model->status;
    }


    public function saved(Payment $model)
    {
        $logFile = 'dcode-payments-observers-'.date('Y-m-d').'.log';

        $user = User::where('id',$model->user_id)
            ->first();
        if ($model->wasChanged('status') || $model->wasRecentlyCreated) {
            if ($model->status == "Não Pago") {
                if ($user) {
                    devlogs($model->id . ' - ' . $model->status . ' - usuario: p suspended', $logFile);
                    $user->update(['status' => 'SUSPENDED']);
                }
            }
            if ($model->status == "Pago") {
                if ($user) {
                    devlogs($model->id . ' - ' . $model->status . ' - usuario: p active', $logFile);
                    $user->update(['status' => 'ACTIVE']);
                }
            }
            if ($model->status == "Aguardando") {
                if ($user) {
                    devlogs($model->id . ' - ' . $model->status . ' - usuario: p waiting', $logFile);
                    $user->update(['status' => 'WAITING']);
                }
            }
            if ($model->status == "Cancelado") {
                if ($user) {
                    devlogs($model->id . ' - ' . $model->status . ' - usuario: p suspended', $logFile);
                    $user->update(['status' => 'SUSPENDED']);
                    // verificar se preciso tratar as ordens de pagamento
                    //  $orders = Order::isActive()->where('payment_id',$model->id)->get();
                    // não precisa pq a verificação de pagamentos não pega esses cancelados pega por pagamento
                }
            }
        }
    }
}
