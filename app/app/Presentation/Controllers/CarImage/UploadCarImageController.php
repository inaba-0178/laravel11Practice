<?php

declare(strict_types=1);

namespace App\Presentation\Controllers\CarImage;

use App\Application\UseCases\CarImage\UploadCarImageUseCase;
use App\Application\UseCases\CarImage\UploadCarImageInputData;
use App\Domain\CarImage\ValueObjects\CarId;
use App\Domain\CarImage\ValueObjects\ImageType;
use App\Domain\CarImage\Exceptions\CarNotFoundException;
use App\Domain\Common\Constants\FileConstants;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Exception;

class UploadCarImageController extends Controller
{
    public function __construct(
        private readonly UploadCarImageUseCase $useCase,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'car_id'     => 'required|integer|min:1',
                'image_type' => 'required|string',
                'file'       => 'required|file|mimes:' . FileConstants::ALLOWED_IMAGE_MIMES . '|max:51200',
            ]);

            $input = new UploadCarImageInputData(
                carId     : new CarId($request->input('car_id')),
                imageType : new ImageType($request->input('image_type')),
                file      : $request->file('file'),
            );

            $output = $this->useCase->execute($input);

            return response()->json($output->toArray(), 201);

        } catch (InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);

        } catch (CarNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);

        } catch (Exception $e) {
            Log::error('CarImage upload error:', [
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