<?php

namespace app\common\command;


use app\lib\Log;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Request;
use app\lib\RollingCurlX;
use app\lib\ApiResponse;


/**
 * 群发小程序客服消息
 * Class WxCmMassTask
 * @package app\common\command
 */
class WxCmMassSender extends Command
{
    protected $msgDict = [];

    protected function configure()
    {
        $this->setName('WxCmMassSender')->setDescription('Mass texting for customer message');
    }

    protected function execute(Input $input, Output $output)
    {
        try{
            $this->main($input, $output);
        } catch (\Exception $exception) {
            Log::error($exception->getTraceAsString());
        }
    }

    protected function main(Input $input, Output $output) {
        $request = Request::instance();
        $request->module('api');

        $massRows = $this->selectRows();
        if(!empty($massRows)) {
            $RCX = new  (10);
            // Callback
            $callback = function ($response, $url, $request_info, $user_data, $time) {

            };

            foreach ($massRows as $tx) {
                // TODO 写了一半


                $this->msgDict[ $tx->notify_url ] = $tx;
                $app = M('app')->getById($tx->app_id);

                if($app) {
                    $Auth = S('sdkAuth')->setSecretKey($app->client_secret);
                    $request->bind('auth', $Auth);

                    $data = S('transaction', 'mch')->simpleInfo($tx);
                    $response = new ApiResponse();
                    $response->success($data);
                    $this->addRequest($RCX, $tx->notify_url, $response->getContent(), $callback);
                }

            }
            $RCX->execute();
        }
    }

    protected function selectRows() {
        $model = M('CmMass');
        $model->where('status', 0);
        return $model->select();
    }

    /**
     * @param RollingCurlX $RCX
     * @param $url
     * @param $post_data
     * @param $callback
     */
    protected function addRequest($RCX, $url, $post_data, $callback) {
        $headers = ['Content-type: application/json'];
        $options = [];
        if(stripos($url, 'https://') !== false) {
            $options['CURLOPT_SSL_VERIFYPEER'] = false;
            $options['CURLOPT_SSL_VERIFYHOST'] = true;
        }

        $RCX->addRequest($url, $post_data, $callback, null, $options, $headers);
    }
}