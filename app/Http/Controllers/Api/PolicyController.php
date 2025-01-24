<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Policy;
use App\Models\UserPolicyAcceptance;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PolicyController extends Controller
{
    // Получение последней версии политики
    public function latest(Request $request)
    {
        $policy = Policy::orderBy('effective_date', 'desc')->first(); // Получаем последнюю политику по дате вступления

        // Проверка, принял ли пользователь уже политику
        $user = $request->user();
        $userPolicyAcceptance = $user->acceptedPolicies()->where('policy_id', $policy->id)->first();

        // Если пользователь уже принял эту политику, возвращаем сообщение об этом
        if ($userPolicyAcceptance) {
            return response()->json([
                'message' => 'Вы уже приняли эту политику.',
                'policy' => $policy,
                'accepted_at' => $userPolicyAcceptance->accepted_at,
            ]);
        }

        // Если есть обновление, уведомляем пользователя
        $isUpdated = $userPolicyAcceptance ? $userPolicyAcceptance->accepted_at < $policy->updated_at : true;

        return response()->json([
            'message' => $isUpdated ? 'Политика обновлена, пожалуйста, примите новую версию.' : 'Вы ещё не приняли политику.',
            'policy' => $policy,
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

        // Проверяем, если политика актуальна (есть обновление)
        $userPolicyAcceptance = $user->acceptedPolicies()->where('policy_id', $policy->id)->first();

        if ($userPolicyAcceptance && $userPolicyAcceptance->accepted_at >= $policy->updated_at) {
            return response()->json([
                'message' => 'Вы уже приняли последнюю версию этой политики.',
            ]);
        }

        // Обновляем или создаем запись о принятии политики
        UserPolicyAcceptance::updateOrCreate(
            ['user_id' => $user->id, 'policy_id' => $request->policy_id],
            ['accepted_at' => now()]
        );

        return response()->json(['message' => 'Политика принята']);
    }
}