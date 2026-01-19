import { api } from "@/lib/api";

export type OpenVsClosedPoint = {
  date: string; // YYYY-MM-DD
  opened: number;
  closed: number;
};

export type OpenVsClosedResponse = {
  range: { from: string; to: string };
  totals: { opened: number; closed: number };
  data: OpenVsClosedPoint[];
};

export async function fetchOpenVsClosed(params?: {
  from?: string;
  to?: string;
  companyId?: number;
}): Promise<OpenVsClosedResponse> {
  const search = new URLSearchParams();

  if (params?.from) search.set("from", params.from);
  if (params?.to) search.set("to", params.to);
  if (typeof params?.companyId === "number") search.set("companyId", String(params.companyId));

  const qs = search.toString();
  const url = `/sensrit/tickets/open-vs-closed${qs ? `?${qs}` : ""}`;

  const res = await api.get<OpenVsClosedResponse>(url);

  // ✅ Aqui é o ponto crítico: retornar o objeto inteiro
  return res.data;
}
