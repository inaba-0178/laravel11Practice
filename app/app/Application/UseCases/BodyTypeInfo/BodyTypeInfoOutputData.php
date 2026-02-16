<?php

namespace App\Application\UseCases\BodyTypeInfo;

use App\Domain\BodyTypeInfo\Entities\BodyType;

class BodyTypeInfoOutputData
{
    private BodyType $bodyType;

    /**
     * @param BodyType $bodyType
     */
    public function __construct(BodyType $bodyType)
    {
        $this->bodyType = $bodyType;
    }

    public function toArray(): array
    {
        $response = [
            'success' => true,
            'data' => [
                'BodyTypeInfo' => $this->bodyType->toArray(),
            ],
        ];

        return $response;
    }

    public function getBodyType(): BodyType
    {
        return $this->bodyType;
    }
}
