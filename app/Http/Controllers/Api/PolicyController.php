<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Policy;
use App\Models\UserPolicyAcceptance;
use Illuminate\Http\Request;

class PolicyController extends Controller
{
    // Получение последней версии политики
    public function latest(Request $request)
    {
        $policy = Policy::orderBy('effective_date', 'desc')->first(); // Получаем последнюю политику по дате вступления

        if (!$policy) {
            return response()->json([
                'message' => 'Политика не найдена.',
            ], 404);
        }

        // Проверка, принял ли пользователь уже политику
        $user = $request->user();
        $userPolicyAcceptance = $user->acceptedPolicies()->where('policy_id', $policy->id)->first();

        // Если пользователь уже принял эту политику, возвращаем соответствующее сообщение
        if ($userPolicyAcceptance) {
            return response()->json([
                'message' => 'Вы уже приняли эту политику.',
                'policy' => [
                    'id' => $policy->id,
                    'effective_date' => $policy->effective_date,
                ],
                'accepted_at' => $userPolicyAcceptance->accepted_at,
            ]);
        }

        // Если есть обновление, уведомляем пользователя
        $isUpdated = $userPolicyAcceptance ? $userPolicyAcceptance->accepted_at < $policy->updated_at : true;

        return response()->json([
            'message' => $isUpdated ? 'Политика обновлена, пожалуйста, примите новую версию.' : 'Вы ещё не приняли политику.',
            'policy' => [
                'id' => $policy->id,
                'type' => $policy->type,
                'content' => $policy->content,
                'effective_date' => $policy->effective_date,
            ],
            'is_updated' => $isUpdated,
        ]);
    }

    // Принятие политики пользователем
    public function accept(Request $request)
    {
        $request->validate([
            'policy_id' => 'required|exists:policies,id',
        ]);

        $user = $request->user();
        $policy = Policy::find($request->policy_id);

        if (!$policy) {
            return response()->json([
                'message' => 'Политика не найдена.',
            ], 404);
        }

        // Проверяем, если политика актуальна (есть обновление)
        $userPolicyAcceptance = $user->acceptedPolicies()->where('policy_id', $policy->id)->first();

        if ($userPolicyAcceptance && $userPolicyAcceptance->accepted_at >= $policy->updated_at) {
            return response()->json([
                'message' => 'Вы уже приняли последнюю версию этой политики.',
                'policy' => [
                    'id' => $policy->id,
                    'effective_date' => $policy->effective_date,
                ],
                'accepted_at' => $userPolicyAcceptance->accepted_at,
            ]);
        }

        // Обновляем или создаем запись о принятии политики
        $userPolicyAcceptance = UserPolicyAcceptance::updateOrCreate(
            ['user_id' => $user->id, 'policy_id' => $request->policy_id],
            ['accepted_at' => now()]
        );

        return response()->json([
            'message' => 'Политика принята.',
        ]);
    }
}