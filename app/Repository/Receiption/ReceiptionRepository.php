<?php

namespace App\Repository\Receiption;

use App\Filter\Reservation\ReservationFilter;
use App\Models\Client;
use App\Models\Reservation;
use App\Notifications\CopmeleteReservationMessageNotification;
use App\Repository\BaseRepositoryImplementation;
use App\Statuses\ReservationStatus;
use App\Statuses\ReservationType;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;


class ReceiptionRepository extends BaseRepositoryImplementation
{

    public function getFilterItems($filter)
    {
        $records = Reservation::query();
        if ($filter instanceof ReservationFilter) {
            $records->when(isset($filter->type), function ($query) use ($filter) {
                $query->where('type', $filter->getType());
            });
            return $records->with(['client'])->paginate($filter->per_page);
        }
        return $records->with(['client'])->paginate($filter->per_page);
    }

    public function model()
    {
        return Reservation::class;
    }
    public function create_client($data)
    {
        DB::beginTransaction();
        $existsClient = Client::where('email', $data['email'])->first();
        try {
            if (!$existsClient) {
                $client = Client::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'phone' => $data['phone'],
                ]);
            }
            DB::commit();
            if ($client != null) {
                return $client;
            } else {
                return $client;
            }
        } catch (\Throwable $th) {
            DB::rollback();
            Log::error($th->getMessage());
        }
    }

    public function create_reservation($data)
    {

        DB::beginTransaction();
        try {

            $existingReservation = Reservation::where('expert_id', $data['expert_id'])
                ->where('date', $data['date'])
                ->where(function ($query) use ($data) {
                    $query->where(function ($query) use ($data) {
                        $query->where('start_time', '>=', $data['start_time'])
                            ->where('start_time', '<', $data['end_time']);
                    })
                        ->orWhere(function ($query) use ($data) {
                            $query->where('end_time', '>', $data['start_time'])
                                ->where('end_time', '<=', $data['end_time']);
                        })
                        ->orWhere(function ($query) use ($data) {
                            $query->where('start_time', '<=', $data['start_time'])
                                ->where('end_time', '>=', $data['end_time']);
                        });
                })
                ->where('type', ReservationType::UN_APPROVED)
                ->orWhere('type', ReservationType::APPROVED)
                ->first();
            if ($existingReservation) {
                DB::rollback();
                return "A reservation already exists for this date and time And This Expert";
            } else {
                $reservation = new Reservation();
                $reservation->client_id = $data['client_id'];
                $reservation->expert_id = $data['expert_id'];
                $reservation->date = $data['date'];
                $reservation->start_time = $data['start_time'];
                $reservation->end_time = $data['end_time'];
                $reservation->event = $data['event'];
                $reservation->type = ReservationType::UN_APPROVED;
                $reservation->status = ReservationStatus::PENDING;
                $reservation->save();

                $reservation->services()->attach($data['services']);
            }
            DB::commit();

            return $reservation->load(['client', 'expert']);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
        }
    }
    public function complete_reservation($data)
    {
        DB::beginTransaction();
        try {
            $reservation = $this->updateById($data['reservation_id'], $data);

            if (Arr::has($data, 'attachment')) {
                $file = Arr::get($data, 'attachment');
                $extension = $file->getClientOriginalExtension();
                $file_name = Str::uuid() . date('Y-m-d') . '.' . $extension;
                $file->move(public_path('attachments'), $file_name);
                $image_file_path = public_path('attachments/' . $file_name);
                $image_data = file_get_contents($image_file_path);
                $base64_image = base64_encode($image_data);
                $reservation->attachment = $base64_image;
            }

            if ($reservation->type = ReservationType::UN_APPROVED) {
                $reservation->type = ReservationType::APPROVED;
                $client = Client::where('id', $reservation->client_id)->first();
                $client->notify(new CopmeleteReservationMessageNotification($client));
            }
            $reservation->save();

            DB::commit();
            if ($reservation === null) {
                return response()->json(['message' => "Reservation was not Updated"]);
            }
            return $reservation->load('expert', 'client');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
            return response()->json(['message' => $e->getMessage()]);
        }
    }
}
