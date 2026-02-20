<?php
namespace App\Domain\CarList\Entities;

final class CarDetail
{
    public function __construct(
        public readonly int                 $id,
        public readonly ?int                $carId,
        public readonly ?DateTimeImmutable  $firstRegistrationDate,
        public readonly ?DateTimeImmutable  $inspectionExpireDate,
        public readonly ?string             $inspectionStatus,
        public readonly ?string             $driveSystem,
        public readonly ?int                $displacement,
        public readonly string              $steeringWheel,
        public readonly ?int                $numberOfDoors,
        public readonly string              $slideDoor,
        public readonly ?int                $ridingCapacity,
        public readonly ?int                $loanAvailable,
        public readonly ?string             $description,
        public readonly ?string             $freeText,
        public readonly ?string             $seoTitle,
        public readonly ?string             $seoDescription,
    ) {}

    public function getId(): int
    {
        return $this->id;
    }

    public function getCarId(): ?int
    {
        return $this->carId;
    }

    public function getFirstRegistrationDate(): ?DateTimeImmutable
    {
        return $this->firstRegistrationDate;
    }

    public function getInspectionExpireDate(): ?DateTimeImmutable
    {
        return $this->inspectionExpireDate;
    }

    public function getInspectionStatus(): ?string
    {
        return $this->inspectionStatus;
    }

    public function getDriveSystem(): ?string
    {
        return $this->driveSystem;
    }

    public function getDisplacement(): ?int
    {
        return $this->displacement;
    }

    public function getSteeringWheel(): string
    {
        return $this->steeringWheel;
    }

    public function getNumberOfDoors(): ?int
    {
        return $this->numberOfDoors;
    }

    public function getSlideDoor(): string
    {
        return $this->slideDoor;
    }

    public function getRidingCapacity(): ?int
    {
        return $this->ridingCapacity;
    }

    public function getLoanAvailable(): ?int
    {
        return $this->loanAvailable;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getFreeText(): ?string
    {
        return $this->freeText;
    }

    public function getSeoTitle(): ?string
    {
        return $this->seoTitle;
    }

    public function getSeoDescription(): ?string
    {
        return $this->seoDescription;
    }

    public function toArray(): array
    {
        return [
            'carId'                 => $this->carId,
            'firstRegistrationDate' => $this->firstRegistrationDate?->format('Y-m-d'),
            'inspectionExpireDate'  => $this->inspectionExpireDate?->format('Y-m-d'),
            'inspectionStatus'      => $this->inspectionStatus ?? '',
            'steeringWheel'         => $this->steeringWheel,
            'numberOfDoors'         => $this->numberOfDoors,
            'slideDoor'             => $this->slideDoor,
            'ridingCapacity'        => $this->ridingCapacity,
            'loanAvailable'         => $this->loanAvailable,
            'freeText'              => $this->freeText ?? '',
            'seoTitle'              => $this->seoTitle ?? '',
            'seoDescription'        => $this->seoDescription,
        ];
    }
}