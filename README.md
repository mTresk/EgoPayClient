# Клиент для интернет-эквайринга EgoPay

## Возможности

Проведение платежа

Получение статуса платежа

## Установка

Установите пакет с помощью composer:

```bash
composer require mtresk/ego-pay-client
```

## Использование

Пример класса

```php
use mTresk\EgoPayClient\EgoPay;

class PaymentService
{
    private EgoPay $client;

    public function __construct()
    {
        $this->client = $this->createClient();
    }

    // Создаем клиент
    public function createClient(): EgoPay
    {
        $login = // логин;
        $password = // пароль;
        $shopId = // id магазина;
        $location = // ссылка сервиса оплаты;
        $uri = // ссылка на сайт;
        $urlOk = // ссылка на удачную оплату;
        $urlFault = // ссылка на неудачную оплату;

        return new EgoPay($shopId, $login, $password, $location, $uri, $urlOk, $urlFault);
    }


    // Создаем платеж
    public function createPayment()
    {
        $payment = [
            'amount' => 1000,
            'order_number' => 001,
            // Опционально
            'customer_id' => 1,
            'customer_name' => 'Maxim Tresk',
            'customer_phone' => '+7(999)999-99-99',
            'customer_email' => 'test@test.ru',
        ];

        // Возвращаем ссылку на оплату
        return $this->client->register($payment);

    }

    public function updateStatus()
    {
        // Получаем статус заказа
        return $this->client->getStatus(001);
    }
}
```
