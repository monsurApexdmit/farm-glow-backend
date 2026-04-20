<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateProfileRequest;
use App\Http\Requests\User\UpdatePreferencesRequest;
use App\Http\Requests\User\ChangePasswordRequest;
use App\Services\UserPreferenceService;
use App\Services\UserActivityLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class UserProfileController extends Controller
{
    protected UserPreferenceService $preferenceService;
    protected UserActivityLogService $activityService;

    public function __construct(UserPreferenceService $preferenceService, UserActivityLogService $activityService)
    {
        $this->preferenceService = $preferenceService;
        $this->activityService = $activityService;
    }

    public function me(): JsonResponse
    {
        try {
            $user = auth()->user();
            return response()->json([
                "message" => "Current user retrieved successfully",
                "data" => $user->load(["company", "preferences"]),
            ]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }

    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        try {
            $user = auth()->user();
            $user->update($request->validated());

            $this->activityService->logActivity($user, "profile_updated", "User profile updated", null, $request);

            return response()->json([
                "message" => "Profile updated successfully",
                "data" => $user,
            ]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }

    public function getPreferences(): JsonResponse
    {
        try {
            $user = auth()->user();
            $preferences = $this->preferenceService->getPreferences($user);

            return response()->json([
                "message" => "Preferences retrieved successfully",
                "data" => $preferences,
            ]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }

    public function updatePreferences(UpdatePreferencesRequest $request): JsonResponse
    {
        try {
            $user = auth()->user();
            $preferences = $this->preferenceService->updatePreferences($user, $request->validated());

            $this->activityService->logActivity($user, "preferences_updated", "User preferences updated", null, $request);

            return response()->json([
                "message" => "Preferences updated successfully",
                "data" => $preferences,
            ]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }

    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        try {
            $user = auth()->user();

            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json(["error" => "Current password is incorrect"], 400);
            }

            $user->update(["password" => bcrypt($request->password)]);

            $this->activityService->logActivity($user, "password_changed", "User password changed", null, $request);

            return response()->json([
                "message" => "Password changed successfully",
            ]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }

    public function getActivity(): JsonResponse
    {
        try {
            $user = auth()->user();
            $activities = $this->activityService->getActivityLogs($user, 50);

            return response()->json([
                "message" => "Activity logs retrieved successfully",
                "data" => $activities,
            ]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }
}
