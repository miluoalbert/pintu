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
    private const sortRule = [
        1 => 'sort',
        2 => 'created_at',
        3 => 'updated_at',
    ];

    public function getPage(int $page = 1, array $conditions = [], array $orderBy = ['field' => 1, 'direction' => 0], int $pageSize = 10)
    {
        $sort = 'sort';
        $direction = 'desc';
        if (! empty($orderBy['field']) && in_array($orderBy['field'], array_keys(self::sortRule))) {
            $sort = self::sortRule[$orderBy['field']];
            $direction = empty($orderBy['direction']) ? ' desc' : ' asc';
        }
        $limit = 0 >= $pageSize ? 20 : $pageSize;
        $offset = ($page - 1) * $limit;
        empty($conditions) || $this->db->where($conditions);
        $return = $this->db->from(self::table)
            ->order_by($sort, $direction)
            ->order_by('id', 'desc')
            ->limit($limit, $offset)
            ->get()
            ->result_array();
//        echo $this->db->last_query();die;
        return $return;
    }

    public function count(array $conditions = []): int
    {
        empty($conditions) || $this->db->where($conditions);
        return $this->db
            ->from(self::table)
            ->count_all_results();
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