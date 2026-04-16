<?php

declare(strict_types=1);

namespace App\Application\UseCases\Asset;

use App\Domain\Asset\Repositories\AssetUploadRepositoryInterface;
use App\Domain\Asset\ValueObjects\AssetType;
use App\Domain\Asset\Exceptions\AssetRecordNotFoundException;

final class UploadAssetUseCase
{
    public function __construct(
        private readonly AssetUploadRepositoryInterface $repository,
    ) {}

    /**
     * @throws AssetRecordNotFoundException
     */
    public function execute(UploadAssetInputData $input): UploadAssetOutputData
    {
        $record = $this->repository->findRecord($input->type, $input->recordId);

        if ($record === null) {
            throw new AssetRecordNotFoundException(
                $input->type->getValue(),
                $input->recordId,
            );
        }

        $path = $this->resolvePath($input->type, $record, $input->recordId);
        $storedPath = $input->file->store($path, 's3');

        $asset = $this->repository->save($input->type, $input->recordId, $storedPath);

        return new UploadAssetOutputData($asset);
    }

    private function resolvePath(AssetType $type, object $record, int $recordId): string
    {
        return match(true) {
            $type->isStaff()   => 'staffs/' . $record->dealer_id . '/' . ($record->user_id ?? 'no_account'),
            $type->isContent() => 'contents/' . $record->dealer_id,
            $type->isMember()  => 'members/' . $recordId,
            default            => 'assets/' . $recordId,
        };
    }
}