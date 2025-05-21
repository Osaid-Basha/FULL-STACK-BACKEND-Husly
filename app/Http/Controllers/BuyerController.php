<?php

namespace App\Http\Controllers;

use App\Models\User;

class BuyerController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/agents",
     *     summary="Get all agents",
     *     tags={"Agents"},
     *     @OA\Response(
     *         response=200,
     *         description="A list of agents",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="first_name", type="string"),
     *                 @OA\Property(property="last_name", type="string"),
     *                 @OA\Property(property="email", type="string")
     *             )
     *         )
     *     )
     * )
     */
    public function getAllAgents()
    {
        $agents = User::whereHas('role', function ($query) {
            $query->where('type', 'agent');
        })->get();

        return response()->json($agents);
    }
    /**
     * @OA\Get(
     *     path="/api/agents/search/{keyword}",
     *     summary="Search agents by name",
     *     tags={"Agents"},
     *     @OA\Parameter(
     *         name="keyword",
     *         in="path",
     *         required=true,
     *         description="Keyword to search for agents by name",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Matching agents"
     *     )
     * )
     */
    public function searchAgents($keyword)
    {
        $agents = User::whereHas('role', function ($query) {
            $query->where('type', 'agent');
        })->where(function ($q) use ($keyword) {
            $q->where('first_name', 'like', "%$keyword%")
                ->orWhere('last_name', 'like', "%$keyword%");
        })->get();

        return response()->json($agents);
    }
    /**
     * @OA\Get(
     *     path="/api/agents/{id}",
     *     summary="Get agent details by ID",
     *     tags={"Agents"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the agent",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Agent details"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Agent not found"
     *     )
     * )
     */
    public function getAgentById($id)
    {
        $agent = User::whereHas('role', function ($query) {
            $query->where('type', 'agent');
        })->where('id', $id)->first();

        if (!$agent) {
            return response()->json(['message' => 'Agent not found'], 404);
        }

        return response()->json($agent);

    }
}
