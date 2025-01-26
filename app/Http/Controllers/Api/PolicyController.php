<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Policy;
use App\Models\UserPolicyAcceptance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PolicyController extends Controller
{
    // Получение последних политик с учетом проверки типов
    public function latest(Request $request)
    {
        // Получаем последние политики для каждого типа
        $latestPolicies = Policy::select('type', DB::raw('MAX(effective_date) as max_date'))
            ->groupBy('type')
            ->orderBy('max_date', 'desc')
            ->get();

        if ($latestPolicies->isEmpty()) {
            return response()->json([
                'message' => 'Политики не найдены.',
            ], 404);
        }

        $user = $request->user();

        // Проверяем, какие политики пользователь уже принял
        $unacceptedPolicies = [];
        foreach ($latestPolicies as $policy) {
            $policyDetails = Policy::where('type', $policy->type)
                ->where('effective_date', $policy->max_date)
                ->first();

            $userPolicyAcceptance = $user->acceptedPolicies()
                ->where('policy_id', $policyDetails->id)
                ->first();

            if (!$userPolicyAcceptance) {
                $unacceptedPolicies[] = [
                    'id' => $policyDetails->id,
                    'type' => $policyDetails->type,
                    'content' => $policyDetails->content,
                    'effective_date' => $policyDetails->effective_date,
                ];
            }
        }

        if (empty($unacceptedPolicies)) {
            return response()->json([
                'message' => 'Вы приняли все последние версии политик.',
            ]);
        }

        return response()->json([
            'message' => 'Есть новые политики, которые требуют принятия.',
            'policies' => $unacceptedPolicies,
        ]);
    }

    // Получение всех последних политик с разными типами
    public function getAllLatest()
    {
        $latestPolicies = Policy::select('type', DB::raw('MAX(effective_date) as max_date'))
            ->groupBy('type')
            ->orderBy('max_date', 'desc')
            ->get();

        if ($latestPolicies->isEmpty()) {
            return response()->json([
                'message' => 'Политики не найдены.',
            ], 404);
        }

        $policies = [];
        foreach ($latestPolicies as $policy) {
            $policyDetails = Policy::where('type', $policy->type)
                ->where('effective_date', $policy->max_date)
                ->first();

            $policies[] = [
                'id' => $policyDetails->id,
                'type' => $policyDetails->type,
                'content' => $policyDetails->content,
                'effective_date' => $policyDetails->effective_date,
            ];
        }

        return response()->json([
            'message' => 'Пожалуйста примите политики для регистрации.',
            'policies' => $policies,
        ]);
    }


    // Принятие политики пользователем (одной или двух)
    public function accept(Request $request)
    {
        $request->validate([
            'policy_ids' => 'required|array|min:1|max:2', // Обязательное поле, массив из 1-2 ID
            'policy_ids.*' => 'required|exists:policies,id', // Каждый ID должен существовать в таблице `policies`
        ]);

        $user = $request->user();
        $acceptedPolicies = [];

        foreach ($request->policy_ids as $policyId) {
            $policy = Policy::find($policyId);

            if (!$policy) {
                return response()->json([
                    'message' => "Политика с ID {$policyId} не найдена.",
                ], 404);
            }

            // Проверяем, если политика уже была принята
            $userPolicyAcceptance = $user->acceptedPolicies()->where('policy_id', $policyId)->first();

            if ($userPolicyAcceptance && $userPolicyAcceptance->accepted_at >= $policy->updated_at) {
                $acceptedPolicies[] = [
                    'id' => $policy->id,
                    'type' => $policy->type,
                    'effective_date' => $policy->effective_date,
                    'status' => 'Уже принята',
                    'accepted_at' => $userPolicyAcceptance->accepted_at,
                ];
                continue;
            }

            // Создаём или обновляем запись о принятии политики
            $userPolicyAcceptance = UserPolicyAcceptance::updateOrCreate(
                ['user_id' => $user->id, 'policy_id' => $policyId],
                ['accepted_at' => now()]
            );

            $acceptedPolicies[] = [
                'id' => $policy->id,
                'type' => $policy->type,
                'effective_date' => $policy->effective_date,
                'status' => 'Принята',
                'accepted_at' => $userPolicyAcceptance->accepted_at,
            ];
        }

        return response()->json([
            'message' => 'Политики обработаны.',
            'policies' => $acceptedPolicies,
        ]);
    }



}