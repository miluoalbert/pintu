<?php


/**
 * Class Controller
 *
 * @author WangNan <jadesouth@aliyun.com>
 * @date   2018-08-28 21:30:56
 */
class Controller extends CI_Controller
{
    const STATUS_SUCCESS = 0;
    const STATUS_ERROR = 1;

    protected static function responseOK($data = null)
    {
        self::responseSuccess('OK', $data);
    }

    protected static function responseSuccess(string $msg, $data = null)
    {
        $response['code'] = self::STATUS_SUCCESS;
        $response['msg'] = $msg;
        $response['data'] = $data;
        self::responseJson($response);
    }

    protected static function responseError(string $msg, $data = null)
    {
        $response['code'] = self::STATUS_ERROR;
        $response['msg'] = $msg;
        $response['data'] = $data;
        self::responseJson($response);
    }

    private static function responseJson(array $data)
    {
        header('Content-type:application/json;charset=UTF-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}