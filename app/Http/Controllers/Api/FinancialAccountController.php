<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Requests\Financial\StoreAccountRequest;
use App\Http\Requests\Financial\UpdateAccountRequest;
use App\Models\FinancialAccount;
use App\Services\FinancialAccountService;
use Illuminate\Http\JsonResponse;

class FinancialAccountController extends Controller {
    protected FinancialAccountService $service;
    public function __construct(FinancialAccountService $service) { $this->service = $service; }
    public function index(): JsonResponse {
        try {
            $accounts = $this->service->getAccounts(auth()->user()->company_id);
            return response()->json(["message" => "Accounts retrieved", "data" => $accounts]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }
    public function store(StoreAccountRequest $request): JsonResponse {
        try {
            $account = $this->service->createAccount(auth()->id(), auth()->user()->company_id, $request->validated());
            return response()->json(["message" => "Account created", "data" => $account], 201);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }
    public function show(FinancialAccount $account): JsonResponse {
        try {
            if ($account->company_id !== auth()->user()->company_id) { return response()->json(["error" => "Not found"], 404); }
            return response()->json(["message" => "Account retrieved", "data" => $account->load(["transactions"])]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }
    public function update(UpdateAccountRequest $request, FinancialAccount $account): JsonResponse {
        try {
            if ($account->company_id !== auth()->user()->company_id) { return response()->json(["error" => "Not found"], 404); }
            $updated = $this->service->updateAccount($account, $request->validated());
            return response()->json(["message" => "Account updated", "data" => $updated]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }
    public function destroy(FinancialAccount $account): JsonResponse {
        try {
            if ($account->company_id !== auth()->user()->company_id) { return response()->json(["error" => "Not found"], 404); }
            $this->service->deleteAccount($account, auth()->id());
            return response()->json(["message" => "Account deleted"]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }
    public function balance(FinancialAccount $account): JsonResponse {
        try {
            if ($account->company_id !== auth()->user()->company_id) { return response()->json(["error" => "Not found"], 404); }
            $balance = $this->service->getAccountBalance($account);
            return response()->json(["message" => "Balance retrieved", "data" => ["balance" => $balance]]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }
}
