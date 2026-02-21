<?php
namespace App\Domain\AreaCarList\Entities;

final class Area
{
    private readonly int    $id;
    private readonly string $name;
    private readonly int    $sortOrder;

    public function __construct(
        int     $id,
        string  $name,
        int     $sortOrder,
    ){
        $this->id           = $id;
        $this->name         = $name;
        $this->sortOrder    = $sortOrder;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSortOrder(): int
    {
        return $this->sortOrder;
    }

    public function toArray(): array
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'sort_order'    => $this->sortOrder,
        ];
    }
}