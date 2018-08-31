<?php

require_once CONTROLLER_PATH . 'Controller.php';

/**
 * Class AdminController
 *
 * @author WangNan <jadesouth@aliyun.com>
 * @date   2018-08-28 21:32:56
 */
class AdminController extends Controller
{
    protected function view(string $viewName, array $data = [])
    {
        $this->load->view('admin/common/header');
        $this->load->view('admin/common/left');
        $this->load->view('admin/' . $viewName, $data);
        $this->load->view('admin/common/footer');
    }
}