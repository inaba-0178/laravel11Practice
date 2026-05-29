<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories\Asset;

use App\Domain\Asset\Entities\Asset;
use App\Domain\Asset\Repositories\AssetUploadRepositoryInterface;
use App\Domain\Asset\ValueObjects\AssetType;
use App\Infrastructure\Eloquent\User\StkDealerStaff;
use App\Infrastructure\Eloquent\User\StkDealerContent;
use App\Infrastructure\Eloquent\Opr\OprMainView;

class EloquentAssetUploadRepository implements AssetUploadRepositoryInterface
{
    public function findRecord(AssetType $type, int $recordId): ?object
    {
        return match(true) {
            $type->isStaff()   => StkDealerStaff::whereNull('deleted_at')->find($recordId),
            $type->isContent() => StkDealerContent::whereNull('deleted_at')->find($recordId),
            $type->isOprMainView() => OprMainView::whereNull('deleted_at')->find($recordId),
            default            => null,
        };
    }

    public function save(AssetType $type, int $recordId, string $storedPath): Asset
    {
        $record = $this->findRecord($type, $recordId);
        $record->update(['image_path' => $storedPath]);

        return new Asset(
            id        : $record->id,
            type      : $type->getValue(),
            imagePath : $storedPath,
        );
    }
}