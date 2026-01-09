"use client"

import { Icon } from "@iconify/react";

const Header = () => {
  return (
    <header className="full-container w-full text-sm py-3 px-5 bg-white lg:sticky z-30">
      <nav className="w-full flex items-center justify-between">
        <ul className="icon-nav flex items-center gap-4">
          <li className="relative x1:hidden">
            <a href="javascript:void(0)" className="text-xl icon-hover cursor-pointer text-heading" id="headerCollapse" data-hs-overlay="#application-sidebar-brand" aria-controls="application-sidebar-brand" aria-label="Toggle navigation">
              <Icon icon="tabler:menu-2" className="relative z-1" />
            </a>
          </li>

          <li className="relative">
            <div className="hs-dropdown relative inline-flex [--placement:bottom-left] sm:[--trigger:hover]">
              <a href="#" className="relative hs-dropdown-toggle inline-flex icon-hover text-dark">
                <Icon icon="tabler:bell-ringing" className="text-x1 relative z-[1]" />
                <div className="absolute inline-flex items-center justify-center text-white text-[11px] font-medium bg-primary w-2 h-2 rounded-full -top-[1px] -right-[6px]"></div>
              </a>

              <div className="hs-dropdown-menu transition-[opacity,margin] rounded-md duration hs-dropdown-open:opacity-100 opacity-0 mt-2 min-w-max w-[300px] hidden" aria-labelledby="hs-dropdown-custom-icon-trigger">
                <div>
                  <h3 className="text-dark font-semibold text-base px-6 py-3">Notificações</h3>
                  <ul className="list-none flex flex-col">
                    <li>
                      <a href="#" className="py-3 px-6 block hover:bg-primary/15">
                        <p className="text-sm text-dark font-semibold">Novo chamado aberto</p>
                        <p className="text-xs text-gray-500 font-medium">R14253</p>
                      </a>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </li>
        </ul>
      </nav>
    </header>
  );
}

export default Header;