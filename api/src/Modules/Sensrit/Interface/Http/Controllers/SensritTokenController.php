<?php

namespace App\Modules\Sensrit\Interface\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Modules\Sensrit\Application\UseCases\UpdateSensritToken\UpdateSensritTokenInput;
use App\Modules\Sensrit\Application\UseCases\UpdateSensritToken\UpdateSensritTokenUseCase;
use App\Modules\Sensrit\Application\UseCases\ValidateSensritToken\ValidateSensritTokenUseCase;

class SensritTokenController extends Controller
{
    public function update(Request $request, UpdateSensritTokenUseCase $useCase)
    {
        $data = $request->validate([
            'token' => ['required', 'string', 'min:10'],
        ]);

        $useCase->execute(new UpdateSensritTokenInput($data['token']));

        return response()->json([
            'ok'        => true,
            'message'   => 'Token atualizado com sucesso.',
        ]);
    }

    public function validateToken(ValidateSensritTokenUseCase $useCase)
    {
        $out = $useCase->execute();

        return response()->json([
            'ok' => true,
            'hasToken'      => $out->hasToken,
            'isValid'       => $out->isValid,
            'statusCode'    => $out->statusCode,
            'message'       => $out->message,
        ]);
    }
}
