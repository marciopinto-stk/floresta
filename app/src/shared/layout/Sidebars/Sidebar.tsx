"use client"

import Image from "next/image";
import Link from "next/link";
import { Icon } from "@iconify/react";
import { usePathname } from "next/navigation";
import { Fragment } from "react/jsx-runtime";
import { useEffect, useMemo, useState } from "react";

type SidebarProps = {
  isOpen: boolean;
  onClose: () => void;
}

type NavItem = {
  label: string;
  href: string;
  icon: string;
};

type NavSection = {
  key: string;
  label: string;
  icon?: string;
  items: NavItem[];
}

const sections: NavSection[] = [
  {
    key: "home",
    label: "HOME",
    icon: "tabler:home",
    items: [
      {label: "Dashboard", href: "/dashboard", icon: "tabler:layout-dashboard"}
    ]
  },
  {
    key: "sensrit",
    label: "SENSRIT",
    icon: "tabler:bandage",
    items: [
      {label: "Chamados", href: "/chamados", icon: "tabler:bandage"}
    ]
  },
  {
    key: "s2",
    label: "S2",
    icon: "tabler:medicine-syrup",
    items: [
      {label: "Financeiro", href: "/s2/financeiro", icon: "tabler:pig-money"}
    ]
  },
  {
    key: "yalo",
    label: "YALO",
    icon: "tabler:basket",
    items: [
      {label: "Clientes", href: "/yalo/clientes", icon: "tabler:layout-user"}
    ]
  }
];

export default function Sidebar ({isOpen, onClose}: SidebarProps) {
  const pathname = usePathname();

  const activeSectionKey = useMemo(() => {
    const hit = sections.find((s) => s.items.some((i) => pathname === i.href || pathname.startsWith(i.href + "/")));
    return hit?.key ?? sections[0]?.key ?? "";
  }, [pathname]);

  // accordion: quais seções estão abertas
  const [openKeys, setOpenKeys] = useState<Record<string, boolean>>({});

  // auto-abre a seção do path atual
  useEffect(() => {
    setOpenKeys((prev) => ({ ...prev, [activeSectionKey]: true }));
  }, [activeSectionKey]);

  function toogleSection(key: string) {
    setOpenKeys((prev) => ({ ...prev, [key]: !prev[key] }));
  }

  function isActive(href: string) {
    return pathname === href || pathname.startsWith(href + "/");
  }

  return (
    <Fragment>
      {/* Overlay (mobile only) */}
      <div
        className={[
          "fixed inset-0 z-40 bg-black-40 transiction-opacity xl:hidden",
          isOpen ? "opacity-100" : "pointer-events-none opacity-0"
        ].join(" ")}
        onClick={onClose}
        aria-hidden="true"
      />

      <aside id="app-sidebar" 
        className={[
          // layout
          "fixed left-0 top-0 z-50 h-dvh w-[270px] shrink-0 border-r border-black/10 bg-white",
          "transition-transform duration-300",
          "xl:translate-x-0 xl-flex xl:flex-col",
          isOpen ? "translate-x-0" : "-translate-x-full"
        ].join(" ")}
        aria-label="Sidebar"
      >
        {/* Header */}
        <div className="flex items-center justify-between p-5">
          <Link href="/" aria-label="Ir para Home" onClick={onClose}>
            <Image src="/images/logo-floresta.png" alt="Logo floresta" width={144} height={30} priority />
          </Link>

          <button
            type="button"
            className="inline-flex h-10 w-10 items-center justify-center rounded-[7px] hover:bg-black/5 xl:hidden"
            onClick={onClose}
            aria-label="Fechar menu"
          >
            <Icon icon="tabler:x" className="text-2xl" />
          </button>
        </div>

        {/* Content */}
        <div className="flex-1 overflow-y-auto px-4 pb-6">
          <nav aria-label="Navegação principal" className="space-y-2">
            {sections.map((section) => {
              const open        = !!openKeys[section.key];
              const controlsId  = `sidebar-section-${section.key}`;

              return (
                <div key={section.key} className="rounded-[7px]">
                  {/* Accordion trigger */}
                  <button
                    type="button"
                    className={[
                      "w-full flex items-center justify-between px-3 py-2 rounded-[7px]",
                      "text-xs font-bold tracking-wide text-black/60 hover:bg-black/5",
                    ].join(" ")}
                    onClick={() => toogleSection(section.key)}
                    aria-expanded={open}
                    aria-controls={controlsId}
                  >
                    <span className="flex items-center gap-2">
                      {section.icon && <Icon icon={section.icon} className="text-base" />}
                      {section.label}
                    </span>

                    <Icon
                      icon="tabler:chevron-down"
                      className={[
                        "text-lg transition-transform",
                        open ? "rotate-180" : "rotate-0",
                      ].join(" ")}
                    />
                  </button>

                  {/* Accordion panel */}
                  <div
                    id="{controlsId}"
                    className={[
                      "grid transition-[grid-template-rows] duration-200",
                      open ? "grid-rows-[1fr]" : "grid-rows-[0fr]",
                    ].join(" ")}
                  >
                    <div className="overflow-hidden">
                      <ul className="mt-1 space-y-1 pl-1">
                        {section.items.map((item) => {
                          const active = isActive(item.href);

                          return (
                            <li key={item.href}>
                              <Link
                                href={item.href}
                                onClick={onClose}
                                aria-current={active ? "page" : undefined}
                                className={[
                                  "flex items-center gap-[15px] px-3 py-2 rounded-[7px] text-[14px] leading-[25px] transition-colors",
                                  active ? "bg-[var(--color-primary)] text-white" : "text-black/80 hover:text-[var(--color-primary)] hover:bg-black/5",
                                ].join(" ")}
                              >
                                {item.icon && <Icon icon={item.icon} className="text-xl" />}
                                <span>{item.label}</span>
                              </Link>
                            </li>
                          );
                        })}
                      </ul>
                    </div>
                  </div>
                </div>
              );
            })}
          </nav>
        </div>
      </aside>
    </Fragment>
  );
}
