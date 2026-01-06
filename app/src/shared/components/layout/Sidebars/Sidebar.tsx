"use client"

import Image from "next/image";
import Link from "next/link";
import { Icon } from "@iconify/react";
import { usePathname } from "next/navigation";

const Sidebar = () => {
  const pathname = usePathname();

  function isActive(href: string) {
    return pathname === href || pathname.startsWith(href + "/") ? "active" : "";
  }
  return (
    <aside id="application-sidebar-brand" className="hs-overlay hs-overlay-open:translate-x-0 -translate-x-full transform hidden xl:block xl:translate-x-0 xl:end-auto xl:bottom-0 fixed top-0 with-vertical h-screen z-[999] flex-shrink-0 border-r-[1px] w-[270px] border-gray-400/20  bg-white left-sidebar transition-all duration-300">
      <div className="p-5">
        <Link href="/">
          <Image src="/images/logo-floresta.png" alt="Logo floresta" width={144} height={30} loading="eager" />
        </Link>
      </div>

      <div className="scroll-sidebar" data-simplebar>
        <div className="px-6 mt-8">
          <nav className="w-full flex flex-col sidebar-nav">
            <ul id="sidebarnav" className="text-dark text-sm">
              <li className="text-xs font-bold pb-4">
                <Icon icon="tabler:dots" className="nav-small-cap-icon text-lg hidden text-center" />
                  <span>HOME</span>
              </li>

              <li className="sidebar-item">
                <Link href="/" className={"sidebar-link gap-3 py-3 px-3 rounded-md w-full flex items-center hover:text-primary hover:bg-primary/15 " + isActive("/dashboard")}>
                  <Icon icon="tabler:layout-dashboard" className="text-xl" />
                  <i className="ti ti-layout-dashboard  text-xl"></i>
                  <span>Dashboard</span>
                </Link>
              </li>
            </ul>
          </nav>
        </div>
      </div>
    </aside>
  );
}

export default Sidebar;