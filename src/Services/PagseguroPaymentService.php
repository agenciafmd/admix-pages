<?php

namespace Agenciafmd\Payments\Services;

use Agenciafmd\Frontend\Exceptions\RniServiceException;
use Agenciafmd\Support\Helper;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Illuminate\Support\Facades\Cache;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class PagseguroPaymentService
{
    protected $client;

    public function __construct()
    {
        $logger = new Logger('pagseguro-payment');
        $logger->pushHandler(
            new StreamHandler(storage_path('logs/pagseguro-payment-' . date('Y-m-d') . '.log')),
            Logger::DEBUG
        );

        $stack = HandlerStack::create();
        $stack->push(
            Middleware::log(
                $logger,
                new MessageFormatter("{method} {uri} HTTP/{version} {req_body} | RESPONSE: {code} - {res_body}")
            )
        );

        $this->client = new Client([
                'timeout' => 60,
                'connect_timeout' => 60,
                'http_errors' => false,
                'verify' => false,
                'handler' => $stack,
            ]
        );
    }

    public function createSession()
    {
        if (config('services.pagseguro.email') && config("services.pagseguro.token")) {
            $options = [
                'query' => [
                    'email' => config('services.pagseguro.email'),
                    'token' => config("services.pagseguro.token")
                ]
            ];

            $response = $this->client->post(config('services.pagseguro.url') . '/v2/sessions', $options);

            $resp = json_decode(json_encode(simplexml_load_string((string)$response->getBody())));

            $respCode = json_decode((string)$response->getStatusCode());

            if ($respCode == 200) {
                $rs = $resp->id;
            }

            if ($respCode != 200) {
                $rs = true;
            }

            return $rs;
        }else{
            return false;
        }
    }

    public function createPlan($data = [])
    {
        if($data['trial'] > 0) {
            $plan = [
                'reference' => $data['reference'],
                'preApproval' => [
                    'name' => $data['name'],
                    'charge' => 'AUTO', // outro valor pode ser MANUAL
                    'period' => $data['period'], //WEEKLY, BIMONTHLY, TRIMONTHLY, SEMIANNUALLY, YEARLY
                    'amountPerPayment' => number_format($data['value'], 2), // obrigatório para o charge AUTO - mais que 1.00, menos que 2000.00
                    'trialPeriodDuration' => $data['trial'], //opcional
                    'details' => $data['description'], //opcional
                ]
            ];
        }else{
            $plan = [
                'reference' => $data['reference'],
                'preApproval' => [
                    'name' => $data['name'],
                    'charge' => 'AUTO', // outro valor pode ser MANUAL
                    'period' => $data['period'], //WEEKLY, BIMONTHLY, TRIMONTHLY, SEMIANNUALLY, YEARLY
                    'amountPerPayment' => number_format($data['value'], 2), // obrigatório para o charge AUTO - mais que 1.00, menos que 2000.00
                    'details' => $data['description'], //opcional
                ]
            ];
        }

        #dd($plan);
        if (config('services.pagseguro.email') && config("services.pagseguro.token")) {
            $options = [
                'headers' => [
                    'Accept' => 'application/vnd.pagseguro.com.br.v3+xml;charset=ISO-8859-1',
                    'Content-Type' => 'application/json',
                ],
                'query' => [
                    'email' => config('services.pagseguro.email'),
                    'token' => config("services.pagseguro.token")
                ],
                'json' => $plan,
            ];

            $response = $this->client->post(config('services.pagseguro.url').'/pre-approvals/request/', $options);

            $resp = json_decode(json_encode(simplexml_load_string((string)$response->getBody())));

            #dd($resp);

            $respCode = json_decode((string)$response->getStatusCode());

            if ($respCode == 200) {
                $rs = $resp;
            }

            if ($respCode != 200) {
                $rs = false;
            }
            return $rs;
        } else {
            return false;
        }
    }

    public function createCardToken($data = [])
    {
        if (config('services.pagseguro.email') && config("services.pagseguro.token")) {
            $body = [
                'sessionId' => $data['sessionId'],
                'amount' => $data['amount'],
                'cardNumber' => $data['cardNumber'],
                'cardBrand' => $data['cardBrand'],
                'cardCvv' => $data['cardCvv'],
                'cardExpirationMonth' => $data['cardExpirationMonth'],
                'cardExpirationYear' => $data['cardExpirationYear'],
            ];

            $options = [
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                'form_params' => $body,
            ];

            $response = $this->client->post('https://df.uol.com.br/v2/cards', $options);

            $resp = json_decode(json_encode(simplexml_load_string((string)$response->getBody())));

            $respCode = json_decode((string)$response->getStatusCode());

            if ($respCode == 200) {
                $rs = $resp;
            }

            if ($respCode != 200) {
                $rs = false;
            }
            return $rs;
        }else{
            return false;
        }
    }

    public function createAdhesionPlan($data = [])
    {
        $body = [
                "plan" => $data['plan'],
                "reference" => $data['reference'],
                "sender" => [
                    "name" => $data['name'],
                    "email" => $data['email'],
                    "ip" => getUserIpAddr(),
                    "phone" => [
                        "areaCode" => $data['ddd'],
                        "number" => $data['phone']
                    ],
                    "address" => [
                        "street" => $data['address'],
                        "number" => $data['number'],
                        "complement" => $data['complement'],
                        "district" => $data['neighborhood'],
                        "city" => $data['city'],
                        "state" => $data['state'],
                        "country" => "BRA",
                        "postalCode" => $data['zipcode']
                    ],
                    "documents" =>
                        [
                            [
                                "type" => "CPF",
                                "value" => only_numbers($data['cpf'])
                            ]
                        ]
                ],
                "paymentMethod" => [
                    "type" => "CREDITCARD",
                    "creditCard" => [
                        "token" => $data['card_token'],
                        "holder" => [
                            "name" => $data['name'],
                            "birthDate" => $data['dob']->format('d/m/Y'),
                            "documents" =>
                                [
                                    [
                                        "type" => "CPF",
                                        "value" => only_numbers($data['cpf'])
                                    ]
                                ],
                            "billingAddress" => [
                                "street" => $data['address'],
                                "number" => $data['number'],
                                "complement" => $data['complement'],
                                "district" => $data['neighborhood'],
                                "city" => $data['city'],
                                "state" => $data['state'],
                                "country" => "BRA",
                                "postalCode" => $data['zipcode']
                            ],
                            "phone" => [
                                "areaCode" => $data['ddd'],
                                "number" => $data['phone']
                            ]
                        ]
                    ]
                ]
        ];
        #dd($body);
        if (config('services.pagseguro.email') && config("services.pagseguro.token")) {
            $options = [
                'headers' => [
                    'Accept' => 'application/vnd.pagseguro.com.br.v3+json;charset=ISO-8859-1',
                    'Content-Type' => 'application/json',
                ],
                'query' => [
                    'email' => config('services.pagseguro.email'),
                    'token' => config("services.pagseguro.token")
                ],
                'json' => $body,
            ];

            $response = $this->client->post(config('services.pagseguro.url').'/pre-approvals', $options);

            $resp = json_decode((string)$response->getBody());

            $respCode = json_decode((string)$response->getStatusCode());

            if ($respCode == 200) {
                $rs = $resp;
            }

            if ($respCode != 200) {
                $rs = false;
            }
            return $rs;
        } else {
            return false;
        }
    }

    public function consultAdhesion(string $code)
    {
//        if(app()->environment('local')){
//            $code = 'E3436D590E0EE20884B9DFA02A245B3A';
//        }
        if (config('services.pagseguro.email') && config("services.pagseguro.token")) {

            $options = [
                'headers' => [
                    'Accept' => 'application/vnd.pagseguro.com.br.v3+json;charset=ISO-8859-1',
                    'Content-Type' => 'application/json',
                ],
                'query' => [
                    'email' => config('services.pagseguro.email'),
                    'token' => config("services.pagseguro.token")
                ]
            ];

            $response = $this->client->get(config('services.pagseguro.url').'/pre-approvals/'.$code, $options);
            $resp = json_decode((string)$response->getBody());

            if ($resp->status == 'Error') {
                return [
                    'code' => 406,
                    'message' => $resp->msgErr,
                ];
            }

            if ($resp->status == "Success") {
                foreach ($resp->result as $indication) {
                    $rs['result'][] = ['leadid' => $indication->leadid,
                        "leadnome" => $indication->leadnome,
                        "empreendimentocod" => $indication->empreendimentocod,
                        "empreendimentonome" => $indication->empreendimentonome,
                        "dddprincipal" => $indication->dddprincipal,
                        "telefoneprincipal" => $indication->telefoneprincipal,
                        "cpfindicador" => $indication->cpfindicador,
                        "leadstatus" => $indication->leadstatus,
                        "leadcodigo" => $indication->leadcodigo,
                        "oppid" => $indication->oppid,
                        "oppdeletado" => $indication->oppdeletado,
                        "oppstagename" => ($indication->oppstagename) ?: null,
                        "oppcodigooportunidade" => $indication->oppcodigooportunidade,
                        "oppdatavenda" => $indication->oppdatavenda,
                    ];

                }
                $rs['status'] = "Success";
                $rs['code'] = null;
                return $rs;
            }
        } else {
            return false;
        }
    }

    public function getPaymentOrders(string $preApprovalCode)
    {
        if (config('services.pagseguro.email') && config("services.pagseguro.token")) {
            $response = $this->client->get(config('services.pagseguro.url')."/pre-approvals/".$preApprovalCode."/payment-orders", [
                'headers' => [
                    'Accept' => 'application/vnd.pagseguro.com.br.v3+json;charset=ISO-8859-1',
                    'Content-Type' => 'application/json',
                ],
                'query' => [
                    'email' => config('services.pagseguro.email'),
                    'token' => config("services.pagseguro.token")
                ]
            ]);

            $resp = json_decode((string)$response->getBody());

            #dd($resp);

            $respCode = json_decode((string)$response->getStatusCode());

            if ($respCode == 200) {
                $rs = $resp;
            }

            if ($respCode != 200) {
                $rs = false;
            }
            return $rs;
        }else{
            return false;
        }
    }

    public function getPaymentOrdersByDateRange(string $initialDate, string $finalDate)
    {
        if (config('services.pagseguro.email') && config("services.pagseguro.token")) {
            $response = $this->client->get(config('services.pagseguro.url')."/pre-approvals/", [
                'headers' => [
                    'Accept' => 'application/vnd.pagseguro.com.br.v3+json;charset=ISO-8859-1',
                    'Content-Type' => 'application/json',
                ],
                'query' => [
                    'email' => config('services.pagseguro.email'),
                    'token' => config("services.pagseguro.token"),
                    'initialDate' => Carbon::parse($initialDate)->format('Y-m-d\TH:i'),
                    'finalDate' => Carbon::parse($finalDate)->format('Y-m-d\TH:i')
                ]
            ]);

            $resp = json_decode((string)$response->getBody());

            #dd($resp);

            $respCode = json_decode((string)$response->getStatusCode());

            if ($respCode == 200) {
                $rs = $resp;
            }

            if ($respCode != 200) {
                $rs = false;
            }
            return $rs;
        }else{
            return false;
        }
    }

    public function changePaymentForm($data = [])
    {
        $body = [
            "type" => "CREDITCARD",
            "sender" => [
                    "ip" => getUserIpAddr(),
            ],
            "creditCard" => [
                    "token" => $data['card_token'],
                    "holder" => [
                        "name" => $data['name'],
                        "birthDate" => $data['dob']->format('d/m/Y'),
                        "documents" =>
                            [
                                [
                                    "type" => "CPF",
                                    "value" => $data['cpf']
                                ]
                            ],
                        "phone" => [
                            "areaCode" => $data['ddd'],
                            "number" => $data['phone']
                        ],
                        "billingAddress" => [
                            "street" => $data['address'],
                            "number" => $data['number'],
                            "complement" => $data['complement'],
                            "district" => $data['neighborhood'],
                            "city" => $data['city'],
                            "state" => $data['state'],
                            "country" => "BRA",
                            "postalCode" => $data['zipcode']
                        ],

                    ]
            ]
        ];

        #dd($body);
        if (config('services.pagseguro.email') && config("services.pagseguro.token")) {
            $options = [
                'headers' => [
                    'Accept' => 'application/vnd.pagseguro.com.br.v3+json;charset=ISO-8859-1',
                    'Content-Type' => 'application/json',
                ],
                'query' => [
                    'email' => config('services.pagseguro.email'),
                    'token' => config("services.pagseguro.token")
                ],
                'json' => $body,
            ];

            $response = $this->client->put(config('services.pagseguro.url').'/pre-approvals/'.$data["payment_code"].'/payment-method', $options);

            $resp = json_decode((string)$response->getBody());

            $respCode = json_decode((string)$response->getStatusCode());

            if ($respCode == 200) {
                $rs = $resp;
            }

            if ($respCode != 200) {
                $rs = false;
            }
            return $rs;
        } else {
            return false;
        }
    }

    public function getNotification(string $notificationCode)
    {
        if (config('services.pagseguro.email') && config("services.pagseguro.token")) {
            $response = $this->client->get(config('services.pagseguro.url') . "/v3/transactions/notifications/" . $notificationCode, [
                'query' => [
                    'email' => config('services.pagseguro.email'),
                    'token' => config("services.pagseguro.token")
                ]
            ]);

            $responseBody = utf8_decode((string)$response->getBody());
            $xml = simplexml_load_string($responseBody);

            dd($xml);

            $ids = explode(', ', $xml->reference);
            $payments = Payment::whereIn('id', $ids)
                ->get();

            // ignora todas as notificações maiores que 4 (Disponível|Em disputa|Devolvida|Cancelada)
            if ($xml->status > 4) {
                return "not";
            }

            if ($xml->status <= 2) {
                foreach ($payments as $payment) {
                    $payment->pagseguro = now();
                    $payment->save();
                }

                return "not"; // notificação diferente de pago
            }

            if (($xml->status == 3) || ($xml->status == 4)) {
                foreach ($payments as $payment) {
                    $payment->pagseguro = null;
                    $payment->payment = now();
                    $payment->save();
                }
            }

        }else{
            return false;
        }
    }

    public function reactiveAdhesion($data = [])
    {
        $body = [
            "status" => "ACTIVE",
        ];

        if (config('services.pagseguro.email') && config("services.pagseguro.token")) {
            $options = [
                'headers' => [
                    'Accept' => 'application/vnd.pagseguro.com.br.v3+json;charset=ISO-8859-1',
                    'Content-Type' => 'application/json',
                ],
                'query' => [
                    'email' => config('services.pagseguro.email'),
                    'token' => config("services.pagseguro.token")
                ],
                'json' => $body,
            ];

            $response = $this->client->put(config('services.pagseguro.url').'/pre-approvals/'.$data["payment_code"].'/status', $options);

            $resp = json_decode((string)$response->getBody());

            $respCode = json_decode((string)$response->getStatusCode());

            if ($respCode == 200) {
                $rs = $resp;
            }

            if ($respCode != 200) {
                $rs = false;
            }
            return $rs;
        } else {
            return false;
        }
    }

    public function cancelAdhesion($data = [])
    {
        if (config('services.pagseguro.email') && config("services.pagseguro.token")) {
            $options = [
                'headers' => [
                    'Accept' => 'application/vnd.pagseguro.com.br.v3+json;charset=ISO-8859-1',
                    'Content-Type' => 'application/json',
                ],
                'query' => [
                    'email' => config('services.pagseguro.email'),
                    'token' => config("services.pagseguro.token")
                ],
            ];

            $response = $this->client->put(config('services.pagseguro.url').'/pre-approvals/'.$data["payment_code"].'/cancel', $options);

            $resp = json_decode((string)$response->getBody());

            $respCode = json_decode((string)$response->getStatusCode());

            if ($respCode == 200) {
                $rs = $resp;
            }

            if ($respCode != 200) {
                $rs = false;
            }
            return $rs;
        } else {
            return false;
        }
    }

    public function suspendAdhesion($data = [])
    {
        $body = [
            "status" => "SUSPENDED",
        ];

        if (config('services.pagseguro.email') && config("services.pagseguro.token")) {
            $options = [
                'headers' => [
                    'Accept' => 'application/vnd.pagseguro.com.br.v3+json;charset=ISO-8859-1',
                    'Content-Type' => 'application/json',
                ],
                'query' => [
                    'email' => config('services.pagseguro.email'),
                    'token' => config("services.pagseguro.token")
                ],
                'json' => $body,
            ];

            $response = $this->client->put(config('services.pagseguro.url').'/pre-approvals/'.$data["payment_code"].'/status', $options);

            $resp = json_decode((string)$response->getBody());

            $respCode = json_decode((string)$response->getStatusCode());

            if ($respCode == 200) {
                $rs = $resp;
            }

            if ($respCode != 200) {
                $rs = false;
            }
            return $rs;
        } else {
            return false;
        }
    }

    public function changePlanValue($data = [])
    {
        $body = [
            "amountPerPayment" => $data['new_value'],
            "updateSubscriptions" => $data['update_subscriptions']
        ];

        if (config('services.pagseguro.email') && config("services.pagseguro.token")) {
            $options = [
                'headers' => [
                    'Accept' => 'application/vnd.pagseguro.com.br.v3+json;charset=ISO-8859-1',
                    'Content-Type' => 'application/json',
                ],
                'query' => [
                    'email' => config('services.pagseguro.email'),
                    'token' => config("services.pagseguro.token")
                ],
                'json' => $body,
            ];

            $response = $this->client->put(config('services.pagseguro.url').'/pre-approvals/request/'.$data["plan_code"].'/payment', $options);

            $resp = json_decode((string)$response->getBody());

            $respCode = json_decode((string)$response->getStatusCode());

            if ($respCode == 200) {
                $rs = $resp;
            }

            if ($respCode != 200) {
                $rs = false;
            }
            return $rs;
        } else {
            return false;
        }
    }

    public function giveDiscount($data = [])
    {
        $body = [
            "value" => $data['value'],
            "type" => $data['type']
        ];

        if (config('services.pagseguro.email') && config("services.pagseguro.token")) {
            $options = [
                'headers' => [
                    'Accept' => 'application/vnd.pagseguro.com.br.v3+json;charset=ISO-8859-1',
                    'Content-Type' => 'application/json',
                ],
                'query' => [
                    'email' => config('services.pagseguro.email'),
                    'token' => config("services.pagseguro.token")
                ],
                'json' => $body,
            ];

            $response = $this->client->put(config('services.pagseguro.url').'/pre-approvals/'.$data["payment_code"].'/discount', $options);

            $resp = json_decode((string)$response->getBody());

            $respCode = json_decode((string)$response->getStatusCode());

            if ($respCode == 200) {
                $rs = $resp;
            }

            if ($respCode != 200) {
                $rs = false;
            }
            return $rs;
        } else {
            return false;
        }
    }
}
