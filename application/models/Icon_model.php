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
        $data['identifier'] = "{$data['category_id']}_{$data['name']}_{$data['e_name']}";
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
        if (! empty($data['category_id']) || ! empty($data['name']) || ! empty($data['e_name'])) {
            $data['identifier'] = "{$data['category_id']}_{$data['name']}_{$data['e_name']}";;
        }
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

    public function insertBySplitCharacter(string $splitCharacterName, string $filePath): bool
    {
        $fileInfo = explode('_', $splitCharacterName);

        if (5 == count($fileInfo)) {
            $identifier = "{$fileInfo[0]}_{$fileInfo[2]}_{$fileInfo[3]}";
            $existed = (boolean)$this->db->from(self::table)
                ->where('identifier', $identifier)
                ->count_all_results();
            $urlName = 1 == $fileInfo[4] ? 'icon_url' : 'url';
            if ($existed) {
                $updateData = [
                    $urlName => $filePath,
                ];
                $res = $this->db->from(self::table)
                    ->set($updateData)
                    ->where('identifier', $identifier)
                    ->update();
            } else {
                $insertData = [
                    'category_id' => $fileInfo[0],
                    'name' => $fileInfo[2],
                    'e_name' => $fileInfo[3],
                    'sort' => $fileInfo[1],
                    'identifier' => $identifier,
                    $urlName => $filePath,
                ];
                var_dump($insertData);
                $res = $this->db->insert(self::table, $insertData);
            }
            return $res;
        }

        return false;
    }
}
