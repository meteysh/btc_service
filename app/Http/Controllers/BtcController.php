<?php

namespace App\Http\Controllers;


use App\Http\Requests\BalanceRequest;
use App\Models\Account;
use App\Models\Cashback;
use App\Models\Page;
use App\Models\Partner;
use App\Models\Site;
use App\Models\Transaction;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BtcController extends Controller
{
    const COMISSION_PERCENT = 0.01;
    const PARTNER_PERCENT = 0.05;
    const CASHBACK_PERCENT = 0.10;

    public function show(int $id)
    {
        $amount = 100;
        DB::beginTransaction();
        try {
            $partnerId = 1;
            $partner = Partner::find($partnerId);
            $partnerAccount = $partner->account;
            $partnerAccountId = $partnerAccount->id;

            $site = Site::find(1);
            $siteAccount = $site->account;
            $siteAccountId = $siteAccount->id;

            $amountOut = $amount * (1 - self::COMISSION_PERCENT);
            $amountSite = $amount * self::COMISSION_PERCENT;

            $user = User::find($id);
            $userAccount = $user->account;
            $userAccountId = $userAccount->id;

            $userAccount->decrement('balance', $amountOut);
            //->increment() нет т.к. просто показательно списываем куда-то
            $this->saveTransactionHistory($amountOut, $userAccountId);

            $userAccount->decrement('balance', $amountSite);
            $siteAccount->increment('balance', $amountSite);
            $this->saveTransactionHistory($amountSite, $userAccountId, $siteAccountId);

            $cashback = $user->cashback;
            $cashbackAccount = $cashback->account;
            $cashbackAccountId = $cashbackAccount->id;

            $amountPartner = $amountSite * self::PARTNER_PERCENT;
            $amountCashback = $amountSite * self::CASHBACK_PERCENT;

            $siteAccount->decrement('balance', $amountPartner);
            $partnerAccount->increment('balance', $amountPartner);
            $this->saveTransactionHistory($amountPartner, $siteAccountId, $partnerAccountId);

            $siteAccount->decrement('balance', $amountCashback);
            $cashbackAccount->increment('balance', $amountCashback);
            $this->saveTransactionHistory($amountCashback, $siteAccountId, $cashbackAccountId);

            DB::commit();
        }catch(Exception $e){
            DB::rollBack();
            echo $e->getMessage();
        }
        echo 555;
        //$user->save();
        //dd($user);
//        $user = Cashback::find(1);
//
//        $account = new Account();
//        $account->balance = 1100;
//
//        //$user->accounts()->save($account);
//
//        //dd($user);
        try {
//            $user = User::find($id);
//            $userBalance = $user->userBalance();
//            $userBalance->decrement('cashback_balance', 0.0);
//            $userBalance->decrement('balance', 0.0);

        } catch (ModelNotFoundException $e) {
            return response('Пользователь не найден', 404);
        }

        $data = [
            'userId' => $id,
            'userBalance' => 111,
            'partnerBalance' => 222,
            'siteBalance' => 333,
            'cashbackBalance' => 444,
        ];
        return view('index', $data);
    }

    public function depositFromUserAccount(BalanceRequest $request)
    {

        $balance = $request->input('balance');
        $userId = $request->input('id');
        dd($balance, $userId);
        $comission = $balance * self::COMISSION_PERCENT;

        $userBalance = 11;
        $cashbackBalance = $comission * self::CASHBACK_PERCENT;
        $siteBalance = $comission;// зачислить
        $partnerBalance = $comission * self::PARTNER_PERCENT;

        $data = [
            'userBalance' => $balance,
            'partnerBalance' => 222,
            'siteBalance' => 333,
            'cashbackBalance' => 444,
        ];
        return view('index', $data);
    }

    public function depositPartnerAccount(Request $request)
    {
        echo 5555;
    }

    public function depositCashbackAccount(Request $request)
    {
        echo 9999;
    }

    private function saveTransactionHistory($amount, $fromId, $toId = null)
    {
        $transaction = new Transaction();
        $transaction->from_id = $fromId;
        $transaction->to_id = $toId;
        $transaction->amount = $amount;
        $transaction->save();
    }

    private function transferBtc($from, $to, $amount)
    {
        $fromAccount = $from->account;
        $toAccount = $to->account;
        $fromAccount->decrement('balance', $amount);
        $toAccount->increment('balance', $amount);
        $this->saveTransactionHistory($amount, $fromAccount->id, $toAccount->id);
    }
}
