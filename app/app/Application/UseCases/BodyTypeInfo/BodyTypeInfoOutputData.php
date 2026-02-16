<?php
namespace App\Application\UseCases\BodyTypeInfo;

use App\Domain\BodyTypeInfo\Entities\BodyType;

class BodyTypeInfoOutputData
{
    public function __construct(
        private readonly BodyType $bodyType
    ) {}

    public function toArray(): array
    {
        return [
            'success' => true,
            'data' => [
                'BodyTypeInfo' => $this->bodyType->toArray(),
            ],
        ];
    }

    public function getBodyType(): BodyType
    {
        return $this->bodyType;
    }
}