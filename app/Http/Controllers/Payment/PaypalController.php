<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PayPal\Rest\ApiContext;
use App\Http\Controllers\Payment\Payment as PaymentInterface;
use Validator;
use URL;
use Session;
use Redirect;
use Input;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\ExecutePayment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\Transaction;

class PaypalController extends Controller
{
    private $_api_context;

    public function __construct()
    {
        $paypal_configuration = config('paypal');
        $this->_api_context = new ApiContext(new OAuthTokenCredential($paypal_configuration['client_id'], $paypal_configuration['secret']));
        $this->_api_context->setConfig($paypal_configuration['settings']);
    }

    public function paymentWithPaypal(Request $request)
    {
        $payer = new Payer();
        $payer->setPaymentMethod('paypal');
        // set order items
        $item_1 = new Item();

        $item_1->setName('Product 1')->setCurrency('USD')->setQuantity(1)->setPrice('100');

        $item_list = new ItemList();
        $item_list->setItems(array($item_1));

        // order total
        $amount = new Amount();
        $amount->setCurrency('USD')->setTotal('100');

        // create transaction
        $transaction = new Transaction();
        $transaction->setAmount($amount)->setItemList($item_list)->setDescription('Enter Your transaction description');

        // add success and cancel urls
        $redirect_urls = new RedirectUrls();
        $redirect_urls->setReturnUrl(route('status'))->setCancelUrl(route('status'));

        // initialize payment object
        $payment = new Payment();
        $payment->setIntent('Sale')->setPayer($payer)->setRedirectUrls($redirect_urls)->setTransactions(array($transaction));
        try {
            $payment->create($this->_api_context);
        } catch (\PayPal\Exception\PPConnectionException $ex) {
            if (\Config::get('app.debug')) {
                \Session::put('error','Connection timeout');
                return Redirect::route('checkout');
            } else {
                \Session::put('error','Some error occur, sorry for inconvenient');
                return Redirect::route('checkout');
            }
        }
        foreach($payment->getLinks() as $link) {
            if($link->getRel() == 'approval_url') {
                $redirect_url = $link->getHref();
                break;
            }
        }

        \Session::put('paypal_payment_id', $payment->getId());

        if(isset($redirect_url)) {
            return \Redirect::away($redirect_url);
        }

        \Session::put('error','Unknown error occurred');
        return \Redirect::route('checkout');
    }

    public function getPaymentStatus(Request $request)
    {
        $payment_id = Session::get('paypal_payment_id');

        Session::forget('paypal_payment_id');
        if (empty($request->input('PayerID')) || empty($request->input('token'))) {
            \Session::put('error','Payment failed');
            return Redirect::route('checkout');
        }
        $payment = Payment::get($payment_id, $this->_api_context);
        $execution = new PaymentExecution();
        $execution->setPayerId($request->input('PayerID'));
        $result = $payment->execute($execution, $this->_api_context);
        if ($result->getState() == 'approved') {
            return Redirect::route('checkout');
        }

        \Session::put('error','Payment failed !!');
        return Redirect::route('checkout');
    }


}
