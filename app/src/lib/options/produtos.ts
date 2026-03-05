import { OptionsResponse } from "@/shared/layout/Forms/types";
import { api } from "../api";

type ApiResponse = {
  data: { value: number; label: string }[];
  pagination: { page: number; limit: number; total: number; hasNext: boolean };
};

export async function searchProdutos(params: {
  q: string;
  page: number;
  limit: number;
}): Promise<OptionsResponse> {
  const raw = await api.getWithQuery<ApiResponse>("/produtos/options", {
    q: params.q,
    page: params.page,
    limit: params.limit,
  });

  return {
    options: raw.data.map((it) => ({ id: it.value, label: it.label })),
    pagination: raw.pagination,
  };
}