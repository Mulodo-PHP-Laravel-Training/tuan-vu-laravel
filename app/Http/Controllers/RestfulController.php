<?php
namespace App\Http\Controllers;

use App\Http\Controllers;

class RestfulController extends Controller
{

    private $statusCode    = 200;
    private $result;
    private $format        = 'json';
    
    /**
     * Format API response in case successful
     *
     * @param  array $data
     *
     * @return void
     */
    public function formatApiSuccess($data = null)
    {
        $this->statusCode = 200;

        $this->result = [
            'status' => 'success',
            'result' => $data,
        ];
    }

    /**
     * Format API response in case failed
     *
     * @param     $errorMsg
     * @param int $code
     *
     * @return void
     */
    public function formatApiError($errorMsg, $code = 400)
    {
        $this->statusCode = $code;
        $error            = json_decode($errorMsg);
        if (!is_object($error))
        {
            $messages = $errorMsg;
        }
        else
        {
            foreach (get_object_vars($error) as $item => $message)
            {
                $messages[] = [
                    'item'    => $item,
                    'message' => $message
                ];
            }
        }

        $this->result = [
            'status' => 'error',
            'result' => [
                'code'        => $code,
                'description' => $messages,
            ],
        ];
    }

    /**
     * Response api in JSON/XML/Plaintext
     *
     * @return mixed
     */
    public function responseApi()
    {
        switch ($this->format)
        {
            case 'json':
                return response()->json($this->result, $this->statusCode);
            case 'xml':
                return Response::make($this->result, $this->statusCode, ['Content-Type' => 'text/xml; charset=UTF-8']);
            default:
                break;
        }
    }
}