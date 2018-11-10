<?php

require_once CONTROLLER_PATH . 'Controller.php';

/**
 * Class Template
 *
 * @author miluo
 * @date   2018-11-10
 */
class Template extends Controller
{
    public function index()
    {
        $this->load->model('template_model');
        $data = $this->template_model->getTemplates();
        if (! empty($data)) {
            foreach ($data as &$datum) {
                if (! empty($datum['img_url'])) {
                    $datum['full_img_url'] = base_url() . $datum['img_url'];
                    unset($datum['img_url']);
                }
                if (! empty($datum['bg1_url'])) {
                    $datum['full_bg1_url'] = base_url() . $datum['bg1_url'];
                    unset($datum['bg1_url']);
                }
                if (! empty($datum['bg2_url'])) {
                    $datum['full_bg2_url'] = base_url() . $datum['bg2_url'];
                    unset($datum['bg2_url']);
                }
                if (! empty($datum['bg3_url'])) {
                    $datum['full_bg3_url'] = base_url() . $datum['bg3_url'];
                    unset($datum['bg3_url']);
                }
                if (! empty($datum['bg4_url'])) {
                    $datum['full_bg4_url'] = base_url() . $datum['bg4_url'];
                    unset($datum['bg4_url']);
                }
            }
        }
        return self::responseOK($data);
    }
}