import { api } from "@/lib/api";
import type { OptionsResponse } from "@/shared/layout/Forms/types";

type ApiResponse = {
  data: { value: number; label: string }[];
  pagination: { page: number; limit: number; total: number; hasNext: boolean };
};

export async function searchMedicos(params: { q: string; page: number; limit: number }): Promise<OptionsResponse> {
  const raw = await api.getWithQuery<ApiResponse>("/medicos/options", params);

  return {
    options: raw.data.map((it) => ({ id: it.value, label: it.label })),
    pagination: raw.pagination,
  };
}

export type ProdutividadeFileUploadResponse = {
  ok: boolean;
  message?: string;
  errors?: Record<string, string[]>;
  // se o backend devolver detalhes (ex: linhas inválidas), pode vir aqui também:
  data?: unknown;
};

export async function validateProdutividadeFile(file: File) {
  const form = new FormData();
  form.append("file", file);

  console.log("uploading:", {
    name: file.name,
    size: file.size,
    type: file.type
    });

    for (const [k, v] of form.entries()) console.log(k, v);

  return api.postForm<unknown>(
    "/medicos/produtividade/validar",
    form
  );
}