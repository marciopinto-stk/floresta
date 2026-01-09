"use client"

import Sidebar from "@/shared/layout/Sidebars/Sidebar";
import Topbar from "@/shared/layout/Topbars/Topbar";
import { ReactNode, useEffect, useState } from "react";

export default function AdminLayout({ children }: {children: ReactNode})
{
  const [sidebarOpen, setSidebarOpen] = useState(false);

  useEffect(() => {
    function onKeyDown(e: KeyboardEvent) {
      if (e.key === "Escape") setSidebarOpen(false);
    }
    window.addEventListener("keydown", onKeyDown);
    return () => window.removeEventListener("keydown", onKeyDown);
  }, []);

  useEffect(() => {
    document.documentElement.style.overflow = sidebarOpen ? "hidden" : "";
    return () => {
      document.documentElement.style.overflow = "";
    };
  }, [sidebarOpen]);

  return (
    <div className="min-h-dvh">
      <Sidebar isOpen={sidebarOpen} onClose={() => setSidebarOpen(false)} />
      
      <div className="xl:pl-[270px] w-full">
        <Topbar
          onOpenSidebar={() => setSidebarOpen(true)}
          user={{ name: "Márcio Pinto", email: "marcio@empresa.com" }}
          notifications={[
            { id: "1", title: "Novo chamado aberto", subtitle: "R14253" },
          ]}
        />
        
        <main className="w-full p-6">
          {children}
        </main>
      </div>
    </div>
  );
}