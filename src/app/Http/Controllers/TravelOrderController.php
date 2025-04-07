<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTravelOrderRequest;
use App\Http\Requests\UpdateTravelOrderRequest;
use App\Http\Resources\TravelOrderResource;
use App\Models\TravelOrder;
use App\Notifications\OrderStatusUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TravelOrderController extends Controller
{

    public function index(Request $request)
    {
        $query = $request->user()->travelOrders()
            ->statusFilter($request->status)
            ->destinationFilter($request->destination)
            ->dateRangeFilter($request->start_date, $request->end_date);

        return TravelOrderResource::collection($query->get());
    }

    public function store(StoreTravelOrderRequest $request)
    {
        try {
            $order = TravelOrder::create(
                $request->validated() + [
                    'user_id' => auth()->id(),
                    'status'  => 'solicitado'
                ]
            );

            return response()->json($order, 201);

        } catch (\Exception $e) {
            return response()->json([
                'error'   => 'Erro ao criar pedido',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function updateStatus(UpdateTravelOrderRequest $request, TravelOrder $travel_order)
    {
        if (!Gate::allows('updateStatus', $travel_order)) {
            abort(403, 'Ação não autorizada');
        }

        $travel_order->update($request->validated());
        $travel_order->user->notify(new OrderStatusUpdated($travel_order));

        return new TravelOrderResource($travel_order);
    }


    public function show(TravelOrder $travel_order)
    {
        if (!Gate::allows('view', $travel_order)) {
            return response()->json([
                'message' => 'Você não tem permissão para visualizar este pedido'
            ], 403);
        }

        return response()->json([
            'data' => $travel_order
        ]);
    }

    public function cancel(Request $request, TravelOrder $travel_order)
    {
        $this->authorize('cancel', $travel_order);

        if ($travel_order->status !== 'aprovado') {
            return response()->json(['error' => 'Só é possível cancelar pedidos aprovados'], 400);
        }

        $travel_order->update(['status' => 'cancelado']);
        $travel_order->user->notify(new OrderStatusUpdated($travel_order));

        return new TravelOrderResource($travel_order);
    }
}
