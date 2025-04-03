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
        $errors = $this->validator->validate($product);

        return $errors->count() > 0 ? $this->formatValidationErrors($errors) : [];
    }

    private function formatValidationErrors(ConstraintViolationListInterface $errors): array
    {
        $errorMessages = [];
        foreach ($errors as $error) {
            $errorMessages[] = $error->getPropertyPath() . ': ' . $error->getMessage();
        }
        return $errorMessages;
    }
}
