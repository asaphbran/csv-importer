<?php

namespace App\Validator;

use App\Entity\Product;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ProductValidator
{
    public function __construct(private ValidatorInterface $validator) {}

    public function validate(Product $product): array
    {
        // Validates data taking into consideration the characteristics given in the annotations for each property
        $errors = $this->validator->validate($product);

        return $errors->count() > 0 ? $this->formatValidationErrors($errors) : [];
    }

    private function formatValidationErrors(ConstraintViolationListInterface $errors): array
    {
        $errorMessages = [];

        // Giving format to the errors by returning with a speciffic strcuture to be readable
        foreach ($errors as $error) {
            $errorMessages[] = $error->getPropertyPath() . ': ' . $error->getMessage();
        }

        return $errorMessages;
    }
}
