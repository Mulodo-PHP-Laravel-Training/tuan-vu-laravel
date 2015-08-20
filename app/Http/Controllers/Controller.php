<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

abstract class Controller extends BaseController
{

    use DispatchesJobs, ValidatesRequests;

    private $status = 400;
    private $result;
    private $format = 'json';

    /**
     * Format API response in case successful
     *
     * @param  array $data
     *
     * @return void
     */
    public function formatApiSuccess($data)
    {
        $this->status = 200;
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
        $this->status = $code;
        $error        = json_decode($errorMsg);
        if (!is_object($error))
        {
            $messages = $errorMsg;
        } else
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
                return response($this->result, $this->status)->header('Content-Type', 'application/json');
            case 'xml':
                return response($this->result, $this->status)->header('Content-Type', 'application/xml');
            default:
                break;
        }
    }
}
