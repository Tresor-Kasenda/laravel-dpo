<?php

namespace Scott\LaravelDpo\DpoConnector;

use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Scott\LaravelDpo\LaravelDpoException;
use SimpleXMLElement;

class PaymentConnector
{
    public function __construct(
        protected Commander $commander
    ) {
    }

    /**
     * @throws LaravelDpoException
     * @throws \Exception
     */
    public function paymentUri(array $data)
    {
        if ($data['success'] === true) {
            $tokenVerification = $this->commander->verificationToken([
                'companyToken' => $this->commander->connector->company_token,
                'transToken' => $data['transToken'],
            ]);

            if (! empty($tokenVerification) && $tokenVerification !== '') {
                $verified = new SimpleXMLElement($tokenVerification);

                if ($verified->Result->_toString() === '900') {
                    return $this->commander->getGateway().$data['transToken'];
                }
            } else {
                throw new LaravelDpoException(
                    'Something went wrong with the token verification'.$data['resultExplanation']
                );
            }
        }
    }

    /**
     * @throws LaravelDpoException
     * @throws \Exception
     */
    public function processPayment(array $data): Application|Redirector|RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        $paymentUri = $this->paymentUri(
            $this->commander->createToken($data)
        );

        return redirect($paymentUri);
    }
}
