<?php

namespace App\Http\Controllers;


use App\Exceptions\TransactionException;
use App\Http\Requests\AmountRequest;
use App\Models\Partner;
use App\Models\Site;
use App\Models\User;
use App\Services\BtcService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class BtcController extends Controller
{
    private BtcService $btcService;

    public function __construct(BtcService $btcService)
    {
        $this->btcService = $btcService;
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function show(int $id)
    {
        try {
            $user = User::findOrFail($id);
            $partner = Partner::findOrFail(1);
            $site = Site::findOrFail(1);
            $cashback = $user->cashback;
            $data = [
                'userId' => $id,
                'userBalance' => $user->account->balance,
                'partnerBalance' => $partner->account->balance,
                'siteBalance' => $site->account->balance,
                'cashbackBalance' => $cashback->account->balance,
            ];
        } catch (ModelNotFoundException $e) {
            $modelName = str_replace('App\Models\\', '', $e->getModel());
            return response($modelName . ' не найден', 404);
        }
        return view('index', $data);
    }

    /**
     * Делаем перевод со счета пользователя на счета партнера,
     * сайта и на счет кэшбэка
     *
     * @param AmountRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function depositFromUserAccount(AmountRequest $request)
    {
        $amount = $request->input('amount');
        $userId = $request->input('id');
        try {
            $this->btcService->fromUserTransfer($userId, $amount);
        } catch (TransactionException $e) {
            return Redirect::back()->withErrors(['error' => $e->getMessage()]);
        }
        return back();
    }

    /**
     * Партнер забирает биткоины со своего счета
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function depositPartnerAccount()
    {
        $partnerId = 1;
        try {
            $this->btcService->fromPartnerTransfer($partnerId);
        } catch (TransactionException $e) {
            return Redirect::back()->withErrors(['error' => $e->getMessage()]);
        }
        return back();
    }

    /**
     * Возвращаем кэшбэк на счет пользователя
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function depositCashbackAccount(Request $request)
    {
        try {
            $userId = $request->input('id');
            $this->btcService->getCashback($userId);
        } catch (TransactionException $e) {
            return Redirect::back()->withErrors(['error' => $e->getMessage()]);
        }
        return back();
    }
}
