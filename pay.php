<?php
require __DIR__ . '/vendor/autoload.php';

use YooKassa\Client;

$client = new Client();
$client->setAuth('1038975', 'test_PmEsM598zpNCyX2ufcrU3-DQ9hy5dOJ14lCvv8JCXwI');

$idempotenceKey = uniqid('', true);
$response = $client->createPayment(
    array(
        'amount' => array(
            'value' => '1.00',
            'currency' => 'RUB',
        ),
        'confirmation' => array(
            'type' => 'embedded',
        ),
        'capture' => true,
        'description' => 'ТЕСТ',
    ),
    $idempotenceKey
);

//get confirmation token
$confirmationToken = $response->getConfirmation()->getConfirmationToken();
$paymentId = $response->getId();
$payment = $client->getPaymentInfo($paymentId)->getStatus();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Прием платежа с помощью виджета ЮKassa</title>

    <!--Подключение библиотеки для инициализации виджета ЮKassa-->
    <script src="https://yookassa.ru/checkout-widget/v1/checkout-widget.js"></script>
</head>

<body>
    <div id="payment-form"></div>

    Данные банковской карты для оплаты в <b>тестовом магазине</b>:

    <b>5555 5555 5555 4477</b>

    <script>
        //Инициализация виджета. Все параметры обязательные.
        const checkout = new window.YooMoneyCheckoutWidget({
            confirmation_token: '<?= $confirmationToken ?>', //Токен, который перед проведением оплаты нужно получить от ЮKassa
            // return_url: 'http://localhost/rampus/profile', //Ссылка на страницу завершения оплаты, это может быть любая ваша страница

            customization: {
                // modal: true,
                // payment_methods: ['sbp']
            },
            error_callback: function(error) {
                console.log(error)
            }
        });

        //Отображение платежной формы в контейнере
        checkout.render('payment-form');

        checkout.on('success', () => {
            console.log('success')
            console.log('0', '<?php echo $client->getPaymentInfo($paymentId)->getStatus(); ?>')
            setTimeout(() => {
                console.log('1', '<?php echo $client->getPaymentInfo($paymentId)->getStatus(); ?>')
            }, 1000)
            setTimeout(() => {
                console.log('5', '<?php echo $client->getPaymentInfo($paymentId)->getStatus(); ?>')
            }, 5000)
            setTimeout(() => {
                console.log('10', '<?php echo $client->getPaymentInfo($paymentId)->getStatus(); ?>')
            }, 10000)
            setTimeout(() => {
                console.log('60', '<?php echo $client->getPaymentInfo($paymentId)->getStatus(); ?>')
            }, 60000)
            checkout.destroy();
        });
    </script>
</body>

</html>