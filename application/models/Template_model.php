<?php


/**
 * Class TemplateModel
 *
 * @author miluo
 * @date   2018-11-9
 */
class Template_model extends CI_Model
{
    private const table = 'template';
    private const sortRule = [
        1 => 'sort',
        2 => 'created_at',
        3 => 'updated_at',
    ];

    public function getAll()
    {
        return $this->db
            ->from(self::table)
            ->get()
            ->result_array();
    }

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

    public function getTemplates()
    {
        $selectFields = ', template.id as template_id, template.name as template_name';
        $selectFields .= ', img_url, bg1_url, bg2_url, bg3_url, bg4_url, arrange';
        return $this->db->select($selectFields)
            ->from('template')
            //->join('icon', 'category.id = icon.category_id', 'left')
            //->where('category.is_show', 1)
            ->where('template.is_show', 1)
            ->order_by('template.sort')
            //->order_by('tamplate.sort')
            ->get()
            ->result_array();
    }
}