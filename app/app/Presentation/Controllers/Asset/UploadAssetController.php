<?php

declare(strict_types=1);

namespace App\Presentation\Controllers\Asset;

use App\Application\UseCases\Asset\UploadAssetUseCase;
use App\Application\UseCases\Asset\UploadAssetInputData;
use App\Domain\Asset\ValueObjects\AssetType;
use App\Domain\Asset\Exceptions\AssetRecordNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Exception;

class UploadAssetController extends Controller
{
    public function __construct(
        private readonly UploadAssetUseCase $useCase,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'type'      => 'required|string',
                'record_id' => 'required|integer|min:1',
                'file'      => 'required|file|image|max:10240',
            ]);

            $input = new UploadAssetInputData(
                type     : new AssetType($request->input('type')),
                recordId : (int) $request->input('record_id'),
                file     : $request->file('file'),
            );

            $output = $this->useCase->execute($input);

            return response()->json($output->toArray(), 201);

        } catch (InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);

        } catch (AssetRecordNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);

        } catch (Exception $e) {
            Log::error('Asset upload error:', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'サーバーエラーが発生しました。',
            ], 500);
        }
    }
}