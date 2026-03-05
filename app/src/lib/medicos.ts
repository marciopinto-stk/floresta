import { api } from "@/lib/api";

export type ProdutividadeImportResponse = {
  ok: boolean;
  message?: string;
  errors?: Record<string, string[]>;
  data?: unknown;
};

export type ExceptionPair = {
  id_medico: number;
  id_produto: number;
}

export async function importProdutividade(params: {
  file: File;
  competencia: string;
  exceptions: ExceptionPair[];
}) {
  const form = new FormData();
  form.append("file", params.file);
  form.append("competencia", params.competencia);
  form.append("exceptions", JSON.stringify(params.exceptions));

  return api.postForm<ProdutividadeImportResponse>("/medicos/produtividade/importar", form);
}