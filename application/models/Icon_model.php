<?php


/**
 * Class Icon_model
 *
 * @author WangNan <jadesouth@aliyun.com>
 * @date   2018-08-28 20:55:56
 */
class Icon_model extends CI_Model
{
    private const table = 'icon';

    public function getPage(int $page, int $pageSize = 20)
    {
        $limit = 0 >= $pageSize ? 20 : $pageSize;
        $offset = ($page - 1) * $limit;
        return $this->db->from(self::table)
            ->order_by('sort', 'desc')
            ->order_by('id', 'desc')
            ->limit($limit, $offset)
            ->get()
            ->result_array();
    }
    public function count(): int
    {
        return $this->db->count_all(self::table);
    }

    public function add($data)
    {
        return $this->db->insert(self::table, $data);
    }

    public function get(int $id)
    {
        return $this->db->from(self::table)
            ->where('id', $id)
            ->get()
            ->row_array();
    }

    public function delete(int $id)
    {
        return $this->db->from(self::table)
                        ->where('id', $id)
                        ->delete();
    }

    public function set(int $id, $data)
    {
        return $this->db->from(self::table)
                        ->where('id', $id)
                        ->set($data)
                        ->update();
    }

    public function getIcons()
    {
        $selectFields = 'category.id as category_id, category.name as category_name';
        $selectFields .= ', icon.id as icon_id, icon.name as icon_name, e_name as icon_ename';
        $selectFields .= ', url, icon_url';
        return $this->db->select($selectFields)
            ->from('category')
            ->join('icon', 'category.id = icon.category_id', 'left')
            ->where('category.is_show', 1)
            ->where('icon.is_show', 1)
            ->order_by('category.sort')
            ->order_by('icon.sort')
            ->get()
            ->result_array();
    }
}