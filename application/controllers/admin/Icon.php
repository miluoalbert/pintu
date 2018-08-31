<?php

require_once ADMIN_CONTROLLER_PATH . 'AdminController.php';

/**
 * Class Icon
 *
 * @author WangNan <jadesouth@aliyun.com>
 * @date   2018-08-27 23:37:56
 */
class Icon extends AdminController
{
    public function index()
    {
        $viewData = [
            'data' => [],
            'page' => null,
        ];
        $this->load->model('icon_model');
        $config['total_rows'] = $this->icon_model->count();
        if (0 < $config['total_rows']) {
            $config['per_page'] = 10;
            $config['base_url'] = base_url('admin/icon/index');
            $this->load->library('pagination');
            $config['uri_segment'] = 4;
            $this->pagination->initialize($config);
            $viewData['page'] = $this->pagination->create_links();
            $pageNum = $this->uri->segment(4, 1);
            $viewData['data'] = $this->icon_model->getPage($pageNum, $config['per_page']);
            // 分类数据
            $this->load->model('category_model');
            $category = $this->category_model->getAll();
            if (! empty($category)) {
                foreach ($category as $item) {
                    $viewData['category'][$item['id']] = $item['name'];
                }
            }
        }

        $this->view('icon/index', $viewData);
    }

    public function create()
    {
        $categoryId = $this->input->post('category');
        $name = $this->input->post('name');
        $eName = $this->input->post('ename');
        $sort = (int)$this->input->post('sort');
        $url = $this->input->post('icon');
        $iconUrl = $this->input->post('icon_url');

        if (0 >= $categoryId) {
            return self::responseError('图标分类必选');
        }
        if (empty($name)) {
            return self::responseError('图标名必须');
        }
        if (empty($eName)) {
            return self::responseError('图标英文名必须');
        }
        if (empty($url)) {
            return self::responseError('图标文件必须上传');
        }
        if (empty($iconUrl)) {
            return self::responseError('图标Icon必须上传');
        }

        $sort = 0 < $sort ? $sort : 0;

        $this->load->model('icon_model');
        $insertData = [
            'category_id' => $categoryId,
            'name'        => $name,
            'e_name'      => $eName,
            'url'         => $url,
            'icon_url'    => $iconUrl,
            'sort'        => $sort,
        ];
        $res = $this->icon_model->add($insertData);

        if ($res) {
            return self::responseSuccess('添加图标成功');
        } else {
            return self::responseError('添加图标失败');
        }
    }

    public function edit()
    {
        $id = (int)$this->input->post('id');
        if (0 >= $id) {
            return self::responseError('非法请求');
        }
        $categoryId = $this->input->post('category');
        $name = $this->input->post('name');
        $eName = $this->input->post('ename');
        $sort = (int)$this->input->post('sort');
        $url = $this->input->post('icon');
        $iconUrl = $this->input->post('icon_url');

        if (0 >= $categoryId) {
            return self::responseError('图标分类必选');
        }
        if (empty($name)) {
            return self::responseError('图标名必须');
        }
        if (empty($eName)) {
            return self::responseError('图标英文名必须');
        }
        if (empty($url)) {
            return self::responseError('图标文件必须上传');
        }
        if (empty($iconUrl)) {
            return self::responseError('图标Icon必须上传');
        }

        $sort = 0 < $sort ? $sort : 0;

        $this->load->model('icon_model');
        $updateData = [
            'category_id' => $categoryId,
            'name'        => $name,
            'e_name'      => $eName,
            'url'         => $url,
            'icon_url'    => $iconUrl,
            'sort'        => $sort,
        ];
        $res = $this->icon_model->set($id, $updateData);

        if ($res) {
            return self::responseSuccess('修改图标成功');
        } else {
            return self::responseError('修改图标失败');
        }
    }

    public function delete()
    {
        $id = (int)$this->input->post('id');
        if (0 >= $id) {
            return self::responseError('非法请求');
        }

        $this->load->model('icon_model');
        $res = $this->icon_model->delete($id);

        if ($res) {
            return self::responseSuccess('删除图标成功');
        } else {
            return self::responseError('删除图标失败');
        }
    }

    public function get()
    {
        $id = (int)$this->input->get('id');
        if (0 >= $id) {
            return self::responseError('非法请求');
        }

        $this->load->model('icon_model');
        $res = $this->icon_model->get($id);
        if (! empty($res['url'])) {
            $res['full_url'] = base_url() . $res['url'];
        }
        if (! empty($res['icon_url'])) {
            $res['full_icon_url'] = base_url() . $res['icon_url'];
        }
        if (empty($res)) {
            return self::responseError('信息不存在');
        }

        // 分类数据
        $this->load->model('category_model');
        $category = $this->category_model->getAll();
        $res['category'] = $category;
        return self::responseOK($res);
    }

    public function show()
    {
        $id = $this->input->post('id');
        if (0 >= $id) {
            return self::responseError('非法请求');
        }

        $this->load->model('icon_model');
        $res = $this->icon_model->set($id, ['is_show' => 1]);

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

        $this->load->model('icon_model');
        $res = $this->icon_model->set($id, ['is_show' => 0]);

        if ($res) {
            return self::responseSuccess('OK');
        } else {
            return self::responseError('设置失败');
        }
    }
}
