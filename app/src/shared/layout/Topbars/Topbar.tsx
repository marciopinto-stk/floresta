"use client"

import { Icon } from "@iconify/react";
import Link from "next/dist/client/link";
import React, { useEffect, useId, useRef, useState } from "react";

type Notification = {
  id: string;
  title: string;
  subtitle?: string;
}

type User = {
  name: string;
  email?: string;
}

type Props = {
  onOpenSidebar: () => void;
  user?: User;
  notifications?: Notification[];
}

function useClickOutside(
  refs: Array<React.RefObject<HTMLElement | null>>,
  onOutside: () => void
) {
  useEffect(() => {
    function onMouseDown(e: MouseEvent) {
      const target = e.target as Node;
      const clickedInside = refs.some((r) => r.current?.contains(target))

      if (!clickedInside) onOutside();
    }

    document.addEventListener("mousedown", onMouseDown);
    return () => document.removeEventListener("mousedown", onMouseDown);
  }, [refs, onOutside]);
}

export default function Topbar ({
  onOpenSidebar,
  user = { name: "Usuário" },
  notifications = [{ id: "1", title: "Novo chamado aberto", subtitle: "R14532" }],
}: Props) {
  const [notifOpen, setNotifOpen] = useState(false);
  const [userOpen, setUserOpen]   = useState(false);

  const notifWrapRef  = useRef<HTMLDivElement | null>(null);
  const userWrapRef   = useRef<HTMLDivElement | null>(null);

  const notifMenuId = useId();
  const userMenuId  = useId();
  
  // Fecha com ESC
  useEffect(() => {
    function onKeyDown(e: KeyboardEvent) {
      if (e.key !== "Escape") return;
      setNotifOpen(false);
      setUserOpen(false);
    }

    window.addEventListener("keydown", onKeyDown);
    return () => window.removeEventListener("keydown", onKeyDown);
  }, []);

  // Click outside
  // Click outside
  useClickOutside([notifWrapRef], () => setNotifOpen(false));
  useClickOutside([userWrapRef], () => setUserOpen(false));
    
  return(
    <header className="sticky top-0 z-30 w-full border-b border-black/10 bg-white/90 backdrop-blur">
      <div className="flex h-14 items-center justify-between px-5">
        {/* Left */}
        <div className="flex itens center gap-3">
          {/*Toggle sidebar (mobile*/}
          <button
            type="button"
            onClick={onOpenSidebar}
            className="xl:hidden inline-flex h-10 w-10 items-center justify-center rounded-[7px] hover:bg-black/5 text-black/80"
            aria-label="Abrir menu"
          >
            <Icon icon="tabler:menu-2" className="text-2xl" />
          </button>

          {/* Espaço para breadcrumb/título */}
          <div className="hidden sm:block">
            <div className="text-sm font-semibold text-black/80">Floresta</div>
            <div className="text-xs text-black/50">Admin</div>
          </div>
        </div>

        {/* Right */}
        <div className="flex items-center gap-2">
          {/* Notifications */}
          <div className="relative" ref={notifWrapRef}>
            <button
              type="button"
              onClick={() => {
                setUserOpen(false);
                setNotifOpen((v) => !v);
              }}
              className="relative inline-flex h-10 w-10 items-center justify-center rounded-[7px] hover:bg-black/5 text-black/80"
              aria-haspopup="menu"
              aria-expanded={notifOpen}
              aria-controls={notifMenuId}
              aria-label="Notificações"
            >
              <Icon icon="tabler:bell-ringing" className="text-2xl" />
              {notifications.length > 0 && (
                <span className="absolute top-2 right-2 h-2 w-2 rounded-full bg-[var(--color-primary)]" />
              )}
            </button>

            <div
              id={notifMenuId}
              role="menu"
              className={[
                "absolute right-0 mt-2 w-[320px] rounded-[7px] border border-black/10 bg-white shadow-[var(--shadow-md)]",
                notifOpen ? "block" : "hidden",
              ].join(" ")}
            >
              <div className="px-4 py-3 border-b border-black/10">
                <div className="text-sm font-semibold text-black/80">
                  Notificações
                </div>
                <div className="text-xs text-black/50">
                  {notifications.length} item(ns)
                </div>
              </div>

              <ul className="max-h-[320px] overflow-auto py-1">
                {notifications.length === 0 ? (
                  <li className="px-4 py-4 text-sm text-black/60">
                    Nenhuma notificação.
                  </li>
                ) : (
                  notifications.map((n) => (
                    <li key={n.id}>
                      <button
                        type="button"
                        role="menuitem"
                        className="w-full text-left px-4 py-3 hover:bg-[color-mix(in_oklch,var(--color-primary)_12%,white)]"
                        onClick={() => setNotifOpen(false)}
                      >
                        <div className="text-sm font-semibold text-black/80">
                          {n.title}
                        </div>
                        {n.subtitle && (
                          <div className="text-xs text-black/50">{n.subtitle}</div>
                        )}
                      </button>
                    </li>
                  ))
                )}
              </ul>
            </div>
          </div>

          {/* User menu */}
          <div className="relative" ref={userWrapRef}>
            <button
              type="button"
              onClick={() => {
                setNotifOpen(false);
                setUserOpen((v) => !v);
              }}
              className="inline-flex items-center gap-2 rounded-[7px] px-2 py-1.5 hover:bg-black/5"
              aria-haspopup="menu"
              aria-expanded={userOpen}
              aria-controls={userMenuId}
            >
              <span className="inline-flex h-9 w-9 items-center justify-center rounded-full bg-black/10 text-black/70">
                <Icon icon="tabler:user" className="text-xl" />
              </span>

              <span className="hidden sm:flex flex-col items-start leading-tight">
                <span className="text-sm font-semibold text-black/80">
                  {user.name}
                </span>
                {user.email && (
                  <span className="text-xs text-black/50">{user.email}</span>
                )}
              </span>

              <Icon icon="tabler:chevron-down" className="hidden sm:block text-lg text-black/60" />
            </button>

            <div
              id={userMenuId}
              role="menu"
              className={[
                "absolute right-0 mt-2 w-[220px] rounded-[7px] border border-black/10 bg-white shadow-[var(--shadow-md)]",
                userOpen ? "block" : "hidden",
              ].join(" ")}
            >
              <div className="px-4 py-3 border-b border-black/10">
                <div className="text-sm font-semibold text-black/80">{user.name}</div>
                {user.email && <div className="text-xs text-black/50">{user.email}</div>}
              </div>

              <div className="py-1">
                
               <button
                  type="button"
                  role="menuitem"
                  className="w-full px-4 py-2 text-left text-sm text-black/70 hover:bg-black/5"
                  onClick={() => setUserOpen(false)}
                >
                  Meu perfil
                </button> 

                <div className="w-full px-4 py-2 text-left text-sm text-black/70 hover:bg-black/5">
                  <Link href="/settings" className="">Configurações</Link>
                </div>
                
                <div className="my-1 border-t border-black/10" />

                <button
                  type="button"
                  role="menuitem"
                  className="w-full px-4 py-2 text-left text-sm text-[var(--color-error)] hover:bg-[color-mix(in_oklch,var(--color-error)_10%,white)]"
                  onClick={() => setUserOpen(false)}
                >
                  Sair
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      
        
    </header>
  );
}