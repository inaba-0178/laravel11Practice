<?php

declare(strict_types=1);

namespace App\Application\UseCases\StaffImage;

use App\Domain\StaffImage\Repositories\StaffImageUploadRepositoryInterface;
use App\Domain\StaffImage\Exceptions\StaffNotFoundException;

final class UploadStaffImageUseCase
{
    public function __construct(
        private readonly StaffImageUploadRepositoryInterface $repository,
    ) {}

    /**
     * @throws StaffNotFoundException
     */
    public function execute(UploadStaffImageInputData $input): UploadStaffImageOutputData
    {
        $staff = $this->repository->findById($input->staffId);

        if ($staff === null) {
            throw new StaffNotFoundException($input->staffId->getValue());
        }

        // staffs/{dealerId}/{userId}/　または　staffs/{dealerId}/no_account/
        $userId    = $staff->userId ?? 'no_account';
        $directory = "staffs/{$staff->dealerId}/{$userId}";
        $path      = $input->file->store($directory, 's3');

        $staffImage = $this->repository->save($input->staffId, $path);

        return new UploadStaffImageOutputData($staffImage);
    }
}