"use client";

import { useMemo, useState } from "react";
import { validateProdutividadeFile, ProdutividadeFileUploadResponse } from "@/lib/medicos";


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
    <div className="p-6">
      <div className="card bg-white">
        <div className="card-body p-6">
          <h1 className="text-xl font-semibold">Importar Produtividade Médica</h1>
          <p className="text-sm opacity-70 mt-1">
            Envie um arquivo para validação. O sistema não importa ainda — apenas valida.
          </p>

          <form onSubmit={onSubmit} className="mt-6 space-y-4">
            <label className="block text-sm font-medium mb-2">Arquivo</label>
            <input
              type="file"
              onChange={(e) => setFile(e.target.files?.[0] ?? null)}
              className="block w-full text-sm text-gray-500
                                    file:me-4 file:py-2 file:px-4
                                    file:rounded-md file:border-0
                                    file:text-sm file:font-semibold
                                    file:bg-primary file:text-white
                                    hover:file:bg-primaryemphasis
                                    file:disabled:opacity-50 file:disabled:pointer-events-none
                                    dark:file:bg-primary
                                    dark:hover:file:bg-primary
                                  "
            />

            {fileInfo && (
              <div className="mt-3 text-sm opacity-80">
                <div><span className="font-medium">Nome:</span> {fileInfo.name}</div>
                <div><span className="font-medium">Tamanho:</span> {fileInfo.size}</div>
                <div><span className="font-medium">Tipo:</span> {fileInfo.type}</div>
              </div>
            )}

            {clientError && (
              <div className="rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                {clientError}
              </div>
            )}

            <div className="flex items-center gap-3">
              <button
                type="submit"
                disabled={isSending}
                className="px-4 py-2 rounded-xl bg-[var(--color-primary)] text-white disabled:opacity-60"
              >
                {isSending ? "Validando..." : "Validar arquivo"}
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
  );
}
