<?php namespace App\Services\payments;


class PaymentFactory
{
    public static function create($class)
    {
		$class = Helper::getPaymentClassName($class);

        if (!class_exists($class)) {
			throw new \Exception("Class '$class' not found");
        }

		$payment_service = new $class();

		return $payment_service;
    }
}


class Helper
{

	public static function getPaymentClassName($shortName)
	{
		if (0 === strpos($shortName, '\\')) {
			return $shortName;
		}

		$shortName = ucfirst(strtolower($shortName));

		return '\\App\\Services\\payments\\'.$shortName.'Payment';
	}
}
