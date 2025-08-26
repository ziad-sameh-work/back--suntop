<?php

namespace App\Modules\Merchants\Services;

use App\Modules\Core\BaseService;
use App\Modules\Merchants\Models\Merchant;

class MerchantService extends BaseService
{
    public function __construct(Merchant $merchant)
    {
        $this->model = $merchant;
    }

    /**
     * Get active merchants
     */
    public function getActiveMerchants()
    {
        return $this->model->where('is_active', true)->get();
    }
}
