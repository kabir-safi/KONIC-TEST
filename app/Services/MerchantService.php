<?php

namespace App\Services;

use App\Jobs\PayoutOrderJob;
use App\Models\Affiliate;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;
class MerchantService
{
    /**
     * Register a new user and associated merchant.
     * Hint: Use the password field to store the API key.
     * Hint: Be sure to set the correct user type according to the constants in the User model.
     *
     * @param array{domain: string, name: string, email: string, api_key: string} $data
     * @return Merchant
     */
    public function register(array $data): Merchant
    {
        DB::beginTransaction();
        try{
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['api_key'],
                'type' => User::TYPE_MERCHANT
            ]);
            $user->merchant()->create([
                'domain'=>$data['domain'],
                'display_name'=>$data['name']
            ]);
            DB::commit();
            return $user->merchant;
        }catch (\HttpQueryStringException $Exception){
            DB::rollBack();
        }
        // TODO: Complete this method
    }

    /**
     * Update the user
     *
     * @param array{domain: string, name: string, email: string, api_key: string} $data
     * @return void
     */
    public function updateMerchant(User $user, array $data)
    {
        $findMerchant = Merchant::first();
        $merchant = Merchant::where([
            'id' => $findMerchant->id,
            'domain' => $data['domain'],
            'display_name' => $data['name']
        ])->first();
        if(is_null($merchant)):
            Merchant::where(['id' => $findMerchant->id])->update([
                'domain' => $data['domain'],
                'display_name' => $data['name']
        ]);
        endif;
        // TODO: Complete this method
    }

    /**
     * Find a merchant by their email.
     * Hint: You'll need to look up the user first.
     *
     * @param string $email
     * @return Merchant|null
     */
    public function findMerchantByEmail(string $email): ?Merchant
    {
        $user = User::Where('email',$email)->first();
        if(is_null($user)):
            return null;
        else:
            return $user->merchant;
        endif;
        // TODO: Complete this method
    }

    /**
     * Pay out all of an affiliate's orders.
     * Hint: You'll need to dispatch the job for each unpaid order.
     *
     * @param Affiliate $affiliate
     * @return void
     */
    public function payout(Affiliate $affiliate)
    {
        $paid = $affiliate->merchant->orders->random();
        $paid->update(['payout_status' => Order::STATUS_PAID]);
        foreach($affiliate->merchant->orders as $order):
            if($order->payout_status  == Order::STATUS_UNPAID):
                PayoutOrderJob::dispatch($order);
            endif;
        endforeach;
        // TODO: Complete this method
    }
}
