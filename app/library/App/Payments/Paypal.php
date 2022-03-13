<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 06.11.17
 * Time: 16:25
 */

namespace App\Payments;

class Paypal
{
    /**
     * Последние сообщения об ошибках
     * @var array
     */
    protected $errors = [];

    /**
     * Данные API
     * Обратите внимание на то, что для песочницы нужно использовать соответствующие данные
     * @var array
     */
    protected static $credentials = [
        'USER' => 'seller_1297608781_biz_api1.lionite.com',
        'PWD' => '1297608792',
        'SIGNATURE' => 'A3g66.FS3NAf4mkHn3BDQdpo6JD.ACcPc4wMrInvUEqO3Uapovity47p',
    ];

    /**
     * Указываем, куда будет отправляться запрос
     * Реальные условия - https://api-3t.paypal.com/nvp
     * Песочница - https://api-3t.sandbox.paypal.com/nvp
     * @var string
     */
    protected $endPoint = 'https://api-3t.sandbox.paypal.com/nvp';

    /**
     * Версия API
     * @var string
     */
    protected $version = '74.0';

    /**
     * Сформировываем запрос
     *
     * @param string $method Данные о вызываемом методе перевода
     * @param array $params Дополнительные параметры
     * @return array | boolean Response array / boolean false on failure
     */
    public function request($method, array $params = [])
    {
        $this->errors = [];
        if (empty($method)) { // Проверяем, указан ли способ платежа
            $this->errors = ['Не указан метод перевода средств'];
            return false;
        }

        // Параметры нашего запроса
        $requestParams = array_merge(
            [
                'METHOD' => $method,
                'VERSION' => $this->version
            ],
            static::$credentials
        );

        $queryParams = array_merge($requestParams, $params);

        // Сформировываем данные для NVP
        $request = http_build_query($queryParams);

        // Настраиваем cURL
        $curlOptions = [
            CURLOPT_URL => $this->endPoint,
            CURLOPT_VERBOSE => 1,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_CAINFO => CERTIFICATE_DIR . '/cacert.pem', // Файл сертификата
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $request
        ];

        $ch = curl_init();
        curl_setopt_array($ch, $curlOptions);

        // Отправляем наш запрос, $response будет содержать ответ от API
        $response = curl_exec($ch);

        // Проверяем, нету ли ошибок в инициализации cURL
        if (curl_errno($ch)) {
            $this->errors = curl_error($ch);
            curl_close($ch);
            return false;
        }

        curl_close($ch);
        $responseArray = [];
        parse_str($response, $responseArray); // Разбиваем данные, полученные от NVP в массив
        return $responseArray;
    }
}
