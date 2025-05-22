<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

class AgentStatsController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/agent/property-stats",
     *     summary="Get property statistics for the authenticated agent",
     *     tags={"Agents"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="agentName", type="string"),
     *             @OA\Property(property="soldCount", type="integer"),
     *             @OA\Property(property="rentedCount", type="integer"),
     *             @OA\Property(property="totalCount", type="integer"),
     *             @OA\Property(property="availableCount", type="integer")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Access denied. You are not an agent.")
     * )
     */
    public function getPropertyStats(Request $request): JsonResponse
    {
        // 1. الحصول على معرف المستخدم المتصل
        $userId = Auth::id();

        if (!$userId) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        // 2. جلب المستخدم مع التحقق منه
        $user = User::find($userId);

        if (!$user) {
            return response()->json(['error' => 'User not found.'], 404);
        }

        // 3. التأكد من أن المستخدم هو نفسه (في حال تم تمرير ID مختلف)
        if ($request->route('id') && $user->id != $request->route('id')) {
            return response()->json(['error' => 'Unauthorized access.'], 403);
        }


        // 5. حساب إحصائيات العقارات
        $total = DB::table('properties')->where('user_id', $user->id)->count();
        $sold = DB::table('properties')
            ->where('user_id', $user->id)
            ->whereNotNull('purchase_id')
            ->count();
        $available = DB::table('properties')
            ->where('user_id', $user->id)
            ->where('available', 1)
            ->count();
        $rented = $total - $sold - $available;

        return response()->json([
            'agentName' => "{$user->first_name} {$user->last_name}",
            'soldCount' => (int)$sold,
            'rentedCount' => (int)$rented,
            'totalCount' => (int)$total,
            'availableCount' => (int)$available,
        ]);
    }
}
