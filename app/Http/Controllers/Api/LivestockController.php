<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Livestock;
use App\Http\Requests\Livestock\StoreLivestockRequest;
use App\Http\Requests\Livestock\UpdateLivestockRequest;
use App\Http\Requests\Livestock\StoreLivestockHealthRequest;
use App\Services\LivestockService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LivestockController extends Controller
{
    protected LivestockService $livestockService;

    public function __construct(LivestockService $livestockService)
    {
        $this->livestockService = $livestockService;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $livestock = $this->livestockService->getLivestock(
                auth()->user()->company_id,
                $request->query("farm_id"),
                $request->query("shed_id")
            );
            return response()->json([
                "message" => "Livestock retrieved successfully",
                "data" => $livestock,
            ]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }

    public function store(StoreLivestockRequest $request): JsonResponse
    {
        try {
            $livestock = $this->livestockService->createLivestock(auth()->id(), $request->validated());
            return response()->json([
                "message" => "Livestock created successfully",
                "data" => $livestock,
            ], 201);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }

    public function show(Livestock $livestock): JsonResponse
    {
        try {
            if ($livestock->farm->company_id !== auth()->user()->company_id) {
                return response()->json(["error" => "Livestock not found"], 404);
            }
            return response()->json([
                "message" => "Livestock retrieved successfully",
                "data" => $livestock,
            ]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 403);
        }
    }

    public function update(UpdateLivestockRequest $request, Livestock $livestock): JsonResponse
    {
        try {
            if ($livestock->farm->company_id !== auth()->user()->company_id) {
                return response()->json(["error" => "Livestock not found"], 404);
            }
            $updated = $this->livestockService->updateLivestock($livestock, $request->validated());
            return response()->json([
                "message" => "Livestock updated successfully",
                "data" => $updated,
            ]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }

    public function destroy(Livestock $livestock): JsonResponse
    {
        try {
            if ($livestock->farm->company_id !== auth()->user()->company_id) {
                return response()->json(["error" => "Livestock not found"], 404);
            }
            $this->livestockService->deleteLivestock($livestock);
            return response()->json([
                "message" => "Livestock deleted successfully",
            ]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }

    public function recordHealth(StoreLivestockHealthRequest $request, Livestock $livestock): JsonResponse
    {
        try {
            if ($livestock->farm->company_id !== auth()->user()->company_id) {
                return response()->json(["error" => "Livestock not found"], 404);
            }
            $healthRecord = $this->livestockService->recordHealthStatus($livestock, $request->validated());
            return response()->json([
                "message" => "Health record created successfully",
                "data" => $healthRecord,
            ], 201);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }

    public function getHealth(Livestock $livestock): JsonResponse
    {
        try {
            if ($livestock->farm->company_id !== auth()->user()->company_id) {
                return response()->json(["error" => "Livestock not found"], 404);
            }
            $healthHistory = $this->livestockService->getHealthHistory($livestock);
            return response()->json([
                "message" => "Health history retrieved successfully",
                "data" => $healthHistory,
            ]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }

    public function inventorySummary(Request $request): JsonResponse
    {
        try {
            $companyId = auth()->user()->company_id;
            $farmId = $request->query('farm_id');

            $query = \App\Models\Livestock::query()
                ->active()
                ->with(['livestockType', 'healthRecords' => function($q) {
                    $q->latest()->limit(1);
                }])
                ->whereHas('farm', function($q) use ($companyId) {
                    $q->where('company_id', $companyId);
                });

            if ($farmId) {
                $query->where('farm_id', $farmId);
            }

            $all = $query->get();

            // Group by livestock type
            $groups = [];
            foreach ($all as $animal) {
                $type = $animal->livestockType;
                if (!$type) continue;
                $key = $type->id;
                if (!isset($groups[$key])) {
                    $groups[$key] = [
                        'type_id'        => $type->id,
                        'type'           => $type->code,
                        'label'          => $type->name,
                        'icon'           => $type->icon,
                        'healthyCount'   => 0,
                        'sickCount'      => 0,
                        'treatmentCount' => 0,
                        'quarantineCount'=> 0,
                        'capacityUsed'   => 0,
                        'totalCapacity'  => 0,
                        'weights'        => [],
                        'ageMonths'      => [],
                        'animals'        => [],
                    ];
                }

                $latestHealth = $animal->healthRecords->first();
                $hs = $latestHealth ? $latestHealth->health_status : 'healthy';

                switch ($hs) {
                    case 'sick':       $groups[$key]['sickCount']++;       break;
                    case 'treatment':  $groups[$key]['treatmentCount']++;  break;
                    case 'quarantine': $groups[$key]['quarantineCount']++; break;
                    default:           $groups[$key]['healthyCount']++;    break;
                }

                $groups[$key]['capacityUsed']++;
                if ($animal->weight) $groups[$key]['weights'][] = (float)$animal->weight;
                if ($animal->date_of_birth) $groups[$key]['ageMonths'][] = $animal->date_of_birth->diffInMonths(now());

                $groups[$key]['animals'][] = [
                    'id'             => $animal->id,
                    'tag_number'     => $animal->tag_number,
                    'name'           => $animal->name ?? $animal->tag_number,
                    'breed'          => $animal->breed,
                    'gender'         => $animal->gender,
                    'health_status'  => $hs,
                    'weight'         => $animal->weight,
                    'weight_unit'    => $animal->weight_unit,
                    'date_of_birth'  => $animal->date_of_birth?->toDateString(),
                    'age_months'     => $animal->date_of_birth ? $animal->date_of_birth->diffInMonths(now()) : null,
                    'last_checkup'   => $latestHealth ? $latestHealth->created_at->diffForHumans() : 'No records',
                    'notes'          => $latestHealth?->observations ?? '',
                    'shed_id'        => $animal->shed_id,
                ];
            }

            // Total shed capacity across company
            $totalShedCapacity = \App\Models\LivestockShed::whereHas('farm', function($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })->sum('capacity') ?: 1000;

            $result = [];
            foreach ($groups as $key => $g) {
                $avgAge = count($g['ageMonths']) ? round(array_sum($g['ageMonths']) / count($g['ageMonths']), 1) : 0;
                $avgWeight = count($g['weights']) ? round(array_sum($g['weights']) / count($g['weights']), 1) : 0;
                $totalCapacity = $shedCapacities[$key] ?? max($g['capacityUsed'] + 20, 100);

                $result[] = [
                    'type'           => $g['type'],
                    'label'          => $g['label'],
                    'icon'           => $g['icon'],
                    'healthyCount'   => $g['healthyCount'],
                    'sickCount'      => $g['sickCount'],
                    'treatmentCount' => $g['treatmentCount'],
                    'quarantineCount'=> $g['quarantineCount'],
                    'capacityUsed'   => $g['capacityUsed'],
                    'totalCapacity'  => (int)$totalCapacity,
                    'avgAge'         => $avgAge > 0 ? round($avgAge / 12, 1) . ' years' : 'Unknown',
                    'avgWeight'      => $avgWeight > 0 ? $avgWeight . ' kg' : 'Unknown',
                    'productionRate' => $this->getProductionRate($g['type'], $g['healthyCount']),
                    'animals'        => $g['animals'],
                ];
            }

            return response()->json(['message' => 'Inventory summary retrieved successfully', 'data' => $result]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    private function getProductionRate(string $type, int $healthyCount): string
    {
        return match($type) {
            'cattle'  => round($healthyCount * 14, 0) . 'L/day',
            'sheep'   => round($healthyCount * 0.25, 1) . ' kg wool/week',
            'chicken' => round($healthyCount * 0.85, 0) . ' eggs/day',
            'pig'     => round($healthyCount * 0.8, 1) . ' kg gain/day',
            'goat'    => round($healthyCount * 2.5, 1) . 'L/day',
            'duck'    => round($healthyCount * 0.7, 0) . ' eggs/day',
            default   => 'N/A',
        };
    }

}
