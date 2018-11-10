<?php

require_once ADMIN_CONTROLLER_PATH . 'AdminController.php';

/**
 * Class Template
 *
 * @author miluo
 * @date   2018-11-9 16:33:22
 */
class Template extends AdminController
{
    public function index()
    {
        $viewData = [
            'data' => [],
            'page' => null,
        ];

        $viewData['sortBaseUrl'] = base_url() . uri_string() . '?';
        $sortType = $this->input->get('s');
        $sortDirection = (int)$this->input->get('d');

        $conditions = [];

        $getParam['s'] = $sortType;
        $getParam['d'] = $sortDirection;
        $viewData['s'] = $sortType;
        $viewData['d'] = $sortDirection;

        $orderBy = [
            'field' => $sortType,
            'direction' => $sortDirection,
        ];

        // 分页数据
        $this->load->model('template_model');
        $config['total_rows'] = $this->template_model->count($conditions);
        if (0 < $config['total_rows']) {
            $config['per_page'] = 10;
            $config['base_url'] = base_url('admin/template/index');
            $config['uri_segment'] = 4;
            $this->load->library('pagination');
            $this->pagination->initialize($config);
            $viewData['page'] = $this->pagination->create_links();
            $pageNum = $this->uri->segment(4, 1);
            $viewData['data'] = $this->template_model->getPage($pageNum, $conditions, $orderBy, $config['per_page']);
        }

        $this->view('template/index', $viewData);
    }

    public function create()
    {
        $name = $this->input->post('name');
        $sort = (int)$this->input->post('sort');
        $img_url = $this->input->post('img_url');
        $bg1_url = $this->input->post('bg1_url');
        $bg2_url = $this->input->post('bg2_url');
        $bg3_url = $this->input->post('bg3_url');
        $bg4_url = $this->input->post('bg4_url');
        $arrange = $this->input->post('arrange');

        if (empty($name)) {
            return self::responseError('模板名必须');
        }
        if (empty($img_url)) {
            return self::responseError('预览图片必须上传');
        }
        if (empty($bg1_url)) {
            return self::responseError('必须上传至少一张背景图');
        }
        if (empty($arrange)) {
            return self::responseError('必须上传icon位置描述');
        }
        $bg2_url = empty($bg2_url) ? $bg2_url : '';
        $bg3_url = empty($bg3_url) ? $bg3_url : '';
        $bg4_url = empty($bg4_url) ? $bg4_url : '';

        $sort = 0 < $sort ? $sort : 0;

        $this->load->model('template_model');
        $insertData = [
            'name'        => $name,
            'img_url'     => $img_url,
            'bg1_url'     => $bg1_url,
            'bg2_url'     => $bg2_url,
            'bg3_url'     => $bg3_url,
            'bg4_url'     => $bg4_url,
            'arrange'     => $arrange,
            'sort'        => $sort,
        ];
        $res = $this->template_model->add($insertData);

        if ($res) {
            return self::responseSuccess('添加模板成功');
        } else {
            return self::responseError('添加模板失败');
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
            return self::responseError('模板名必须');
        }

        $sort = 0 < $sort ? $sort : 0;

        $this->load->model('template_model');
        $updateData = [
            'name'        => $name,
            'sort'        => $sort,
        ];
        $res = $this->template_model->set($id, $updateData);

        if ($res) {
            return self::responseSuccess('修改模板成功');
        } else {
            return self::responseError('修改模板失败');
        }
    }

    public function delete()
    {
        $id = (int)$this->input->post('id');
        if (0 >= $id) {
            return self::responseError('非法请求');
        }

        $this->load->model('template_model');
        $res = $this->template_model->delete($id);

        if ($res) {
            return self::responseSuccess('删除模板成功');
        } else {
            return self::responseError('删除模板失败');
        }
    }

    public function get()
    {
        $id = (int)$this->input->get('id');
        if (0 >= $id) {
            return self::responseError('非法请求');
        }

        $this->load->model('template_model');
        $res = $this->template_model->get($id);
        if (! empty($res['img_url'])) {
            $res['full_img_url'] = base_url() . $res['img_url'];
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

        $this->load->model('template_model');
        $res = $this->template_model->set($id, ['is_show' => 1]);

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

        $this->load->model('template_model');
        $res = $this->template_model->set($id, ['is_show' => 0]);

        if ($res) {
            return self::responseSuccess('OK');
        } else {
            return self::responseError('设置失败');
        }
    }

}
