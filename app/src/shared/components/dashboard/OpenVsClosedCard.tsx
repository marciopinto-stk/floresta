"use client";

import { useEffect, useMemo, useState } from "react";
import {
  ResponsiveContainer,
  XAxis,
  YAxis,
  Tooltip,
  Legend,
  CartesianGrid,
  BarChart,
  ReferenceLine,
  Bar,
} from "recharts";

import { fetchOpenVsClosed, OpenVsClosedResponse } from "@/lib/sensritDashboard";
import { RangePicker7Days } from "@/shared/layout/Datepicker/RangePicker7Days";

/* Date helpers */
function toYmd(d: Date): string {
  const y   = d.getFullYear();
  const m   = String(d.getMonth() + 1).padStart(2, "0");
  const day = String(d.getDate()).padStart(2, "0");
  return `${y}-${m}-${day}`;
}

function addDays(date: Date, days: number): Date {
  const d = new Date(date);
  d.setDate(d.getDate() + days); 
  return d;
}

function diffDaysInclusive(from: string, to: string): number {
  const a   = new Date(from + "T00:00:00");
  const b   = new Date(to + "T00:00:00");
  const ms  = b.getTime() - a.getTime(); 
  return Math.floor(ms / 86400000) + 1;
}

function last7DaysRangeClient(): { from: string; to: string } {
  const today = new Date();
  const to    = toYmd(today);
  const from  = toYmd(addDays(today, -6)); // 7 dias incluindo hoje
  return { from, to };
}

/* Formatters */
function formatWeekdayPtBR(ymd: string): string {
  const d = new Date(ymd + "T00:00:00");
  return new Intl.DateTimeFormat("pt-BR", { weekday: "short" }).format(d);
}

function formatTooltipLabel(ymd: string): string {
  const d = new Date(ymd + "T00:00:00");
  return new Intl.DateTimeFormat("pt-BR", { dateStyle: "medium" }).format(d);
}

type Props = {
  companyId?: number;
};

type ThemeColors = {
  opened: string;
  closed: string;
  axis: string;
  grid: string;
};

function readCssVar(name: string, fallback: string): string {
  if (typeof window === "undefined") return fallback;
  const v = getComputedStyle(document.documentElement).getPropertyValue(name)?.trim();
  return v || fallback;
}

export default function OpenVsClosedCard({ companyId }: Props) {
  const [mounted, setMounted] = useState(false);
  const [from, setFrom]       = useState<string>("");
  const [to, setTo]           = useState<string>("");
  const [loading, setLoading] = useState<boolean>(false);
  const [error, setError]     = useState<string | null>(null);
  const [payload, setPayload] = useState<OpenVsClosedResponse | null>(null);
  const [theme, setTheme] = useState<ThemeColors>({
    opened: "currentColor",
    closed: "currentColor",
    axis: "currentColor",
    grid: "rgba(0,0,0,0.08)",
  });

  useEffect(() => {
    setMounted(true);
    
    const r = last7DaysRangeClient();
    setFrom(r.from);
    setTo(r.to);

    setTheme({
      opened: readCssVar("--ds-primary", readCssVar("--color-primary", "#2563eb")),
      closed: readCssVar("--color-success", "#16a34a"),
      axis: readCssVar("--color-bodytext", "rgba(0,0,0,0.65)"),
      grid: readCssVar("--color-bordergray", "rgba(0,0,0,0.08)"),
    });
  }, []);

  const rangeDays = useMemo(() => {
    if (!from || !to) return 0;
    return diffDaysInclusive(from, to);
  }, [from, to]);

  const isRangeValid = mounted && rangeDays > 0 && rangeDays <= 7;

  async function load() {
    setLoading(true);
    setError(null);

    try {
      const raw: unknown = await fetchOpenVsClosed({ from, to, companyId });

      // Normaliza: aceita
      // 1) { range, totals, data } (formato completo)
      // 2) [{date, opened, closed}, ...] (array puro)
      let normalized: OpenVsClosedResponse;

      if (Array.isArray(raw)) {
        const totals = raw.reduce(
          (acc, r) => {
            acc.opened += Number(r?.opened ?? 0);
            acc.closed += Number(r?.closed ?? 0);
            return acc;
          },
          { opened: 0, closed: 0 }
        );

        normalized = {
          range: { from, to },
          totals,
          data: raw,
        };
      } else if (raw?.data && Array.isArray(raw.data)) {
        normalized = raw as OpenVsClosedResponse;
      } else if (raw?.data?.data && Array.isArray(raw.data.data)) {
        // caso venha aninhado (axios response mal retornado em algum lugar)
        normalized = raw.data as OpenVsClosedResponse;
      } else {
        normalized = {
          range: { from, to },
          totals: { opened: 0, closed: 0 },
          data: [],
        };
      }

      setPayload(normalized);
    } catch (err: unknown) {
      console.error("OpenVsClosedCard fetch error:", err);
      setError(err?.message ?? "Erro ao carregar dados");
      setPayload(null);
    } finally {
      setLoading(false);
    }
  }


  useEffect(() => {
    if (isRangeValid) load();
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [isRangeValid, from, to, companyId]);

  const safePayload: OpenVsClosedResponse = {
    range: payload?.range ?? { from: from || "", to: to || "" },
    totals: payload?.totals ?? { opened: 0, closed: 0 },
    data: payload?.data ?? [],
  };

  const chartData = useMemo(() => {
    return safePayload.data.map((row) => ({
      date: row.date,
      opened: row.opened,
      closed: -Math.abs(row.closed),
    }));
  }, [safePayload.data]);

  const maxY = useMemo(() => {
    const m = chartData.reduce((acc, d) => Math.max(acc, d.opened, Math.abs(d.closed)), 0);
    return m || 1;
  }, [chartData]);

  function parseYmd(ymd: string): Date {
    return new Date(ymd + "T00:00:00");
  }

  function formatPtBR(from: string, to: string): string {
    const a     = parseYmd(from);
    const b     = parseYmd(to);
    const ftm   = new Intl.DateTimeFormat("pt-BR", { dateStyle: "medium" });

    return `${ftm.format(a)}  → ${ftm.format(b)}`;
  }

  return (
    <div className="card card-sm">
      <div className="p8">
        <div className="sm:flex items-center justify-between mb-6">
          <div>
            <h5 className="card-title">Abertos X Fechados</h5>
            <p className="card-subtitle">
              Visão Geral • {" "}
              {
                
                mounted ? (
                <>
                  <span className="font-medium">{formatPtBR(safePayload.range.from, safePayload.range.to)}</span>
                </>
              ) : (
                <span className="text-black/40">carregando intervalo…</span>
              )}
            </p>
          </div>
          <div className="sm:mt-0 mt-4">
            <RangePicker7Days
              disabled={!mounted}
              value={{ from, to }}
              onChange={(next) => {
                setFrom(next.from);
                setTo(next.to);
              }}
            />
          </div>
        </div>

        <div className="grid grid-cols-12 gap-6 items-start">
          <div className="lg:col-span-8 md:col-span-8 sm:col-span-12 col-span-12">
            <div className="w-full">
              {chartData.length === 0 ? (
                <div className="h-[325px] flex items-center justify-center text-sm text-black/60">
                  Sem dados para o período selecionado.
                </div>
              ) : (
                
                <div className="h-[325px]">
                  <ResponsiveContainer>
                    <BarChart
                      data={chartData}
                      responsive
                      stackOffset="sign"
                      margin={{ top: 16, right: 0, left: 0, bottom: 8 }}
                    >
                      <CartesianGrid strokeDasharray="3 3" />
                      <XAxis dataKey="date" tick={{ fill: theme.axis }} tickFormatter={(v) => formatWeekdayPtBR(String(v))} />
                      <YAxis domain={[-maxY, maxY]} allowDecimals={false} tick={{ fill: theme.axis }} tickFormatter={(v) => Math.abs(v)} />
                      <ReferenceLine y={0} stroke={theme.axis} />
                      <Tooltip formatter={(value: number) => Math.abs(value)} labelFormatter={(v) => formatTooltipLabel(String(v))} />
                      <Legend />

                      <Bar
                        dataKey="opened"
                        name="Abertos"
                        fill={theme.opened}
                        radius={[100, 100, 0, 0]}
                        stackId="stack"
                        barSize={20}
                      />

                      <Bar
                        dataKey="closed"
                        name="Fechados"
                        fill={theme.closed}
                        radius={[100, 100, 0, 0]}
                        stackId="stack"
                      />
                    </BarChart>
                  </ResponsiveContainer>
                </div>
              )}
            </div>
          </div>
          <div className="lg:col-span-4 md:col-span-4 sm:col-span-12 col-span-12">
            <div className="flex items-baseline gap-3 pt-9">
              <i className="h-2 w-2 rounded-full bg-primary"></i>
              <div>
                <p>Quantidade de tickets abertos</p>
                <h6 className="text-lg">{safePayload.totals.opened}</h6>
              </div>
            </div>

            <div className="flex items-baseline gap-3 pt-5">
              <i className="h-2 w-2  rounded-full bg-secondary"></i>
              <div>
                <p>Quantidade de tickets encerrados</p>
                <h6 className="text-lg">{safePayload.totals.closed}</h6>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
