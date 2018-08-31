<?php

require_once ADMIN_CONTROLLER_PATH . 'AdminController.php';

/**
 * Class Category
 *
 * @author WangNan <jadesouth@aliyun.com>
 * @date   2018-08-27 23:37:56
 */
class Category extends AdminController
{
    public function getEnable()
    {
        $this->load->model('category_model');
        $res = $this->category_model->getAll();
        return self::responseOK($res);
    }
    public function index()
    {
        $viewData = [
            'data' => [],
            'page' => null,
        ];
        $this->load->model('category_model');
        $config['total_rows'] = $this->category_model->count();
        if (0 < $config['total_rows']) {
            $config['per_page'] = 10;
            $config['base_url'] = base_url('admin/category/index');
            $this->load->library('pagination');
            $config['uri_segment'] = 4;
            $this->pagination->initialize($config);
            $viewData['page'] = $this->pagination->create_links();
            $pageNum = $this->uri->segment(4, 1);
            $viewData['data'] = $this->category_model->getPage($pageNum, $config['per_page']);
        }

        $this->view('category/index', $viewData);
    }

    public function create()
    {
        $name = $this->input->post('name');
        $sort = (int)$this->input->post('sort');

        if (empty($name)) {
            return self::responseError('分类名必须');
        }

        $sort = 0 < $sort ? $sort : 0;

        $this->load->model('category_model');
        $res = $this->category_model->add(['name' => $name, 'sort' => $sort]);

        if ($res) {
            return self::responseSuccess('添加分类成功');
        } else {
            return self::responseError('添加分类失败');
        }
    }

    public function edit()
    {
        $id = (int)$this->input->post('id');
        if (0 >= $id) {
            return self::responseError('非法请求');
        }

        $name = $this->input->post('name');
        $sort = (int)$this->input->post('sort');
        if (empty($name)) {
            return self::responseError('分类名必须');
        }

        $sort = 0 < $sort ? $sort : 0;

        $this->load->model('category_model');
        $res = $this->category_model->set($id, ['name' => $name, 'sort' => $sort]);

        if ($res) {
            return self::responseSuccess('修改分类成功');
        } else {
            return self::responseError('修改分类失败');
        }
    }

    public function delete()
    {
        $id = (int)$this->input->post('id');
        if (0 >= $id) {
            return self::responseError('非法请求');
        }

        $this->load->model('category_model');
        $res = $this->category_model->delete($id);

        if ($res) {
            return self::responseSuccess('删除分类成功');
        } else {
            return self::responseError('删除分类失败');
        }
    }

    public function get()
    {
        $id = (int)$this->input->get('id');
        if (0 >= $id) {
            return self::responseError('非法请求');
        }

        $this->load->model('category_model');
        $res = $this->category_model->get($id);
        if (empty($res)) {
            return self::responseError('信息不存在');
        }

        return self::responseOK($res);
    }

    public function show()
    {
        $id = $this->input->post('id');
        if (0 >= $id) {
            return self::responseError('非法请求');
        }

        $this->load->model('category_model');
        $res = $this->category_model->set($id, ['is_show' => 1]);

        if ($res) {
            return self::responseSuccess('OK');
        } else {
            return self::responseError('设置失败');
        }
    }

    public function notShow()
    {
        $id = $this->input->post('id');
        if (0 >= $id) {
            return self::responseError('非法请求');
        }

        $this->load->model('category_model');
        $res = $this->category_model->set($id, ['is_show' => 0]);

        if ($res) {
            return self::responseSuccess('OK');
        } else {
            return self::responseError('设置失败');
        }
    }
}
