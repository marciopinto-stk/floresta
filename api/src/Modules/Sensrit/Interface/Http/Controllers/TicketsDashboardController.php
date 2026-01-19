<?php

namespace App\Modules\Sensrit\Interface\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Sensrit\Application\UseCases\Dashboard\TicketsOpenVsClosedUseCase;
use App\Modules\Sensrit\Interface\Http\Requests\Dashboard\OpenVsClosedRequest;
use Illuminate\Http\JsonResponse;

class TicketsDashboardController extends Controller
{
    public function openVsClosed(
        OpenVsClosedRequest $request,
        TicketsOpenVsClosedUseCase $useCase,
    ): JsonResponse
    {
        $from = $request->string('from')->toString();
        $to   = $request->string('to')->toString();

        $companyId = $request->filled('companyId')
            ? (int) $request->input('companyId')
            : null;

        return response()->json(
            $useCase->execute($from, $to, $companyId)
        );
    }
}
