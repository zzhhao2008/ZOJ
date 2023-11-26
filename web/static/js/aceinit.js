function initEditor(id="acecode",language="markdown") {
    //获取控件   id ：codeEditor
    editor = ace.edit("codeEditor");
    theme = "tomorrow";
    
    editor.setTheme("ace/theme/" + theme);
    editor.session.setMode("ace/mode/" + language);
    //字体大小
    editor.setFontSize(15);
    //设置只读（true时只读，用于展示代码）
    editor.setReadOnly(false);
    //自动换行,设置为off关闭
    editor.setOption("wrap", "free");
    //启用提示菜单
    ace.require("ace/ext/language_tools");

    //启用Markdown预览
    editor.getSession().on('change', function() {
        document.getElementById(id).value =
            editor.getValue();
    });
}