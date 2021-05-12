<?php


namespace Quest;

use Madlib\Exception;
use Madlib\Config;
use Madlib\Input;
use Madlib\Page;
use Madlib\Curl;
use Madlib\Session;
use Madlib\Mysql;
use Madlib\Message;
use Madlib\Code;

class Paypal {

    protected Config $config;
    protected Input $input;
    protected Page $page;
    protected Curl $curl;
    protected Session $session;
    protected Mysql $mysql;
    protected Message $message;
    protected Code $code;

    public function __construct(
        Config $config,
        Input $input,
        Page $page,
        Curl $curl,
        Session $session,
        Mysql $mysql,
        Message $message,
        Code $code
    ) {
        $this->config = $config;
        $this->input = $input;
        $this->page = $page;
        $this->curl = $curl;
        $this->session = $session;
        $this->mysql = $mysql;
        $this->message = $message;
        $this->code = $code;
    }

    public function createPayment(): void {
        $amount = $this->config::QUEST['paypal_checkout_amount'];
        $commission = 0;
        $total = $amount + $commission;
        $currency = $this->config::QUEST['paypal_checkout_currency'];

        $client = $this->curl->getClient([
            'base_uri' => $this->config::QUEST['paypal_api'],
            'timeout' => $this->config::QUEST['paypal_curl_timeout'],
        ]);
        $response = $client->request('POST', 'v1/payments/payment', [
            'content-type' => 'application/json',
            'auth' =>
            [
                $this->config::QUEST['paypal_client'],
                $this->config::QUEST['paypal_secret'],
            ],
            'json' =>
            [
                'intent' => 'sale',
                'payer' =>
                [
                    'payment_method' => 'paypal',
                ],
                'transactions' =>
                [
                    [
                        'amount' =>
                        [
                            'total' => $total,
                            'currency' => $currency,
                        ],
                    ],
                ],
                'application_context' => [
                    'shipping_preference' => 'NO_SHIPPING',
                ],
                'redirect_urls' =>
                [
                   'return_url' => $this->config::QUEST['paypal_create_payment_return'],
                   'cancel_url' => $this->config::QUEST['paypal_create_payment_cancel'],
                ],
            ],
        ]);

        $statusCode = $response->getStatusCode();
        if ($statusCode != 201) { // 201 Created
            throw new Exception('Invalid response status code [' . $statusCode . ']');
        }

        $contents = $response->getBody()->getContents();
        $result = json_decode($contents);
        $paymentID = $result->id;

        $this->addPaymentLog(
            'create',
            $paymentID,
            $amount,
            $commission,
            $total,
            $currency,
            $contents,
        );

        echo json_encode([
            'id' => $paymentID,
        ]);
    }

    public function executePayment(): void {
        $paymentID = $this->input->getString('paymentID');
        $payerID = $this->input->getString('payerID');

        $paymentLog = $this->getPaymentLog($paymentID);
        $amount = $paymentLog['amount'];
        $commission = $paymentLog['commission'];
        $total = $paymentLog['total'];
        $currency = $paymentLog['currency'];

        $client = $this->curl->getClient([
            'base_uri' => $this->config::QUEST['paypal_api'],
            'timeout' => $this->config::QUEST['paypal_curl_timeout'],
        ]);
        $response = $client->request('POST', 'v1/payments/payment/' . $paymentID . '/execute', [
            'content-type' => 'application/json',
            'auth' =>
            [
                $this->config::QUEST['paypal_client'],
                $this->config::QUEST['paypal_secret'],
            ],
            'json' => [
                'payer_id' => $payerID,
                'transactions' => [
                    [
                        'amount' => [
                            'total' => $total,
                            'currency' => $currency,
                        ],
                    ],
                ],
            ],
        ]);

        if ($response->getStatusCode() != 200) {
            throw new Exception('Invalid response status code');
        }

        $contents = $response->getBody()->getContents();

        $paymentLogCode = $this->addPaymentLog(
            'execute',
            $paymentID,
            $amount,
            $commission,
            $total,
            $currency,
            $contents,
            $payerID,
        );

        $long = $this->mysql->esc($this->config::QUEST['paypal_checkout_subscription_long']);
        $user_id = (int)$this->session->get('user_id');
        if (!$this->mysql->update("UPDATE user SET subscribed_until_at = DATE_ADD(NOW(), INTERVAL $long) WHERE id = $user_id")) {
            throw new Exception('Subscription update failed');
        }

        $this->message->success('Subscription updated. Payment reference: ' . $paymentLogCode . ' - (Please login again to take effect)');
        // TODO: send email

        echo json_encode([
            'status' => 'success',
        ]);
    }

    protected function addPaymentLog(
        string $method,
        string $paymentID,
        float $amount,
        float $commission,
        float $total,
        string $currency,
        string $data,
        ?string $payerID = ''
    ): string {
        $payment_log = true;
        while ($payment_log) {
            $payment_log_code = $this->code->generate(10);
            $payment_log = $this->mysql->selectRow("SELECT payment_log_code FROM paypal_payment_log WHERE payment_log_code = '$payment_log_code'");
        }

        $user_id = (int)$this->session->get('user_id');
        $method = $this->mysql->esc($method);
        $paymentID = $this->mysql->esc($paymentID);
        $currency = $this->mysql->esc($currency);
        $data = $this->mysql->esc($data);
        $payerID = $this->mysql->esc($payerID);
        $query = "
            INSERT INTO paypal_payment_log (user_id, payment_log_code, method, paymentID, amount, commission, total, currency, data, payerID)
            VALUES ($user_id, '$payment_log_code', '$method', '$paymentID', $amount, $commission, $total, '$currency', '$data', '$payerID')
        ";
        if (!$this->mysql->insert($query)) {
            throw new Exception('Payment log fail: ' . $query);
        }

        return $payment_log_code;
    }

    protected function getPaymentLog(string $paymentID, string $method = 'create'): array
    {
        $user_id = (int)$this->session->get('user_id');
        $paymentID = $this->mysql->esc($paymentID);
        $method = $this->mysql->esc($method);
        $query = "
            SELECT * FROM paypal_payment_log WHERE paymentID = '$paymentID' AND method = '$method' AND user_id = $user_id 
        ";
        $paymentLog = $this->mysql->selectRow($query);
        if (!$paymentLog) {
            throw new Exception('Payment is not found');
        }
        return $paymentLog;
    }
}