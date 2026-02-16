<?php

namespace App\Domain\ManufacturerList\ValueObjects;

use InvalidArgumentException;

final class ManufacturerIds
{
    /** @var int[] */
    private readonly array $value;

    /**
     * @param array<int|string> $value
     */
    public function __construct(array $value)
    {
        if ($value === []) {
            throw new InvalidArgumentException('ManufacturerIdsは必須です。');
        }
        
        $normalized = [];
        foreach ($value as $id) {
            // 文字列の数値も受け入れる
            if (is_int($id)) {
                $intId = $id;
            } elseif (is_string($id) && ctype_digit($id)) {
                $intId = (int)$id;
            } else {
                throw new InvalidArgumentException(
                    'ManufacturerIdsは正の整数IDの配列である必要があります。'
                );
            }
            
            if ($intId <= 0) {
                throw new InvalidArgumentException(
                    'ManufacturerIdsは正の整数IDの配列である必要があります。'
                );
            }
            
            $normalized[] = $intId;
        }
        
        $this->value = $normalized;
    }
    
    /** @return int[] */
    public function getValue(): array
    {
        return $this->value;
    }
}