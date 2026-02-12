import { api } from "@/lib/api";


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