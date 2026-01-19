"use client"

import { Icon } from "@iconify/react";
import { useEffect, useMemo, useRef, useState } from "react";
import { DayPicker } from "react-day-picker";

type Props = {
  value: { from: string; to: string };
  onChange: (next: { from: string; to: string }) => void;
  disabled?: boolean;
}

function toYmd(d: Date): string {
  const y     = d.getFullYear();
  const m     = String(d.getMonth() + 1).padStart(2, "0");
  const day   = String(d.getDate()).padStart(2, "0");

  return `${y}-${m}-${day}`;
}

function parseYmd(ymd: string): Date {
  return new Date(ymd + "T00:00:00");
}

function addDays(date: Date, days: number): Date {
  const d = new Date(date);
  d.setDate(d.getDate() + days);

  return d;
}

function isSameDay(a: Date, b: Date) {
  return (
    a.getFullYear() === b.getFullYear() &&
    a.getMonth() === b.getMonth() &&
    a.getDate() === b.getDate()
  );
}

function isBetweenInclusive(day: Date, start: Date, end: Date) {
  const t = day.getTime();
  return t >= start.getTime() && t <= end.getTime();
}

export function RangePicker7Days({ value, onChange, disabled }: Props) {
  const [open, setOpen]   = useState(false);
  const popRef            = useRef<HTMLDivElement | null>(null);

  const anchor = useMemo(() => {
    if (!value?.to) return undefined;
    return parseYmd(value.to);
  }, [value?.to]);

  const rangeFrom = useMemo(() => (value?.from ? parseYmd(value.from) : undefined), [value?.from]);
  const rangeTo   = useMemo(() => (value?.to ? parseYmd(value.to) : undefined), [value?.to]);


  //fecha ao clicar fora
  useEffect(() => {
    function onDocClick(e:MouseEvent) {
      if (!open) return;

      const target = e.target as Node;
      if (popRef.current && !popRef.current.contains(target)) {
        setOpen(false);
      }
    }
    document.addEventListener("mousedown", onDocClick);

    return () => document.removeEventListener("mousedown", onDocClick);
  }, [open]);

  function handleAnchorClick(day: Date) {
    const to = day;
    const from = addDays(day, -6); // 7 dias terminando no dia clicado
    onChange({ from: toYmd(from), to: toYmd(to) });
    //setOpen(false);
  }

  const modifiers = useMemo(() => {
    if (!rangeFrom || !rangeTo) return {};
    return {
      inRange: (day: Date) => isBetweenInclusive(day, rangeFrom, rangeTo),
      rangeStart: (day: Date) => isSameDay(day, rangeFrom),
      rangeEnd: (day: Date) => isSameDay(day, rangeTo),
    };
  }, [rangeFrom, rangeTo]);

  return (
    <div className="relative" ref={popRef}>
      <button
        type="button"
        className="btn btn-datepicker"
        disabled={disabled}
        onClick={() => setOpen((v) => !v)}
      >
        <span className="inline-flex items-center justify-center text-center m-0">
          <Icon icon={"tabler:calendar-week-filled"}  name="calendar" className="w-6 h-6" />
        </span>
      </button>

      {open && (
        <div className="absolute right-0 mt-2 z-50 w-[340px] rounded-[var(--radius-md)] border border-[color:var(--color-bordergray)] bg-white shadow-[var(--shadow-md)] p-3">
          <div className="text-xs text-[color:var(--color-bodytext)]/70 mb-2">
            Clique em um dia para selecionar 7 dias (terminando no dia clicado).
          </div>

          <DayPicker
            mode="single"
            selected={anchor}
            onDayClick={handleAnchorClick}
            weekStartsOn={0}
            showOutsideDays
            fixedWeeks
            modifiers={modifiers}
            className="p-2"
            classNames={{
              months: "flex flex-col gap-4",
              month: "flex flex-col space-y-3 items-center",
              caption: "flex items-center justify-between px-2",
              caption_label: "text-sm font-semibold text-[color:var(--color-dark)]",
              nav: "flex items-start gap-2",
              nav_button:
                "h-9 w-9 inline-flex items-center justify-center rounded-[var(--radius-md)] border border-[color:var(--color-bordergray)] hover:bg-[color:var(--color-darkgrey)] transition",
              table: "w-full border-collapse items-center",
              head_row: "grid grid-cols-7 px-1",
              head_cell: "text-xs font-medium text-[color:var(--color-bodytext)]/70 text-center py-2",
              row: "grid grid-cols-7 px-1",
              cell: "h-10 w-10 flex items-center text-center justify-center",
              day: "h-9 w-9 rounded-[var(--radius-md)] text-sm text-[color:var(--color-dark)] hover:bg-[color:var(--color-lightgray)] transition items-center text-center",
              day_today: "border border-[color:var(--color-primary)] ",
              day_outside: "text-[color:var(--color-bodytext)]/40",
              day_disabled: "text-[color:var(--color-bodytext)]/30",
              day_selected:
                "bg-[color:var(--color-primary)] text-white hover:bg-[color:var(--color-primary-hover)]",
              day_range_start:
                "bg-[color:var(--color-primary)] text-white hover:bg-[color:var(--color-primary-hover)]",
              day_range_end:
                "bg-[color:var(--color-primary)] text-white hover:bg-[color:var(--color-primary-hover)]",
              day_range_middle:
                "bg-[color:var(--color-lightinfo)] text-[color:var(--color-dark)]",
            }}
            modifiersClassNames={{
              inRange: "bg-[color:var(--color-lightinfo)]",
              rangeStart:
                "bg-[color:var(--color-primary)] text-white hover:bg-[color:var(--color-primary-hover)]",
              rangeEnd:
                "bg-[color:var(--color-primary)] text-white hover:bg-[color:var(--color-primary-hover)]",
            }}
          />


          <div className="mt-2 flex justify-end gap-2">
            <button
              type="button"
              className="btn h-9 border border-[color:var(--color-bordergray)] hover:bg-black/5"
              onClick={() => setOpen(false)}
            >
              Fechar
            </button>
          </div>
        </div>
      )}
    </div>
  );
}