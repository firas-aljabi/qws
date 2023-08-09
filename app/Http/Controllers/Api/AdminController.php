<?php

namespace App\Http\Controllers\Api;

use App\ApiHelper\ApiResponseHelper;
use App\ApiHelper\Result;
use App\Http\Controllers\Controller;
use App\Http\Requests\Expert\CreateExpertRequest;
use App\Http\Requests\Expert\GetExpertsRequest;
use App\Http\Requests\Holiday\CreateHolidayRequest;
use App\Http\Requests\Service\CreateServiceRequest;
use App\Http\Requests\Service\GetServicesRequest;
use App\Http\Requests\Transfer\CreateTransferRequest;
use App\Http\Requests\Transfer\GetTransfersRequest;
use App\Http\Requests\Transfer\UpdateransferRequest;
use App\Http\Requests\Users\ChangePermissionRequest;
use App\Http\Requests\Users\CreateUserRequest;
use App\Http\Requests\Users\GetReceiptionsRequest;
use App\Http\Resources\Expert\ExpertResource;
use App\Http\Resources\Holiday\HolidayResource;
use App\Http\Resources\PaginationResource;
use App\Http\Resources\Service\ServiceResource;
use App\Http\Resources\Transfer\TransferResource;
use App\Http\Resources\Users\UserResource;
use App\Models\Transfer;
use App\Services\Admin\AdminService;


class AdminController extends Controller
{
    public function __construct(private AdminService $adminService)
    {
    }
    public function create_admin(CreateUserRequest $request)
    {
        $createdData = $this->adminService->create_admin($request->validated());

        $returnData = UserResource::make($createdData);
        return ApiResponseHelper::sendResponse(
            new Result($returnData, "Done")
        );
    }
    public function create_receiption(CreateUserRequest $request)
    {
        $createdData = $this->adminService->create_receiption($request->validated());

        $returnData = UserResource::make($createdData);
        return ApiResponseHelper::sendResponse(
            new Result($returnData, "Done")
        );
    }

    public function create_transfer(CreateTransferRequest $request)
    {
        $createdData = $this->adminService->create_transfer($request->validated());

        $returnData = TransferResource::make($createdData);
        return ApiResponseHelper::sendResponse(
            new Result($returnData, "Done")
        );
    }
    public function show_transfer($id)
    {
        $employeeData = $this->adminService->show_transfer($id);
        $returnData = TransferResource::make($employeeData);
        return ApiResponseHelper::sendResponse(
            new Result($returnData,  "DONE")
        );
    }

    public function update_transfer(UpdateransferRequest $request)
    {
        $createdData = $this->adminService->update_transfer($request->validated());

        $returnData = TransferResource::make($createdData);
        return ApiResponseHelper::sendResponse(
            new Result($returnData, "Done")
        );
    }


    public function list_of_transfers(GetTransfersRequest $request)
    {
        $data = $this->adminService->list_of_transfers($request->generateFilter());
        $returnData = TransferResource::collection($data);
        $pagination = PaginationResource::make($data);
        return ApiResponseHelper::sendResponseWithPagination(
            new Result($returnData, $pagination, "DONE")
        );
    }

    public function export()
    {
        return $this->adminService->export();
    }

    public function create_service(CreateServiceRequest $request)
    {
        $createdData = $this->adminService->create_service($request->validated());

        $returnData = ServiceResource::make($createdData);
        return ApiResponseHelper::sendResponse(
            new Result($returnData, "Done")
        );
    }

    public function create_expert(CreateExpertRequest $request)
    {
        $createdData = $this->adminService->create_expert($request->validated());
        $returnData = ExpertResource::make($createdData);
        return ApiResponseHelper::sendResponse(
            new Result($returnData, "Done")
        );
    }

    public function list_of_experts(GetExpertsRequest $request)
    {
        $data = $this->adminService->list_of_experts($request->generateFilter());
        $returnData = ExpertResource::collection($data);
        $pagination = PaginationResource::make($data);
        return ApiResponseHelper::sendResponseWithPagination(
            new Result($returnData, $pagination, "DONE")
        );
    }

    public function list_of_services(GetServicesRequest $request)
    {
        $data = $this->adminService->list_of_services($request->generateFilter());
        $returnData = ServiceResource::collection($data);
        $pagination = PaginationResource::make($data);
        return ApiResponseHelper::sendResponseWithPagination(
            new Result($returnData, $pagination, "DONE")
        );
    }

    public function list_of_receiptions(GetReceiptionsRequest $request)
    {
        $data = $this->adminService->list_of_receiptions($request->generateFilter());
        $returnData = UserResource::collection($data);
        $pagination = PaginationResource::make($data);
        return ApiResponseHelper::sendResponseWithPagination(
            new Result($returnData, $pagination, "DONE")
        );
    }

    public function create_holiday(CreateHolidayRequest $request)
    {
        $createdData = $this->adminService->create_holiday($request->validated());
        if ($createdData == null) {
            return response()->json(['message' => 'Holiday For This Expert Already Exist']);
        } else {
            $returnData = HolidayResource::make($createdData);
        }
        return ApiResponseHelper::sendResponse(
            new Result($returnData, "Done")
        );
    }
    public function chang_permission(ChangePermissionRequest $request)
    {
        $updatedUser = $this->adminService->chang_permission($request->validated());

        if (is_object($updatedUser)) {
            return response()->json(['message' => 'Receiption ' . $updatedUser->name . ' Permission Updated Successfully']);
        } elseif ($updatedUser == 'Failed') {
            return response()->json(['message' => 'You Cannot Change Permission For This User']);
        }
        return ApiResponseHelper::sendResponse(
            new Result("Done")
        );
    }
}
