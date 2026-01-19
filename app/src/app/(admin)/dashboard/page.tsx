"use client"

import OpenVsClosedCard from "@/shared/components/dashboard/OpenVsClosedCard";

export default function Page()
{
  return (
    <div className="card grid grid-cols-12 gap-6 p-8">
      <div className="lg:col-span-8 md:col-span-12 sm:col-span-12 col-span-12">
        <OpenVsClosedCard companyId={1} />
      </div>

      <div className="lg:col-span-4 md:col-span-12 sm:col-span-12 col-span-12">
        <div className="card h-full">outro card</div>
      </div>
      
    </div>
    
      

  );
}