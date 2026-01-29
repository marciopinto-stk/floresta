<?php

namespace App\Modules\Sensrit\Interface\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Sensrit\Application\UseCases\Dashboard\TicketsOpenByCategoryUseCase;
use App\Modules\Sensrit\Application\UseCases\Dashboard\TicketsOpenVsClosedUseCase;
use App\Modules\Sensrit\Interface\Http\Requests\Dashboard\OpenByCategoryRequest;
use App\Modules\Sensrit\Interface\Http\Requests\Dashboard\OpenVsClosedRequest;
use Illuminate\Http\JsonResponse;

class TicketsDashboardController extends Controller
{
    public function openVsClosed(
        OpenVsClosedRequest $request,
        TicketsOpenVsClosedUseCase $openVsCloseUseCase
    ): JsonResponse
    {
        $from = $request->string('from')->toString();
        $to   = $request->string('to')->toString();

        $companyId = $request->filled('companyId')
            ? (int) $request->input('companyId')
            : null;

        return response()->json(
            $openVsCloseUseCase->execute($from, $to, $companyId)
        );
    }

    public function openByCategory(
        OpenByCategoryRequest $request,
        TicketsOpenByCategoryUseCase $openByCategoryUseCase
    ): JsonResponse
    {
        $month = $request->integer('month');
        $result = $openByCategoryUseCase->execute($month);

        return response()->json($result);
    }

    public function openedByDayInMonth(
        OpenByDayInMonthRequest $request,
        TicketsOpenedByDayInMonthUseCase $openedByDayInMonthUseCase
    ): JsonResponse
    {
        $month  = $request->integer('month');
        $result = $openedByDayInMonthUseCase->execute($month);
        return response()->json($result);
    }
}
