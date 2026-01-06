<?php

namespace App\Modules\Financeiro\Interface\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Financeiro\Application\DTO\BuscarItensSemNotaPorRecepcaoInput;
use App\Modules\Financeiro\Application\DTO\ProcessarNotasPorRecepcaoInput;
use Illuminate\Http\Request;
use App\Modules\Financeiro\Application\Services\InvoiceService;
use App\Modules\Financeiro\Interface\Http\Resources\NotaRecepcaoResource;
use Modules\Financeiro\Interface\Http\Requests\ProcessarNotasRequest;

class InvoiceController extends Controller
{
    public function __construct(private readonly InvoiceService $service)
    {}

    public function buscarItensSemNotaPorRecepcao(Request $request)
    {
        $data   = $request->validate(['id_recepcao' => ['required', 'integer', 'min:1']]);
        $input  = BuscarItensSemNotaPorRecepcaoInput::fromArray($data);
        $notas  = $this->service->buscarItensSemNotaPorRecepcao($input);

        return NotaRecepcaoResource::collection($notas);
    }

    public function processarNotasPorRecepcao(Request $request)
    {
        $data   = $request->validate(['id_recepcao' => ['required', 'integer', 'min:1']]);
        $input  = ProcessarNotasPorRecepcaoInput::fromArray($data);

        return response()->json(
            $this->service->processarNotasPorRecepcao($input)
        );
    }

    public function emitirNotasPorRecepcao(Request $request)
    {
        $data   = $request->validate(['id_recepcao' => ['required', 'integer', 'min:1']]);
        $input  = ProcessarNotasPorRecepcaoInput::fromArray($data);

        return response()->json(
            $this->service->emitirNotasPorRecepcao($input)
        );
    }

}
