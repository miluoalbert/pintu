<?php


/**
 * Class CategoryModel
 *
 * @author WangNan <jadesouth@aliyun.com>
 * @date   2018-08-28 20:55:56
 */
class Category_model extends CI_Model
{
    private const table = 'category';

    public function getAll()
    {
        return $this->db
            ->from(self::table)
            ->get()
            ->result_array();
    }

    public function getPage(int $page, int $pageSize = 20)
    {
        $limit = 0 >= $pageSize ? 20 : $pageSize;
        $offset = ($page - 1) * $limit;
        return $this->db->from(self::table)
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
}