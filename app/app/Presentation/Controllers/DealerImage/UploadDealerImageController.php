<?php

declare(strict_types=1);

namespace App\Presentation\Controllers\DealerImage;

use App\Application\UseCases\DealerImage\UploadDealerImageUseCase;
use App\Application\UseCases\DealerImage\UploadDealerImageInputData;
use App\Domain\DealerImage\ValueObjects\DealerId;
use App\Domain\DealerImage\Exceptions\DealerNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use RuntimeException;
use Exception;

class UploadDealerImageController extends Controller
{
    public function __construct(
        private readonly UploadDealerImageUseCase $useCase,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'dealer_id' => 'required|integer|min:1',
                'file'      => 'required|file|image|max:10240',
                'alt_text'  => 'nullable|string|max:255',
            ]);

            $input = new UploadDealerImageInputData(
                dealerId : new DealerId($request->input('dealer_id')),
                file     : $request->file('file'),
                altText  : $request->input('alt_text'),
            );

            $output = $this->useCase->execute($input);

            return response()->json($output->toArray(), 201);

        } catch (InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);

        } catch (RuntimeException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);

        } catch (DealerNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);

        } catch (Exception $e) {
            Log::error('DealerImage upload error:', [
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