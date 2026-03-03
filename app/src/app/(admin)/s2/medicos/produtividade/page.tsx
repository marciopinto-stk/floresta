"use client";

import { useMemo, useState } from "react";
import { validateProdutividadeFile, ProdutividadeFileUploadResponse, searchMedicos } from "@/lib/medicos";
import PageHeader from "@/shared/layout/Headers/PageHeader";
import Autocomplete from "@/shared/layout/Forms/Autocomplete";
import { OptionItem } from "@/shared/layout/Forms/types";


function formatBytes(bytes: number) {
  if (bytes === 0) return "0 B";
  const k = 1024;
  const sizes = ["B", "KB", "MB", "GB"];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  const value = bytes / Math.pow(k, i);
  return `${value.toFixed(i === 0 ? 0 : 1)} ${sizes[i]}`;
}

export default function MedicosImportarPage() {
  const [file, setFile] = useState<File | null>(null);
  const [isSending, setIsSending] = useState(false);
  const [result, setResult] = useState<ProdutividadeFileUploadResponse | null>(null);
  const [clientError, setClientError] = useState<string | null>(null);

  const[selectedMedico, setSelectedMedico] = useState<OptionItem | null>(null);

  const fileInfo = useMemo(() => {
    if (!file) return null;
    return { name: file.name, size: formatBytes(file.size), type: file.type || "-" };
  }, [file]);

  async function onSubmit(e: React.FormEvent) {
    e.preventDefault();
    setClientError(null);
    setResult(null);
  
    if (!file) {
      setClientError("Selecione um arquivo antes de enviar.");
      return;
    }

    setIsSending(true);

    try {
      const data = await validateProdutividadeFile(file);
      setResult(data);
    } catch (err: unknown) {
      const message =
        err?.response?.data?.message ||
        err?.message ||
        "Não foi possível validar o arquivo. Tente novamente.";

      setResult({
        ok: false,
        message,
        errors: err?.response?.data?.errors,
        data: err?.response?.data,
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
                      <div className="rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                        {clientError}
                      </div>
                    )}
                  </div>

                  <div className="md:col-span-4">
                    <label className="block text-sm font-medium mb-2">Mês de referência</label>
                    <input
                      type="month"
                      
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

                  <div className="grid grid-cols-1 gap-2 sm:grid-cols-[1fr_1fr_auto]">
                    <Autocomplete
                      label = "Médico"
                      value = {selectedMedico}
                      onChange = {setSelectedMedico}
                      search={searchMedicos}
                    />

                    <select className="form-control">
                      <option value="">Produto</option>
                    </select>
                    
                    <button
                      type="button"
                      className="btn btn-primary w-fit cursor-pointer sm:justify-self-end bg-cyan-600 hover:bg-cyan-800"
                      
                    >
                      +
                    </button>
                    
                  </div>
                </div>

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
            </div>
          </div>
        </div>
      </div>
      
    </div>
    
  );
}
