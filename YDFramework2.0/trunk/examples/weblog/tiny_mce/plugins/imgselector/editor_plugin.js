function TinyMCE_imgselector_getControlHTML(control_name) {
    switch (control_name) {
        case "imgselector":
            return '<img id="{$editor_id}_imgselector" src="{$pluginurl}/images/ibrowser.gif" title="Select Image" width="20" height="20" class="mceButtonNormal" onmouseover="tinyMCE.switchClass(this,\'mceButtonOver\');" onmouseout="tinyMCE.restoreClass(this);" onmousedown="tinyMCE.restoreAndSwitchClass(this,\'mceButtonDown\');" onclick="tinyMCE.execInstanceCommand(\'{$editor_id}\',\'mceBrowseImage\');">';
    }
    return "";
}

function TinyMCE_imgselector_execCommand(editor_id, element, command, user_interface, value) {
    switch (command) {
        case "mceBrowseImage":
            var template = new Array();
            template['file'] = '../../../manage/images.php?do=SelectorImages'; // Relative to theme location
            template['width'] = 880;
            template['height'] = 660;
            tinyMCE.openWindow(template, new Array());
                return true;
    }
    return false;
}
