import { api } from "@/lib/api";

// ##### Abertos VS Fechados #####
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

  return res.data;
}

// ##### Abertos por Categoria #####
export type OpenByCategoryPoint = {
  key: string;
  label: string;
  total: number;
};

export type OpenByCategoryResponse = {
  month: string;
  total: number;
  changePct: number;
  previousTotal: number;
  categories: OpenByCategoryPoint[];
};

export async function fetchOpenByCategory(month?: number): Promise<OpenByCategoryResponse> {
  const url = `/sensrit/tickets/open-by-category${month ? `?month=${month}` : ""}`;

  const res = await api.get<OpenByCategoryResponse>(url);

  return {
    month: String(res.month ?? ""),
    total: Number(res.total ?? 0),
    changePct: Number(res.changePct ?? 0),
    previousTotal: Number(res.previousTotal ?? 0),
    categories: Array.isArray(res.categories)
      ? res.categories.map((c) => ({
          key: String(c?.key ?? "unknown"),
          label: String(c?.label ?? c?.key ?? "Sem categoria"),
          total: Number(c?.total ?? 0),
        }))
      : [],
  };
}

// ##### Abertos por dia #####
export type OpenByDayPoint = {
  date: string; // YYYY-MM-DD
  opened: number;
};

export type OpenedByDayResponse = {
  month: string;
  total: number;
  changePct: number;
  days: OpenByDayPoint[];
};

export async function fetchOpenedByDay(month: string): Promise<OpenedByDayResponse> {
  return api.get<OpenedByDayResponse>(`/sensrit/tickets/opened-by-day?month=${month}`);
}