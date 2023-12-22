<?php
class practice
{
    public static function empty()
    {
        return array(
            "title" => "标题",
            "face" => "题面",
            "rating" => 100, //全部完成后加分
            "showans" => 0,
            "successshow" => "成功后提示文本",
            "createtime" => time(),
            "creator" => user::read()['name'],"tag"=>[]
        );
    }
    public static function put_common($id, $data)
    {
        return DB::putdata("practice/common/$id", $data);
    }
    public static function get_common($id)
    {
        return DB::getdata("practice/common/$id");
    }
    public static function get_all_common($page = 0, $limit = 100)
    {
        return DB::scanData("practice/common", $page, $limit,0);
    }
    public static function config_get()
    {
        return DB::getdata("practice/config");
    }
    public static function config_put($data)
    {
        return DB::putdata("practice/config", $data);
    }
    public static function config_add($type,$remove=1){
        $data = DB::getdata("practice/config");
        $data[$type] += $remove;
        $data["all"] += $remove;
        DB::putdata("practice/config",$data);
        return $data[$type];
    }
    public static function create_common()
    {
        $new = practice::empty();
        $new["id"] = "".practice::config_add("common");
        $id=$new['id'];
        practice::put_common($id, $new);
        return $id;
    }
    public static function visable_common($cfg){
        global $mypower;
        if (isset($cfg['unlink']) && $cfg['unlink'] >= 1) return 0;
        if (user::is_superuser()) return 1;
        if ($cfg['pn'] <= $mypower) return 1;
    }
}
