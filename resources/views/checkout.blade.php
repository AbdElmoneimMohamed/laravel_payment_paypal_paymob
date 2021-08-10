@extends('layouts.app')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
    body {
        font-family: Arial;
        font-size: 17px;
        padding: 8px;
    }

    * {
        box-sizing: border-box;
    }

    .row {
        display: -ms-flexbox; /* IE10 */
        display: flex;
        -ms-flex-wrap: wrap; /* IE10 */
        flex-wrap: wrap;
        margin: 0 -16px;
    }

    .col-25 {
        -ms-flex: 25%; /* IE10 */
        flex: 25%;
    }

    .col-50 {
        -ms-flex: 50%; /* IE10 */
        flex: 50%;
    }

    .col-75 {
        -ms-flex: 75%; /* IE10 */
        flex: 75%;
    }

    .col-25,
    .col-50,
    .col-75 {
        padding: 0 16px;
    }

    .container {
        background-color: #f2f2f2;
        padding: 5px 20px 15px 20px;
        border: 1px solid lightgrey;
        border-radius: 3px;
    }

    input[type=text] {
        width: 100%;
        margin-bottom: 20px;
        padding: 12px;
        border: 1px solid #ccc;
        border-radius: 3px;
    }

    label {
        margin-bottom: 10px;
        display: block;
    }

    .icon-container {
        margin-bottom: 20px;
        padding: 7px 0;
        font-size: 24px;
    }

    .btn {
        background-color: #04AA6D !important;
        color: white;
        padding: 12px;
        margin: 10px 0;
        border: none;
        width: 100%;
        border-radius: 3px;
        cursor: pointer;
        font-size: 17px;
    }

    .btn:hover {
        background-color: #45a049;
    }

    a {
        color: #2196F3;
    }

    hr {
        border: 1px solid lightgrey;
    }

    span.price {
        float: right;
        color: grey;
    }
    /* Responsive layout - when the screen is less than 800px wide, make the two columns stack on top of each other instead of next to each other (also change the direction - make the "cart" column go on top) */
    @media (max-width: 800px) {
        .row {
            flex-direction: column-reverse;
        }
        .col-25 {
            margin-bottom: 20px;
        }
    }
</style>
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @if ($message = \Session::get('success'))
                <div class="custom-alerts alert alert-success">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                    {!! $message !!}
                </div>
                <?php \Session::forget('success');?>
            @endif

            @if ($message = \Session::get('error'))
                <div class="custom-alerts alert alert-danger">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                    {!! $message !!}
                </div>
                <?php \Session::forget('error');?>
            @endif
            <h2 style="text-align: center"> Checkout Form</h2>
            <div class="col-md-12">
                <div class="col-75">
                    <div class="container">

                        <div class="row">
                            <div class="col-md-6">
                                <form action="{{route('paypalPayment')}}" method="POST">
                                    {{ csrf_field() }}
                                    <input type="hidden" id="payment_type" name="payment_type" value="{{\App\Models\Order::PAYMENT_TYPE_PAYPAL}}">
                                    <input type="hidden" id="payment_status" name="payment_status" value="{{\App\Models\Order::PAYMENT_STATUS_PENDING}}">
                                    <input type="hidden" id="cost" name="cost" value="100">
                                    <input type="hidden" id="type" name="type" value="paypal">
                                    <input type="submit" value="Paypal" class="btn">
                                </form>
                            </div>

                            <div class="col-md-6">
                                <form action="{{route('credit')}}" method="POST">
                                    {{ csrf_field() }}
                                    <input type="hidden" id="payment_type" name="payment_type" value="{{\App\Models\Order::PAYMENT_TYPE_PAYPAL}}">
                                    <input type="hidden" id="payment_status" name="payment_status" value="{{\App\Models\Order::PAYMENT_STATUS_PENDING}}">
                                    <input type="hidden" id="cost" name="cost" value="100">
                                    <input type="hidden" id="type" name="type" value="credit">
                                    <input type="submit" value="Paymob" class="btn">
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            </body>
        </div>
    </div>
</div>
@endsection
