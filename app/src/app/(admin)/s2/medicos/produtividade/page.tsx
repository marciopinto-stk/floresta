"use client";

import { useState } from "react";
import { validateProdutividadeFile, ProdutividadeFileUploadResponse, importProdutividade} from "@/lib/medicos";
import PageHeader from "@/shared/layout/Headers/PageHeader";
import Autocomplete from "@/shared/layout/Forms/Autocomplete";
import { OptionItem } from "@/shared/layout/Forms/types";
import { searchProdutos } from "@/lib/options/produtos";
import { Icon } from "@iconify/react";
import { searchMedicos } from "@/lib/options/medicos";
import ImportTraceAccordion from "@/shared/components/medicos/ImportTraceAccordion";



function formatBytes(bytes: number) {
  if (bytes === 0) return "0 B";
  const k = 1024;
  const sizes = ["B", "KB", "MB", "GB"];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  const value = bytes / Math.pow(k, i);
  return `${value.toFixed(i === 0 ? 0 : 1)} ${sizes[i]}`;
}

function getPreviousMonthYYYYMM(date = new Date()) {
  const d = new Date(date.getFullYear(), date.getMonth() - 1, 1);
  const yyyy = d.getFullYear();
  const mm = String(d.getMonth() + 1).padStart(2, "0");
  return `${yyyy}-${mm}`;
}

export default function MedicosImportarPage() {
  const [file, setFile] = useState<File | null>(null);
  const [referenceMonth, setReferenceMonth] = useState<string>(getPreviousMonthYYYYMM());
  const [isSending, setIsSending] = useState(false);
  const [result, setResult] = useState<ProdutividadeFileUploadResponse | null>(null);
  const [clientError, setClientError] = useState<string | null>(null);

  const[selectedMedico, setSelectedMedico] = useState<OptionItem | null>(null);
  const[selectedProduto, setSelectedProduto] = useState<OptionItem | null>(null);

  const [exceptions, setExceptions] = useState<
    { id_medico: number; id_produto: number, medico: string, produto: string }[]
  >([]);

  function addException() {
    if (!selectedMedico || !selectedProduto) {
      setClientError("Selecione um médico e um produto.");
      return;
    }

    const exists = exceptions.some(
      (e) =>
        e.id_medico === selectedMedico.id &&
      e.id_produto === selectedProduto.id
    );

    if (exists) {
      setClientError("Essa exceção já foi adicionada.");
      return;
    }

    setExceptions((prev) => [
      ...prev,
      {
        id_medico: selectedMedico.id,
        id_produto: selectedProduto.id,
        medico: selectedMedico.label,
        produto: selectedProduto.label
      }
    ]);

    setSelectedMedico(null);
    setSelectedProduto(null);
    setClientError(null);
  }

  async function onSubmit(e: React.FormEvent) {
    e.preventDefault();
    setClientError(null);
    setResult(null);
  
    if (!file) {
      setClientError("Selecione um arquivo antes de enviar.");
      return;
    }

    if (!referenceMonth || !/^\d{4}-\d{2}$/.test(referenceMonth)) {
      return setClientError("Informe a competência no formato YYYY-MM.");
    }

    setIsSending(true);

    try {
      const payloadExceptions = exceptions.map((ex) => ({
        id_medico: ex.id_medico,
        id_produto: ex.id_produto
      }));

      const res = await importProdutividade({
        file,
        competencia: referenceMonth,
        exceptions: payloadExceptions
      });

      setResult(res);
    } catch (err: unknown) {
      setResult({
        ok: false,
        message: err?.message ?? "Falha ao importar.",
        errors: err?.data?.errors,
        data: err?.data,
      });
    } finally {
      setIsSending(false);
    }
  }

  return (
    <div className="w-full items-stretch">
      <PageHeader 
        title="Importação de Produtividade Médica"
        subtitle="Importe o arquivo de produtividade médica (Laudos Proradis)."
      />
      <div className="grid grid-cols-12 gap-6 items-stretch">
        <div className="lg:col-span-5 col-span-12">
          <div className="card h-full">
            <div className="p-8">
              <h5 className="card-title">Formulário de envio</h5>
              <p className="card-subtitle">Envie aqui o arquivo .csv e adicione as exceções a serem excluídas da importação.</p>
              <form onSubmit={onSubmit} className="mt-6 space-y-4">
                <div className="grid grid-cols-1 md:grid-cols-12 gap-6 pb-4">
                  <div className="md:col-span-8">
                    <label className="block text-sm font-medium mb-2">Arquivo</label>
                    <input
                      type="file"
                      onChange={(e) => setFile(e.target.files?.[0] ?? null)}
                      className="form-control-file"
                    />

                    {clientError && (
                      <div className="bg-red-50 p-3 text-sm text-red-700">
                        {clientError}
                      </div>
                    )}
                  </div>

                  <div className="md:col-span-4">
                    <label className="block text-sm font-medium mb-2">Mês de referência</label>
                    <input
                      type="month"
                      required
                      defaultValue={referenceMonth}
                      max={getPreviousMonthYYYYMM()}
                      className="form-control"
                    />
                  </div>
                </div>

                <div>
                  <div>
                    <div className="text-sm font-medium">Exceções (médico-produto)</div>
                    <div className="text-xs opacity-70">
                      Pares que devem ser desconsiderados durante a importação.
                    </div>
                  </div>

                  <div className="grid grid-cols-1 gap-2 sm:grid-cols-[1fr_1fr_auto] items-end">
                    <Autocomplete
                      label = "Médico"
                      value = {selectedMedico}
                      onChange = {setSelectedMedico}
                      search={searchMedicos}
                    />

                    <Autocomplete
                      label = "Produto"
                      value = {selectedProduto}
                      onChange = {setSelectedProduto}
                      search={searchProdutos}
                    />
                    
                    <button
                      type="button"
                      className="btn btn-primary w-fit h-fit cursor-pointer sm:justify-self-end bg-cyan-600 hover:bg-cyan-800 mb-1"
                      onClick={addException}
                    >
                      +
                    </button>
                  </div>
                </div>

                {exceptions.length > 0 && (
                  <div className="mt-4 divide-y divide-slate-300 bg-slate-100">
                    {exceptions.map((ex, index) => (
                      <div
                        key={`${ex.id_medico}-${ex.id_produto}`}
                        className="flex items-center justify-between p-3 text-sm"
                      >
                        <div>
                          <span className="font-medium">{ex.medico}</span>
                          <span className="mx-2 text-gray-400">×</span>
                          <span>{ex.produto}</span>
                        </div>

                        <button
                          type="button"
                          onClick={() =>
                            setExceptions((prev) =>
                              prev.filter((_, i) => i !== index)
                            )
                          }
                          className="text-error hover:text-red-800 text-xs cursor-pointer"
                        >
                          <Icon icon="tabler:trash-x" className="text-2xl" />
                        </button>
                      </div>
                    ))}
                  </div>
                )}

                <div className="flex items-center gap-3">
                  <button
                    type="submit"
                    disabled={isSending}
                    className="px-4 py-2 rounded-xl bg-[var(--color-primary)] text-white disabled:opacity-60"
                  >
                    {isSending ? "Enviando..." : "Importar"}
                  </button>

                  <button
                    type="button"
                    onClick={() => {
                      setFile(null);
                      setResult(null);
                      setClientError(null);
                    }}
                    disabled={isSending}
                    className="px-4 py-2 rounded-xl border disabled:opacity-60"
                  >
                    Limpar
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>

        <div className="lg:col-span-7 col-span-12">
          <div className="card h-full">
            <div className="p-8">
              <h5 className="card-title">Relatório de Importação</h5>
              {result?.data?.logs && (
                <div className="mt-6">
                  <ImportTraceAccordion logs={result.data.logs} />
                </div>
              )}
            </div>
          </div>
        </div>
      </div>
      
    </div>
    
  );
}
