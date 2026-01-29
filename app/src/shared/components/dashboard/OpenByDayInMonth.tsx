"use client";

import { useEffect, useMemo, useState } from "react";
import {
  ResponsiveContainer,
  AreaChart,
  Area,
  Tooltip,
} from "recharts";

import { fetchOpenedByDay, OpenedByDayResponse } from "@/lib/sensritDashboard";

function formatMonthLabel(ym: string) {
  // "2026-01" -> "Jan/2026"
  const [y, m] = ym.split("-").map(Number);
  const d = new Date(y, m - 1, 1);
  return d.toLocaleDateString("pt-BR", { month: "short", year: "numeric" });
}

function toYyyyMm(d: Date): string {
  const y = d.getFullYear();
  const m = String(d.getMonth() + 1).padStart(2, "0");
  return `${y}-${m}`;
}

function pctLabel(pct: number) {
  const sign = pct > 0 ? "+" : "";
  return `${sign}${pct.toFixed(0)}%`;
}

export default function OpenByDayInMonth() {
  const [month, setMonth] = useState(() => toYyyyMm(new Date()));
  const [data, setData] = useState<OpenedByDayResponse | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    let alive = true;

    setLoading(true);
    fetchOpenedByDay(month)
      .then((res) => {
        if (!alive) return;
        setData(res);
      })
      .finally(() => {
        if (!alive) return;
        setLoading(false);
      });

    return () => {
      alive = false;
    };
  }, [month]);

  const change = data?.changePct ?? 0;
  const trendUp = change >= 0;

  const chartData = useMemo(() => {
    if (!data) return [];
    return data.days.map((p) => ({
      day: p.day,
      opened: p.opened,
      // label curto pro tooltip
      dayLabel: new Date(p.day + "T00:00:00").toLocaleDateString("pt-BR", {
        day: "2-digit",
        month: "2-digit",
      }),
    }));
  }, [data]);

  return (
    <div className="card bg-white rounded-xl shadow-sm border border-black/5 overflow-hidden">
      <div className="card-body p-5">
        <div className="flex items-start justify-between gap-4">
          <div className="min-w-0">
            <div className="text-sm text-black/60">Abertos no mês</div>

            <div className="mt-1 flex items-center gap-2">
              <span
                className={[
                  "inline-flex items-center gap-1 text-sm font-medium",
                  trendUp ? "text-emerald-600" : "text-rose-600",
                ].join(" ")}
                title="Comparado ao mês anterior"
              >
                <span className="inline-block">
                  {trendUp ? "▲" : "▼"}
                </span>
                {pctLabel(change)}
              </span>

              <span className="text-sm text-black/50">
                último mês
              </span>
            </div>

            <div className="mt-2 text-xs text-black/40">
              {data ? `${formatMonthLabel(data.month)} • Total: ${data.total}` : "—"}
            </div>
          </div>

          {/* ícone (pode trocar por lucide/fa) */}
          <div className="shrink-0">
            <div className="h-10 w-10 rounded-full bg-sky-500/15 flex items-center justify-center">
              <span className="text-sky-600">🖼️</span>
            </div>
          </div>
        </div>

        {/* gráfico */}
        <div className="mt-4 h-20">
          {loading ? (
            <div className="h-full w-full rounded-lg bg-black/5 animate-pulse" />
          ) : (
            <ResponsiveContainer width="100%" height="100%">
              <AreaChart data={chartData} margin={{ top: 6, right: 4, left: 4, bottom: 0 }}>
                <Tooltip
                  cursor={{ strokeWidth: 0 }}
                  content={({ active, payload }) => {
                    if (!active || !payload?.length) return null;
                    const p = payload[0].payload as any;
                    return (
                      <div className="rounded-md border border-black/10 bg-white px-3 py-2 shadow-sm">
                        <div className="text-xs text-black/60">{p.dayLabel}</div>
                        <div className="text-sm font-semibold">{p.opened} abertos</div>
                      </div>
                    );
                  }}
                />
                <Area
                  type="monotone"
                  dataKey="opened"
                  strokeWidth={2}
                  dot={false}
                  // sem setar cores “hard-coded” no gráfico? aqui usei CSS var.
                  stroke="var(--color-primary, #3b82f6)"
                  fill="var(--color-primary, #3b82f6)"
                  fillOpacity={0.12}
                />
              </AreaChart>
            </ResponsiveContainer>
          )}
        </div>

        {/* opcional: seletor do mês (simples) */}
        <div className="mt-3 flex justify-end">
          <input
            className="text-xs border border-black/10 rounded-md px-2 py-1 text-black/70"
            type="month"
            value={month}
            onChange={(e) => setMonth(e.target.value)}
            aria-label="Selecionar mês"
          />
        </div>
      </div>
    </div>
  );
}
