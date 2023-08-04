<?php

namespace App\Services;

use App\Exceptions\AffiliateCreateException;
use App\Mail\AffiliateCreated;
use App\Models\Affiliate;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class AffiliateService
{
    public function __construct(
        protected ApiService $apiService
    ) {}

    /**
     * Create a new affiliate for the merchant with the given commission rate.
     *
     * @param  Merchant $merchant
     * @param  string $email
     * @param  string $name
     * @param  float $commissionRate
     * @return Affiliate
     */
    public function register(Merchant $merchant, string $email, string $name, float $commissionRate): Affiliate
    {
        if($merchant->user->email == $email):
            Throw new AffiliateCreateException();
        else:
            Throw new AffiliateCreateException();
        endif;
        $affiliate = Affiliate::create([
            'user_id'=> $merchant->user_id,
            'merchant_id' => $merchant->id,
            'commission_rate' => $commissionRate,
            'discount_code' => 0.3
        ]);
        Mail::to($email)->send(new \App\Mail\AffiliateCreated($affiliate));
        return $affiliate;
        // TODO: Complete this method
    }
}
