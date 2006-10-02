function AddText( myField, startTag, defaultText, endTag ) {
    myField = document.getElementById( myField );
    if ( document.selection ) {
        myField.focus();
        sel = document.selection.createRange();
        if ( sel.text.length > 0 ) {
            sel.text = startTag + sel.text + endTag;
        } else {
            if ( endTag == '') {
                sel.text = startTag;
            } else {
                sel.text = startTag + endTag;
            }
        }
    } else if ( myField.selectionStart || myField.selectionStart == '0' ) {
        var startPos = myField.selectionStart;
        var endPos = myField.selectionEnd;
        var cursorPos = endPos;
        var scrollTop = myField.scrollTop;
        if ( startPos != endPos ) {
            myField.value = myField.value.substring(0, startPos)
                          + startTag
                          + myField.value.substring(startPos, endPos) 
                          + endTag
                          + myField.value.substring(endPos, myField.value.length);
            cursorPos += startTag.length + endTag.length;
        } else {
            if ( endTag == '') {
                myField.value = myField.value.substring(0, startPos) 
                              + startTag
                              + myField.value.substring(endPos, myField.value.length);
                cursorPos = startPos + startTag.length;
            } else {
                myField.value = myField.value.substring(0, startPos) 
                              + endTag
                              + myField.value.substring(endPos, myField.value.length);
                cursorPos = startPos + endTag.length;
            }
        }
        myField.selectionStart = cursorPos;
        myField.selectionEnd = cursorPos;
        myField.scrollTop = scrollTop;
    } else {
        if ( endTag == '') {
            myField.value += startTag;
        } else {
            myField.value += startTag + endTag;
        }
    }
    myField.focus();
}

function openWin( url, name, opts ) {
    win = window.open( url, name, opts );
    win.focus();
}
