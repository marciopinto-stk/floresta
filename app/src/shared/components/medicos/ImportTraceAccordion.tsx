"use client";

import { useMemo, useState } from "react";

export type ImportLogItem = {
  step: string;
  at: string; // ISO
  level: "info" | "warning" | "error" | "debug" | string;
  message: string;
  context: unknown;
};

type Props = {
  logs: ImportLogItem[];
  title?: string;
};

type Grouped = {
  key: string;      // ex: validate_reference_month
  items: ImportLogItem[];
  levels: Set<string>;
  firstAt?: string;
  lastAt?: string;
};

function baseStep(step: string) {
  // remove apenas o último token se for sufixo de status
  const suffixes = new Set(["start", "done", "error", "fail", "failed", "success", "warning"]);
  const parts = step.split(":");
  const last = parts[parts.length - 1];

  if (parts.length > 1 && suffixes.has(last)) {
    parts.pop();
    return parts.join(":");
  }

  return step;
}

function formatAt(iso: string) {
  // Mantém simples; se quiser, dá pra usar Intl.DateTimeFormat depois
  return iso.replace("T", " ").replace("Z", " UTC");
}

function safeJson(value: unknown) {
  if (value === null || value === undefined) return "null";
  try {
    return JSON.stringify(value, null, 2);
  } catch {
    return String(value);
  }
}

function badgeClass(level: string) {
  if (level === "error") return "bg-red-100 text-red-800 border-red-200";
  if (level === "warning") return "bg-amber-100 text-amber-900 border-amber-200";
  if (level === "debug") return "bg-gray-100 text-gray-800 border-gray-200";
  return "bg-emerald-100 text-emerald-800 border-emerald-200"; // info default
}

export default function ImportTraceAccordion({ logs, title = "Trace da importação" }: Props) {
  const groups = useMemo(() => {
    const map = new Map<string, Grouped>();

    for (const item of logs ?? []) {
      const key = baseStep(item.step);

      const g = map.get(key) ?? {
        key,
        items: [],
        levels: new Set<string>(),
        firstAt: undefined,
        lastAt: undefined,
      };

      g.items.push(item);
      g.levels.add(item.level);

      if (!g.firstAt || item.at < g.firstAt) g.firstAt = item.at;
      if (!g.lastAt || item.at > g.lastAt) g.lastAt = item.at;

      map.set(key, g);
    }

    // ordena grupos pela primeira ocorrência (cronológico)
    return Array.from(map.values()).sort((a, b) => (a.firstAt ?? "").localeCompare(b.firstAt ?? ""));
  }, [logs]);

  const [openKey, setOpenKey] = useState<string | null>(groups[0]?.key ?? null);

  if (!logs || logs.length === 0) {
    return (
      <div className="rounded-xl border bg-white p-4">
        <div className="font-semibold">{title}</div>
        <div className="mt-2 text-sm opacity-70">Nenhum log disponível.</div>
      </div>
    );
  }

  return (
    <div className="bg-white">
      <div className="border-b border-slate-400 p-4">
        <div className="font-semibold">{title}</div>
        <div className="mt-1 text-xs opacity-70">
          {logs.length} eventos • {groups.length} etapas
        </div>
      </div>

      <div className="divide-y divide-slate-400">
        {groups.map((g) => {
          const hasError = g.items.some((x) => x.level === "error" || x.step.endsWith(":error"));
          const subtitle = `${g.items.length} evento(s) • ${formatAt(g.firstAt!)} → ${formatAt(g.lastAt!)}`;

          return (
            <div key={g.key} className="p-0">
              <button
                type="button"
                onClick={() => setOpenKey((prev) => (prev === g.key ? null : g.key))}
                className="w-full p-4 text-left bg-slate-50 hover:bg-slate-100"
              >
                <div className="flex items-start justify-between gap-3">
                  <div>
                    <div className="flex items-center gap-2">
                      <span className="font-semibold">{g.key}</span>
                      {hasError && (
                        <span className="rounded-full border-xs px-2 py-0.5 text-xs bg-red-100 text-red-800 border-red-200">
                          error
                        </span>
                      )}
                    </div>
                    <div className="mt-1 text-xs opacity-70">{subtitle}</div>
                  </div>

                  <span className="text-xs opacity-60">{openKey === g.key ? "▲" : "▼"}</span>
                </div>
              </button>

              {openKey === g.key && (
                <div className="px-4 pb-4">
                  <div className="space-y-3">
                    {g.items
                      .slice()
                      .sort((a, b) => a.at.localeCompare(b.at))
                      .map((it, idx) => (
                        <div key={`${it.step}-${it.at}-${idx}`} className="p-3">
                          <div className="flex flex-wrap items-center gap-2">
                            <span className={`rounded-full border px-2 py-0.5 text-xs ${badgeClass(it.level)}`}>
                              {it.level}
                            </span>
                            <span className="font-mono text-xs opacity-80">{it.step}</span>
                            <span className="text-xs opacity-60">{formatAt(it.at)}</span>
                          </div>

                          <div className="mt-2 text-sm">{it.message}</div>

                          <details className="mt-2">
                            <summary className="cursor-pointer text-xs opacity-70">context</summary>
                            <pre className="mt-2 overflow-auto rounded-lg border bg-gray-50 p-3 text-xs">
                              {safeJson(it.context)}
                            </pre>
                          </details>
                        </div>
                      ))}
                  </div>
                </div>
              )}
            </div>
          );
        })}
      </div>
    </div>
  );
}