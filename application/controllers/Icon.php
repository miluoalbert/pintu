<?php

require_once CONTROLLER_PATH . 'Controller.php';

/**
 * Class Icon
 *
 * @author WangNan <jadesouth@aliyun.com>
 * @date   2018-08-31 12:30:56
 */
class Icon extends Controller
{
    public function index()
    {
        $this->load->model('icon_model');
        $data = $this->icon_model->getIcons();
        if (! empty($data)) {
            foreach ($data as &$datum) {
                if (! empty($datum['url'])) {
                    $datum['full_url'] = base_url() . $datum['url'];
                    unset($datum['url']);
                }
                if (! empty($datum['icon_url'])) {
                    $datum['full_icon_url'] = base_url() . $datum['icon_url'];
                    unset($datum['icon_url']);
                }
            }
        }
        return self::responseOK($data);
    }
}