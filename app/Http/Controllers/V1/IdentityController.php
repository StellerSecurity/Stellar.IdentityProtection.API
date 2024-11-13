<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\IdentityStatus;
use App\Models\IdentityProtection;
use App\Services\IdentityService;
use Carbon\Carbon;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class IdentityController extends Controller
{

    public function __construct(private readonly IdentityService $identityService)
    {

    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function add(Request $request): JsonResponse
    {

        if($request->input('email') === null) {
            return response()->json(['response_code' => 400, 'response_message' => 'No email provided.']);
        }

        if($request->input('user_id') === null) {
            return response()->json(['response_code' => 400, 'response_message' => 'No User id provided.']);
        }

        $add = IdentityProtection::create($request->all());

        $add->response_code = 200;
        $add->response_message = 'Identity Protection added successfully.';

        return response()->json($add);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function breached(Request $request): JsonResponse
    {

        $id = $request->input('id');

        $identityProtection = IdentityProtection::find($id);

        if($identityProtection === null) {
            return response()->json(['response_code' => 400, 'response_message' => 'Identity Protection not found.']);
        }

        $breached = $this->identityService->breachedEmail($identityProtection->email);

        $identityStatus = IdentityStatus::CLEAN->value;

        if(count($breached) > 0) {
            $identityStatus = IdentityStatus::BREACHED->value;
        }

        $identityProtection->status = $identityStatus;
        $identityProtection->save();

        return response()->json($breached);

    }

    /**
     *
     * Patching..
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request): JsonResponse
    {
        $id = $request->input('id');

        $identityProtection = IdentityProtection::find($id);

        if($identityProtection === null) {
            return response()->json(['response_code' => 400, 'response_message' => 'Identity Protection not found.']);
        }

        $identityProtection->fill($request->all());
        $identityProtection->save();

        return response()->json($identityProtection);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function delete(Request $request): JsonResponse
    {
        $id = $request->input('id');

        $identityProtection = IdentityProtection::find($id);

        if($identityProtection === null) {
            return response()->json(['response_code' => 400, 'response_message' => 'Identity Protection not found.']);
        }

        $identityProtection->delete();

        return response()->json(['response_code' => 200, 'response_message' => 'Identity Protection deleted.']);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function view(Request $request): JsonResponse
    {

        $id = $request->input('id');

        $identityProtection = IdentityProtection::find($id);

        if($identityProtection === null) {
            return response()->json(['response_code' => 400, 'response_message' => 'Identity Protection not found.']);
        }

        $identityProtection->response_code = 200;
        $identityProtection->response_message = 'OK';

        return response()->json($identityProtection);
    }

    public function scheduler()
    {

        $identityProtections = IdentityProtection::where('last_check', '<=', Carbon::now()->subDays(7)->toDateTimeString())->take(20)->get();

        foreach($identityProtections as $identityProtection) {

            $breached = $this->identityService->breachedEmail($identityProtection->email);

            $identityStatus = IdentityStatus::CLEAN->value;

            if(count($breached) > 0) {
                $identityStatus = IdentityStatus::BREACHED->value;
            }

            $identityProtection->status = $identityStatus;
            $identityProtection->last_check = Carbon::now();
            $identityProtection->save();
        }

    }

}
