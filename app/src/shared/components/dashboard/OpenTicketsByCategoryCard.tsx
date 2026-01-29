"use client";

import { useEffect, useMemo, useState } from "react";
import { PieChart, Pie, Cell, ResponsiveContainer, Tooltip } from "recharts";

import { fetchOpenByCategory, OpenByCategoryResponse } from "@/lib/sensritDashboard";

const COLORS = ["#4F46E5", "#38BDF8", "#A78BFA", "#22C55E", "#F59E0B", "#FB7185"];

function monthLabel(ym: string) {
  // "2026-01" -> "Jan/2026" (pt-BR)
  const [y, m] = ym.split("-").map(Number);
  const d = new Date(y, (m ?? 1) - 1, 1);
  return d.toLocaleDateString("pt-BR", { month: "short", year: "numeric" });
}

export default function OpenByCategoryCard({ month }: { month?: number }) {
  const [data, setData] = useState<OpenByCategoryResponse | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    let alive = true;

    (async () => {
      try {
        setLoading(true);
        const resp = await fetchOpenByCategory(month);
        if (alive) setData(resp);
      } finally {
        if (alive) setLoading(false);
      }
    })();

    return () => {
      alive = false;
    };
  }, [month]);

  const chartData = useMemo(() => {
    if (!data) return [];
    return data.categories.map((c, idx) => ({
      name: c.label,
      value: c.total,
      color: COLORS[idx % COLORS.length],
    }));
  }, [data]);

  const isUp = (data?.changePct ?? 0) >= 0;
  const changeText = `${isUp ? "+" : ""}${Math.round((data?.changePct ?? 0) * 100)}%`;

  return (
    <div className="card bg-white">
      <div className="card-body p-6">
        <div className="flex items-start justify-between gap-6">
          <div className="min-w-0">
            <div className="flex items-center gap-2">
              <h3 className="text-sm font-semibold text-slate-800">Abertos por categoria</h3>
              {data?.month && (
                <span className="text-xs text-slate-500">{monthLabel(data.month)}</span>
              )}
            </div>

            <div className="mt-3 flex items-end gap-3">
              <p className="text-3xl font-semibold leading-none text-slate-900">
                {loading ? "…" : (data?.total ?? 0)}
              </p>

              <div className="flex items-center gap-2 pb-1">
                <span
                  className={[
                    "inline-flex h-6 items-center gap-1 rounded-full px-2 text-xs font-semibold",
                    isUp ? "bg-emerald-50 text-emerald-700" : "bg-rose-50 text-rose-700",
                  ].join(" ")}
                >
                  <span className="text-sm leading-none">{isUp ? "↗" : "↘"}</span>
                  {loading ? "…" : changeText}
                </span>

                <span className="text-xs text-slate-500">último mês</span>
              </div>
            </div>

            {/* legend */}
            <div className="mt-5 flex flex-wrap items-center gap-x-6 gap-y-2">
              {loading ? (
                <span className="text-xs text-slate-500">Carregando categorias…</span>
              ) : (
                chartData.map((item) => (
                  <div key={item.name} className="flex items-center gap-2">
                    <span className="h-2.5 w-2.5 rounded-full" style={{ backgroundColor: item.color }} />
                    <span className="text-xs text-slate-600">{item.name}</span>
                  </div>
                ))
              )}
            </div>
          </div>

          {/* donut */}
          <div className="h-30 w-30 shrink-0">
            {loading ? (
              <div className="h-full w-full animate-pulse rounded-full bg-slate-100" />
            ) : (
              <ResponsiveContainer width="100%" height="100%">
                <PieChart>
                  <Tooltip
                    formatter={(value: unknown, name: unknown) => [`${value}`, name]}
                    labelFormatter={(label) => String(label)}
                  />
                  <Pie
                    data={chartData}
                    dataKey="value"
                    innerRadius={34}
                    outerRadius={46}
                    paddingAngle={2}
                    stroke="transparent"
                  >
                    {chartData.map((it, idx) => (
                      <Cell key={idx} fill={it.color} />
                    ))}
                  </Pie>
                </PieChart>
              </ResponsiveContainer>
            )}
          </div>
        </div>
      </div>
    </div>
  );
}
