<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use App\Services\UserActivityLogService;
use App\Services\UserAuditTrailService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    protected UserActivityLogService $activityService;
    protected UserAuditTrailService $auditService;

    public function __construct(UserActivityLogService $activityService, UserAuditTrailService $auditService)
    {
        $this->activityService = $activityService;
        $this->auditService = $auditService;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $companyId = auth()->user()->company_id;
            $users = User::where("company_id", $companyId)
                ->with(["company", "roles"])
                ->orderBy("created_at", "desc")
                ->paginate(15);

            return response()->json([
                "message" => "Users retrieved successfully",
                "data" => $users->items(),
                "pagination" => [
                    "total" => $users->total(),
                    "per_page" => $users->perPage(),
                    "current_page" => $users->currentPage(),
                    "last_page" => $users->lastPage(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $data["company_id"] = auth()->user()->company_id;
            $data["created_by"] = auth()->id();

            $user = User::create($data);

            if (!empty($data["roles"])) {
                $user->syncRoles($data["roles"]);
            }

            $this->auditService->log(auth()->user(), "user_created", User::class, $user->id, ["user_id" => $user->id], $request);

            return response()->json([
                "message" => "User created successfully",
                "data" => $user->load(["roles"]),
            ], 201);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }

    public function show(User $user): JsonResponse
    {
        try {
            if ($user->company_id !== auth()->user()->company_id) {
                return response()->json(["error" => "Unauthorized"], 403);
            }

            return response()->json([
                "message" => "User retrieved successfully",
                "data" => $user->load(["company", "roles", "preferences"]),
            ]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        try {
            if ($user->company_id !== auth()->user()->company_id) {
                return response()->json(["error" => "Unauthorized"], 403);
            }

            $oldData = $user->toArray();
            $data = $request->validated();

            $user->update($data);

            if (!empty($data["roles"])) {
                $user->syncRoles($data["roles"]);
            }

            $changes = array_diff_assoc($user->toArray(), $oldData);
            $this->auditService->log(auth()->user(), "user_updated", User::class, $user->id, $changes, $request);

            return response()->json([
                "message" => "User updated successfully",
                "data" => $user->load(["roles"]),
            ]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }

    public function destroy(User $user, Request $request): JsonResponse
    {
        try {
            if ($user->company_id !== auth()->user()->company_id) {
                return response()->json(["error" => "Unauthorized"], 403);
            }

            $user->update(["deleted_by" => auth()->id()]);
            $user->delete();

            $this->auditService->log(auth()->user(), "user_deleted", User::class, $user->id, [], $request);

            return response()->json([
                "message" => "User deleted successfully",
            ]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }

    public function toggleActive(User $user, Request $request): JsonResponse
    {
        try {
            if ($user->company_id !== auth()->user()->company_id) {
                return response()->json(["error" => "Unauthorized"], 403);
            }

            $user->update(["is_active" => !$user->is_active]);

            $this->auditService->log(auth()->user(), "user_status_toggled", User::class, $user->id, ["is_active" => $user->is_active], $request);

            return response()->json([
                "message" => "User status updated successfully",
                "data" => $user,
            ]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }

    public function getActivity(User $user): JsonResponse
    {
        try {
            if ($user->company_id !== auth()->user()->company_id) {
                return response()->json(["error" => "Unauthorized"], 403);
            }

            $activities = $this->activityService->getActivityLogs($user, 50);

            return response()->json([
                "message" => "User activity logs retrieved successfully",
                "data" => $activities,
            ]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }

    public function getAuditTrail(User $user): JsonResponse
    {
        try {
            if ($user->company_id !== auth()->user()->company_id) {
                return response()->json(["error" => "Unauthorized"], 403);
            }

            $auditTrail = $this->auditService->getAuditTrail($user, 100);

            return response()->json([
                "message" => "User audit trail retrieved successfully",
                "data" => $auditTrail,
            ]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }
}
