<?php
$viewimport = [];
class view
{
    // 定义一个静态函数，用于生成HTML的头部
    public static function header($title = "", $oh = "")
    {
        global $config;
        $title = $title . $config['name'];
        // 输出HTML的头部
        echo "<!DOCTYPE html>\n";
        echo "<head>\n";
        // 引入头部文件
        include includeViewer("header");
        // 输出标题
        echo "\n<title>", $title . "</title>\n";
        // 输出oh变量
        echo $oh;
        echo "</head>\n";
        // 引入导航文件
        include includeViewer("nav");
        // 输出容器
        echo "<div class='container main'>";
    }
    // 定义一个静态函数，用于生成HTML的尾部
    public static function foot()
    {
        // 输出容器结束
        echo "</div>";
        // 引入尾部文件
        global $config;
        include includeViewer("foot");
        view::import();
    }
    // 定义一个静态函数，用于生成图标
    public static function icon($name)
    {
        // 返回图标
        return "<i class='bi bi-$name'></i>";
    }
    // 定义一个静态函数，用于生成jsMdLt
    public static function jsMdLt($id = "pFace", $text)
    {
        // 输出JSMarkdown解析器
        echo "
        <script src='https://cdn.jsdelivr.net/npm/marked/marked.min.js'></script>
        <script>
        document.getElementById('$id').innerHTML = marked.parse(`" . str_replace("`", "\`", addslashes($text)) . "`);
        import('/static/js/mathtex.js');
    </script>";
    }
    public static function alert($text, $type = "info", $currenttime = 5000)
    {
        global $viewimport;
        $id = "alert" . time() . rand(1000, 9999) . rand(1000, 9999);
        $viewimport['alert'] = "<div class='alert alert-$type' role='alert' id='$id'>
        $text" .
            //五秒后使用JS删除这个元素
            "<script>setTimeout(function(){document.getElementById('$id').remove();},$currenttime);</script></div>"
            . $viewimport['alert'];
    }
    public static function message($text, $title = '消息', $icon = "bell", $time = "刚刚")
    {
        global $viewimport;
        $icon = view::icon($icon);
        $id = "message" . time() . rand(1000, 9999) . rand(1000, 9999);
        $viewimport['messagebox'] =  <<<EOF
        <div class="toast fade show" role="alert" aria-live="assertive" aria-atomic="true" id="$id">
            <div class="toast-header">
            $icon
            <strong class="me-auto">$title</strong>
            <small class="text-muted">$time</small>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            $text
        </div>
        </div>
        EOF . $viewimport['messagebox'];
    }
    public static function import()
    {
        global $viewimport;
        if ($viewimport['messagebox']) {
            echo "<div class=\"toast-container right-pos\" id='messageboxbox'>" . $viewimport['messagebox'] . "</div>";
            $viewimport['messagebox'] = "";
        } else {
            echo "<div class=\"toast-container right-pos\" id='messageboxbox'></div>";
        }
        if ($viewimport['alert']) {
            echo "<div class=\"top-pos\" id='alertboxbox'>" . $viewimport['alert'] . "</div>";
            $viewimport['alert'] = "";
        } else {
            echo "<div class=\"top-pos\" id='alertboxbox'></div>";
        }
        if ($viewimport['js']) {
            echo "<script>" . $viewimport['js'] . "</script>";
            $viewimport['js'] = "";
        }
        if ($viewimport['css']) {
            echo "<script>" . $viewimport['css'] . "</script>";
            $viewimport['css'] = "";
        }
    }
    public static function B404()
    {
        include includePage("error/404");
    }
    public static function aceeditor($outid='acecode',$language="markdown")
    {
        global $viewimport;
        echo '<input type="hidden" id="'.$outid.'" name="'.$outid.'">
        <pre id="codeEditor" class="ace_editor" style="min-height:320px"><s:textarea class="ace_text-input"   cssStyle="width:97.5%;height:320px;"/></pre>
        ';
        $viewimport['js']=$viewimport['js'].";\ninitEditor('$outid','$language');\n";
    }
}
