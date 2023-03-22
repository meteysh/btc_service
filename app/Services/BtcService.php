<?php

namespace App\Services;

use App\Exceptions\TransactionException;
use App\Models\AccountInterface;
use App\Models\Transaction;
use App\Repositories\CashbackRepository;
use App\Repositories\PartnerRepository;
use App\Repositories\SiteRepository;
use App\Repositories\UserRepository;
use Exception;
use Illuminate\Support\Facades\DB;

class BtcService
{
    const COMISSION_PERCENT = 0.01;
    const PARTNER_PERCENT = 0.05;
    const CASHBACK_PERCENT = 0.10;

    private CashbackRepository $cashbackRepository;
    private PartnerRepository $partnerRepository;
    private SiteRepository $siteRepository;
    private UserRepository $userRepository;

    public function __construct(
        CashbackRepository $cashbackRepository,
        PartnerRepository  $partnerRepository,
        SiteRepository     $siteRepository,
        UserRepository     $userRepository
    )
    {
        $this->cashbackRepository = $cashbackRepository;
        $this->partnerRepository = $partnerRepository;
        $this->siteRepository = $siteRepository;
        $this->userRepository = $userRepository;
    }

    public function fromUserTransfer(int $userId, float $amount)
    {
        DB::beginTransaction();
        try {
            $user = $this->userRepository->getUserById($userId);
            $partner = $this->partnerRepository->getPartnerById(1);
            $site = $this->siteRepository->getSiteById(1);
            $cashback = $this->cashbackRepository->getCashbackById(1);

            $amountOut = $amount * (1 - self::COMISSION_PERCENT);
            $amountSite = $amount * self::COMISSION_PERCENT;
            $amountPartner = $amountSite * self::PARTNER_PERCENT;
            $amountCashback = $amountSite * self::CASHBACK_PERCENT;

            $this->transferBtc($amountOut, $user);
            $this->transferBtc($amountSite, $user, $site);
            $this->transferBtc($amountPartner, $site, $partner);
            $this->transferBtc($amountCashback, $site, $cashback);
            DB::commit();
        } catch (Exception) {
            DB::rollBack();
            throw new TransactionException('Транзакция не удалась');
        }
    }

    public function fromPartnerTransfer(int $partnerId)
    {
        DB::beginTransaction();
        try {
            $partner = $this->partnerRepository->getPartnerById($partnerId);
            $amountOut = $partner->account->balance;
            $this->transferBtc($amountOut, $partner);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw new TransactionException('Транзакция не удалась');
        }
    }

    public function getCashback(int $userId)
    {
        DB::beginTransaction();
        try {
            $user = $this->userRepository->getUserById($userId);
            $cashback = $this->cashbackRepository->getCashbackById($userId);
            $amount = $cashback->account->balance;
            $this->transferBtc($amount, $cashback, $user);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw new TransactionException('Транзакция не удалась');
        }
    }

    private function saveTransactionHistory($amount, $fromId, $toId = null)
    {
        $transaction = new Transaction();
        $transaction->from_id = $fromId;
        $transaction->to_id = $toId;
        $transaction->amount = $amount;
        $transaction->save();
    }

    private function transferBtc($amount, AccountInterface $from, AccountInterface $to = null)
    {
        $fromAccount = $from->account;
        $fromAccountId = $fromAccount->id;
        $fromAccount->decrement('balance', $amount);
        $toAccount = $to?->account;
        $toAccountId = $toAccount?->id;
        $toAccount?->increment('balance', $amount);
        $this->saveTransactionHistory($amount, $fromAccountId, $toAccountId);
    }
}
