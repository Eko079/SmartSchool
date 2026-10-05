<?php

namespace App\Http\Controllers\Portal\Concerns;

use App\Support\Device;
use Illuminate\Http\Request;

trait RendersPortalMobile
{
    protected function portalView(Request $request, string $desktop, string $mobile, array $data = [])
    {
        if (Device::isPhone($request) && view()->exists($mobile)) {
            return view($mobile, $data);
        }

        return view($desktop, $data);
    }
}
