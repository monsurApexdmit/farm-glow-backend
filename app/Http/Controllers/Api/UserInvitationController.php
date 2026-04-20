<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\SendInvitationRequest;
use App\Http\Requests\User\AcceptInvitationRequest;
use App\Models\UserInvitation;
use App\Services\UserInvitationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserInvitationController extends Controller
{
    protected UserInvitationService $invitationService;

    public function __construct(UserInvitationService $invitationService)
    {
        $this->invitationService = $invitationService;
    }

    public function index(): JsonResponse
    {
        try {
            $company = auth()->user()->company;
            $invitations = $this->invitationService->getInvitations($company);

            return response()->json([
                "message" => "Invitations retrieved successfully",
                "data" => $invitations,
            ]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }

    public function send(SendInvitationRequest $request): JsonResponse
    {
        try {
            $user = auth()->user();
            $company = $user->company;

            $invitation = $this->invitationService->sendInvitation(
                $user,
                $company,
                $request->email,
                $request->role ?? "farmer"
            );

            return response()->json([
                "message" => "Invitation sent successfully",
                "data" => $invitation,
            ], 201);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }

    public function getByToken(string $token): JsonResponse
    {
        try {
            $invitation = $this->invitationService->getInvitationByToken($token);

            if (!$invitation) {
                return response()->json(["error" => "Invitation not found"], 404);
            }

            if ($invitation->isExpired()) {
                return response()->json(["error" => "Invitation has expired"], 400);
            }

            return response()->json([
                "message" => "Invitation retrieved successfully",
                "data" => $invitation,
            ]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }

    public function accept(string $token, AcceptInvitationRequest $request): JsonResponse
    {
        try {
            $invitation = $this->invitationService->getInvitationByToken($token);

            if (!$invitation) {
                return response()->json(["error" => "Invitation not found"], 404);
            }

            $user = $this->invitationService->acceptInvitation($invitation, $request->validated());

            return response()->json([
                "message" => "Invitation accepted successfully",
                "data" => $user,
            ]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }

    public function destroy(UserInvitation $invitation, Request $request): JsonResponse
    {
        try {
            if ($invitation->company_id !== auth()->user()->company_id) {
                return response()->json(["error" => "Unauthorized"], 403);
            }

            $this->invitationService->deleteInvitation($invitation);

            return response()->json([
                "message" => "Invitation deleted successfully",
            ]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }

    public function getPending(): JsonResponse
    {
        try {
            $company = auth()->user()->company;
            $invitations = $this->invitationService->getPendingInvitations($company);

            return response()->json([
                "message" => "Pending invitations retrieved successfully",
                "data" => $invitations,
            ]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }
}
