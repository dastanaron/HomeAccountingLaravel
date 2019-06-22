<?php

namespace App\Http\Controllers;

use Illuminate\Http;
use App\Integrations\nalogRu;
use Illuminate\Support\Facades;
use App\Models;
use App\Library\Utilities;
use Illuminate\Database;

class QrCodeScannerController extends Controller
{
    /**
     * @var nalogRu\Library\API
     */
    private $api;

    public function __construct()
    {
        $this->api = new nalogRu\Library\API();
    }

    /**
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('qrcodescanner');
    }

    /**
     * Тестовая чешуя, чтобы посмотреть как это вообще будет
     * @param Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendCheck(Http\Request $request)
    {
        $qrcode = $request->input('qrcode');

        $userId = \Auth::id();

        $isValidCode = (new nalogRu\Library\BarcodeParser())->simpleParse($qrcode)->isValid();

        if ($isValidCode) {
            $checkQueueModel = new Models\CheckQueue();

            $checkQueueModel->qrcode = $qrcode;

            $checkQueueModel->user_id = $userId;

            try {
                $isSaved = $checkQueueModel->save();
            }
            catch (Database\QueryException $e) {
                return Facades\Response::json(['status' => 'error', 'message' => 'Cannot save check, may be the check already recorded to base'])->setStatusCode(500);
            }

            if (!$isSaved) {
                return Facades\Response::json(['status' => 'error', 'message' => 'Cannot save check, unknown error'])->setStatusCode(500);
            }

            return Facades\Response::json(['status' => 'saved', 'message' => 'QRcode was saved'])->setStatusCode(200);
        }

        return Facades\Response::json(['status' => 'error', 'message' => 'qrcode is invalid'])->setStatusCode(500);

        /*
        $networkModel = Models\SocialNetwork::where(['user_id' => $userId, 'social_network' => 'nalog_ru'])->first();

        if (empty($networkModel)) {
            return Facades\Response::json(['status' => 'error', 'message' => 'not found integration for user'])->setStatusCode(404);
        }

        $authData = Utilities\Json::decode($networkModel->comment);

        $loginResult = Utilities\Json::decode($this->api->login($authData['phone'], $authData['code']));

        if (array_key_exists('code', $loginResult)) {
            return Facades\Response::json(['status' => 'error', 'message' => 'nalog.ru authorization failed'])->setStatusCode(403);
        }

        try {
            $this->api->checkExist($qrcode);

            $checkDetail = $this->api->getCheckDetailInfo($qrcode, $authData['phone'], $authData['code']);

            $checkModel = new Models\Check();
            $checkModel->user_id = $userId;
            $checkModel->json = $checkDetail;
            $checkModel->save();
        }
        catch (\Exception $e) {
            return Facades\Response::json(['status' => 'error', 'message' => 'Cannot save check'])->setStatusCode(500);
        }

        return Facades\Response::json(['status' => 'saved', 'message' => 'Check was saved'])->setStatusCode(200);
        */
    }
}
