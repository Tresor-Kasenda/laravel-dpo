<?php

namespace Scott\LaravelDpo\DpoConnector;

use Scott\LaravelDpo\LaravelDpoException;

class Commander
{
    public function __construct(
        public LaravelDpoConnector $connector,
    ) {
    }

    public function getGateway(): string
    {
        return $this->connector->dpo_url.$this->connector->gateway_url;
    }

    /**
     * @throws \Exception
     */
    public function createToken(array $data): array
    {
        $company_token = $this->connector->company_token;
        $company_type = $this->connector->company_type;
        $payment_amount = $data['paymentAmount'];
        $payment_currency = $data['paymentCurrency'];
        $customerFirstName = $data['customerFirstName'];
        $customerLastName = $data['customerLastName'];
        $customerAddress = $data['customerAddress'];
        $customerCity = $data['customerCity'];
        $customerPhone = $data['customerPhone'];
        $redirectURL = $this->connector->redirect_url;
        $backURL = $this->connector->back_url;
        $customerEmail = $data['customerEmail'];
        $reference = $data['companyRef'];

        $date = date('Y/m/d H:i');
        $postXml = <<<LARAVELDPOXML
<?xml version="1.0" encoding="utf-8"?>
<API3G>
<CompanyToken>$company_token</CompanyToken>
<Request>createToken</Request>
<Transaction>
<PaymentAmount>$payment_amount</PaymentAmount>
<PaymentCurrency>$payment_currency</PaymentCurrency>
<CompanyRef>$reference</CompanyRef>
<customerFirstName>$customerFirstName</customerFirstName>
<customerLastName>$customerLastName</customerLastName>
<customerAddress>$customerAddress</customerAddress>
<customerCity>$customerCity</customerCity>
<customerPhone>$customerPhone</customerPhone>
 <RedirectURL>$redirectURL</RedirectURL>
<BackURL>$backURL</BackURL>
<customerEmail>$customerEmail</customerEmail>
<TransactionSource>whmcs</TransactionSource>
</Transaction>
<Services>
<Service>
<ServiceType>$company_type</ServiceType>
<ServiceDescription>$reference</ServiceDescription>
<ServiceDate>$date</ServiceDate>
</Service>
</Services>
</API3G>
LARAVELDPOXML;

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $this->connector->dpo_url.'/API/v6/',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $postXml,
            CURLOPT_HTTPHEADER => [
                'cache-control: no-cache',
            ],
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);

        curl_close($curl);

        if ($response === '') {
            return [
                'success' => false,
                'result' => ! empty($error) ? $error : 'Unknown error occurred in token creation',
                'resultExplanation' => ! empty($error) ? $error : 'Unknown error occurred in token creation',
            ];
        }

        $xml = new \SimpleXMLElement($response);
        $result = $xml->xpath('Result')[0]->__toString();
        $resultExplanation = $xml->xpath('ResultExplanation')[0]->__toString();
        $returnedResult = [
            'result' => $result,
            'resultExplanation' => $resultExplanation,
        ];

        if ($xml->xpath('Result')[0] != '000') {
            $returnedResult['success'] = 'false';
        } else {
            $transToken = $xml->xpath('TransToken')[0]->__toString();
            $transRef = $xml->xpath('TransRef')[0]->__toString();
            $returnedResult['success'] = 'true';
            $returnedResult['transToken'] = $transToken;
            $returnedResult['transRef'] = $transRef;
        }

        return $returnedResult;
    }

    /**
     * @throws LaravelDpoException
     */
    public function verificationToken(array $data)
    {
        $companyToken = $data['companyToken'];
        $transToken = $data['transToken'];

        try {
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => $this->connector->dpo_url.'/API/v6/',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => "<?xml version=\"1.0\" encoding=\"utf-8\"?>\r\n<API3G>\r\n  <CompanyToken>".$companyToken."</CompanyToken>\r\n  <Request>verifyToken</Request>\r\n  <TransactionToken>".$transToken."</TransactionToken>\r\n</API3G>",
                CURLOPT_HTTPHEADER => [
                    'cache-control: no-cache',
                ],
            ]);

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            if (strlen($err) > 0) {
                echo 'cURL Error #:'.$err;
            } else {
                return $response;
            }
        } catch (\Exception $e) {
            throw new LaravelDpoException($e->getMessage());
        }
    }
}
