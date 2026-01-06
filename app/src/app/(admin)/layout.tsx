
import Header from "@/shared/components/layout/Headers/Header";
import Sidebar from "@/shared/components/layout/Sidebars/Sidebar";
import { Fragment, ReactNode } from "react";

export default function AdminLayout({ children }: {children: ReactNode})
{
  return (
    <Fragment>
      <Sidebar />
      <div className="w-full page-wrapper overflow-hidden">
        <Header />
        {children}
      </div>
    </Fragment>
  );
}