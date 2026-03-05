"use client";

import { useEffect, useMemo, useRef, useState } from "react";
import { OptionItem, OptionsResponse } from "./types";
import { error } from "console";

type Props = {
	value: OptionItem | null;
	onChange: (value: OptionItem | null) => void;

	label?: string;
	placeholder?: string;
	disabled?: boolean;

	minChars?: number;
	debounceMs?: number;
	limit?: number;

	search: (params: { q: string; page: number; limit: number }) => Promise<OptionsResponse>;
};

export default function Autocomplete({
	value,
	onChange,
	label = "Selecionar",
	placeholder = "Digite para buscar...",
	disabled,
	minChars = 3,
	debounceMs = 350,
	limit = 10,
	search
}: Props){
	const [query, setQuery]             = useState(value?.label ?? "");
	const [items, setItems]             = useState<OptionItem[]>([]);
	const [open, setOpen]               = useState(false);
	const [loading, setLoading]         = useState(false);
	const [error, setError]             = useState<string | null>(null);
	const [activeIndex, setActiveIndex] = useState(-1);
	const [page, setPage]               = useState(1);
	const [hasNext, setHasNext]         = useState(false);
	
	const rootRef = useRef<HTMLDivElement | null>(null);
	const canSearch = useMemo(() =>query.trim().length >= minChars, [query, minChars]);

	const inputRef = useRef<HTMLInputElement | null>(null);

	// Fecha se clicar fora
	useEffect(() => {
		function onDocClick(e: MouseEvent) {
			if (!rootRef.current) {
				return;
			}

			if (!rootRef.current.contains(e.target as Node)) {
				setOpen(false);
			}
		}

		document.addEventListener("mouseDown", onDocClick);
		return () => document.removeEventListener("mouseDown", onDocClick);
	}, []);

    // Sincroniza o texto quando o value muda
	useEffect(() => {
		setQuery(value?.label ?? "");
	}, [value?.id]); // eslint-disable-line react-hooks/exhaustive-deps


	// busca com debounce
	useEffect(() => {
		setError(null);

		if (value && query.trim() === value.label) {
			setOpen(false);
			setItems([]);
			setActiveIndex(-1);
			setPage(1);
			setHasNext(false);
			return;
		}

		if (!canSearch) {
			setItems([]);
      setOpen(false);
      setActiveIndex(-1);
      setPage(1);
      setHasNext(false);
      return;
		}

		const t = window.setTimeout(async () => {
			setLoading(true);
      setOpen(true);
      setActiveIndex(-1);

			try {
				const res = await search({ q: query.trim(), page: 1, limit });
				setItems(res.options);
        setPage(res.pagination?.page ?? 1);
        setHasNext(Boolean(res.pagination?.hasNext));
			} catch (e: unknown) {
        setItems([]);
        setHasNext(false);
        setError(e?.message ?? "Falha ao buscar.");
      } finally {
        setLoading(false);
      }
		}, debounceMs);

		return () => window.clearTimeout(t);
	}, [query, canSearch, debounceMs, limit, search]);

	function select(item: OptionItem) {
    onChange(item);
    setQuery(item.label);
    setOpen(false);
    setItems([]);
    setActiveIndex(-1);
		setHasNext(false);
		setPage(1);

		requestAnimationFrame(() => inputRef.current?.blur());
  }

  function clear() {
    onChange(null);
    setQuery("");
    setItems([]);
    setOpen(false);
    setActiveIndex(-1);
    setPage(1);
    setHasNext(false);
  }

	async function loadMore() {
    if (loading || !hasNext) return;

    setLoading(true);
    try {
      const nextPage = page + 1;
      const res = await search({ q: query.trim(), page: nextPage, limit });
      setItems((prev) => [...prev, ...res.options]);
      setPage(res.pagination?.page ?? nextPage);
      setHasNext(Boolean(res.pagination?.hasNext));
    } catch (e: unknown) {
      setError(e?.message ?? "Falha ao buscar.");
    } finally {
      setLoading(false);
    }
  }

	function onKeyDown(e: React.KeyboardEvent<HTMLInputElement>) {
    if (!open) return;

    if (e.key === "ArrowDown") {
      e.preventDefault();
      setActiveIndex((prev) => Math.min(prev + 1, items.length - 1));
    } else if (e.key === "ArrowUp") {
      e.preventDefault();
      setActiveIndex((prev) => Math.max(prev - 1, 0));
    } else if (e.key === "Enter") {
      if (activeIndex >= 0 && items[activeIndex]) {
        e.preventDefault();
        select(items[activeIndex]);
      }
    } else if (e.key === "Escape") {
      setOpen(false);
    }
  }

	return (
    <div ref={rootRef} className="relative">
      <label className="block text-sm font-medium mb-2">{label}</label>

      <div className="flex gap-2">
        <input
					ref={inputRef}
          value={query}
          onChange={(e) => {
            const v = e.target.value;
            setQuery(v);
            if (value && v !== value.label) onChange(null);
          }}
          onFocus={() => canSearch && setOpen(true)}
          onKeyDown={onKeyDown}
          disabled={disabled}
          className="form-control"
          placeholder={placeholder}
          autoComplete="off"
        />
      </div>

      {open && (
        <div className="form-control absolute rounded-none z-20 w-full border bg-white shadow-sm overflow-hidden">
          <ul className="max-h-64 overflow-auto">
            {!loading && !error && items.length === 0 && (
              <li className="px-3 py-3 text-sm opacity-70">Nenhum resultado.</li>
            )}

            {items.map((it, idx) => (
              <li
                key={it.id}
                onMouseDown={(e) => e.preventDefault()}
                onClick={() => select(it)}
                className={[
                  "px-3 py-2 text-sm cursor-pointer",
                  idx === activeIndex ? "bg-slate-100" : "hover:bg-slate-100",
                ].join(" ")}
              >
                <div className="font-medium">{it.label}</div>
              </li>
            ))}
          </ul>

          {hasNext && (
            <div className="border-t dark:border-gray-700 p-2">
              <button
                type="button"
                onClick={loadMore}
                disabled={loading}
                className="w-full px-3 py-2 text-sm hover:bg-gray-50 disabled:opacity-60"
              >
                {loading ? "Carregando…" : "Carregar mais"}
              </button>
            </div>
          )}
        </div>
      )}
    </div>
  );
};

