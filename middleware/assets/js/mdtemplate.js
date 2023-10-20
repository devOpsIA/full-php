function childDataViewTemplate(metaDataId, folderId) {
    $.ajax({
        type: 'post',
        url: 'mdtemplate/reportTemplateViewer',
        data: {metaDataId: metaDataId, folderId: folderId},
        dataType: 'json',
        beforeSend: function () {
            Core.blockUI({
                animate: true
            });
        },
        success: function (data) {
            $('.viewer-report-template-'+metaDataId).empty().append(data.html);
            Core.unblockUI();
        },
        error: function () {
            alert("Error");
        }
    });
}
function historyBackTemplateList(metaDataId, folderId) {
    $.ajax({
        type: 'post',
        url: 'mdtemplate/historyBackTemplateList',
        data: {metaDataId: metaDataId, folderId: folderId},
        dataType: 'json',
        success: function (data) {
            childDataViewTemplate(metaDataId, data.parentFolderId);
        },
        error: function () {
            alert("Error");
        }
    });
}
function addTemplateFolder(metaDataId, folderId) {
    var $dialogName = 'dialog-template-add';
    if (!$("#" + $dialogName).length) {
        $('<div id="' + $dialogName + '"></div>').appendTo('body');
    }
    
    $.ajax({
        type: 'post',
        url: 'mdtemplate/addTemplateFolder', 
        data: {metaDataId: metaDataId, folderId: folderId}, 
        dataType: 'json',
        beforeSend: function () {
            Core.blockUI({
                message: 'Loading...', 
                boxed: true 
            });
        },
        success: function (data) {
            $("#" + $dialogName).empty().append(data.html);
            $("#" + $dialogName).dialog({
                cache: false,
                resizable: true,
                bgiframe: true,
                autoOpen: false,
                title: data.title,
                width: 500,
                height: 'auto',
                modal: true,
                close: function () {
                    $("#" + $dialogName).empty().dialog('destroy').remove();
                },
                buttons: [
                    {text: data.save_btn, class: 'btn green-meadow btn-sm', click: function () {
                        
                        $('#temp-folder-form').validate({errorPlacement: function () {}});
                        
                        if ($('#temp-folder-form').valid()) {
        
                            $('#temp-folder-form', '#' + $dialogName).ajaxSubmit({
                                type: 'post',
                                url: 'mdtemplate/addTemplateFolderSave',
                                dataType: 'json',
                                beforeSend: function () {
                                    Core.blockUI({
                                        message: 'Saving...',
                                        boxed: true
                                    });
                                },
                                success: function (data) {
                                    PNotify.removeAll();
                                    new PNotify({
                                        title: data.status,
                                        text: data.message,
                                        type: data.status,
                                        sticker: false 
                                    });
                                        
                                    if (data.status === 'success') {
                                        childDataViewTemplate(metaDataId, folderId);
                                        $("#" + $dialogName).dialog('close');
                                    } 
                                    Core.unblockUI();
                                }
                            });
                        }
                    }}, 
                    {text: data.close_btn, class: 'btn blue-hoki btn-sm', click: function () {
                        $("#" + $dialogName).dialog('close');
                    }}
                ]
            });
            $("#" + $dialogName).dialog('open');
            
            Core.unblockUI();
        },
        error: function () {
            alert("Error");
        }
    }).done(function(){
        Core.initSelect2($("#" + $dialogName));
    });
}
function editTemplateFolder(folderId, parentFolderId, metaDataId) {
    var $dialogName = 'dialog-template-edit';
    if (!$("#" + $dialogName).length) {
        $('<div id="' + $dialogName + '"></div>').appendTo('body');
    }
    
    $.ajax({
        type: 'post',
        url: 'mdtemplate/editTemplateFolder', 
        data: {folderId: folderId, metaDataId: metaDataId}, 
        dataType: 'json',
        beforeSend: function () {
            Core.blockUI({
                message: 'Loading...', 
                boxed: true 
            });
        },
        success: function (data) {
            if (data.hasOwnProperty('status')) {
                PNotify.removeAll();
                new PNotify({
                    title: data.status,
                    text: data.message,
                    type: data.status,
                    addclass: pnotifyPosition,
                    sticker: false 
                });
            } else {
                $("#" + $dialogName).empty().append(data.html);
                $("#" + $dialogName).dialog({
                    cache: false,
                    resizable: true,
                    bgiframe: true,
                    autoOpen: false,
                    title: data.title,
                    width: 500,
                    height: 'auto',
                    modal: true,
                    close: function () {
                        $("#" + $dialogName).empty().dialog('destroy').remove();
                    },
                    buttons: [
                        {text: data.save_btn, class: 'btn green-meadow btn-sm', click: function () {
                            
                            $('#temp-folder-form').validate({errorPlacement: function () {}});
                            
                            if ($('#temp-folder-form').valid()) {
            
                                $('#temp-folder-form', '#' + $dialogName).ajaxSubmit({
                                    type: 'post',
                                    url: 'mdtemplate/editTemplateFolderSave',
                                    dataType: 'json',
                                    beforeSend: function () {
                                        Core.blockUI({
                                            message: 'Saving...',
                                            boxed: true
                                        });
                                    },
                                    success: function (data) {
                                        PNotify.removeAll();
                                        new PNotify({
                                            title: data.status,
                                            text: data.message,
                                            type: data.status,
                                            sticker: false 
                                        });
                                            
                                        if (data.status === 'success') {
                                            childDataViewTemplate(metaDataId, parentFolderId);
                                            $("#" + $dialogName).dialog('close');
                                        } 
                                        Core.unblockUI();
                                    }
                                });
                            }
                        }}, 
                        {text: data.close_btn, class: 'btn blue-hoki btn-sm', click: function () {
                            $("#" + $dialogName).dialog('close');
                        }}
                    ]
                });
                $("#" + $dialogName).dialog('open');
            }
            
            Core.unblockUI();
        },
        error: function () {
            alert("Error");
        }
    }).done(function(){
        Core.initSelect2($("#" + $dialogName));
    });
}
function deleteTemplateFolder(id, dataViewId, folderId, elem) {
    var $dialogName = 'dialog-delete-confirm';
    if (!$("#" + $dialogName).length) {
        $('<div id="' + $dialogName + '"></div>').appendTo('body');
    }

    $.ajax({
        type: 'post',
        url: 'mdcommon/deleteConfirm',
        dataType: 'json',
        beforeSend: function(){
            Core.blockUI({
                animate: true
            });
        },
        success: function(data){
            $("#" + $dialogName).empty().html(data.Html);
            $("#" + $dialogName).dialog({
                cache: false,
                resizable: false,
                bgiframe: true,
                autoOpen: false,
                title: data.Title,
                width: 330,
                height: "auto",
                modal: true,
                close: function(){
                    $("#" + $dialogName).empty().dialog('destroy').remove();
                },
                buttons: [
                    {text: data.yes_btn, class: 'btn green-meadow btn-sm', click: function(){
                        $.ajax({
                            type: 'post',
                            url: 'mdtemplate/deleteTemplateFolder',
                            data: {id: id},
                            dataType: 'json',
                            success: function(data){
                                
                                PNotify.removeAll();
                                new PNotify({
                                    title: data.status,
                                    text: data.message,
                                    type: data.status,
                                    sticker: false
                                });
                                    
                                if (data.status === 'success') {
                                    elem.remove();
                                    $("#" + $dialogName).dialog('close');
                                } 
                            },
                            error: function(){
                                alert("Error");
                            }
                        });

                    }},
                    {text: data.no_btn, class: 'btn blue-madison btn-sm', click: function(){
                        $("#" + $dialogName).dialog('close');
                    }}
                ]
            });
            $("#" + $dialogName).dialog('open');
            Core.unblockUI();
        },
        error: function(){
            alert("Error");
        }
    });
}
function editDataViewTemplate(id, dataViewId, folderId) {
    
    $.ajax({
        type: 'post',
        url: 'mdtemplate/editTemplate', 
        data: {id: id, dataViewId: dataViewId, folderId: folderId}, 
        dataType: 'json',
        beforeSend: function () {
            Core.blockUI({
                message: 'Loading...', 
                boxed: true 
            });
        },
        success: function (data) {
            
            if (data.hasOwnProperty('status')) {
                PNotify.removeAll();
                new PNotify({
                    title: data.status,
                    text: data.message,
                    type: data.status,
                    addclass: pnotifyPosition,
                    sticker: false 
                });
            } else {
                
                var $dialogName = 'dialog-template-edit';
                if (!$("#" + $dialogName).length) {
                    $('<div id="' + $dialogName + '"></div>').appendTo('body');
                }
                var $dialog = $("#" + $dialogName);
    
                $dialog.empty().append(data.html);
                $dialog.dialog({
                    cache: false,
                    resizable: true,
                    bgiframe: true,
                    autoOpen: false,
                    title: data.title,
                    width: 520,
                    height: 'auto',
                    modal: true,
                    close: function () {
                        $dialog.empty().dialog('destroy').remove();
                    },
                    buttons: [
                        {text: data.save_btn, class: 'btn green-meadow btn-sm', click: function () {

                            $('#temp-folder-form').validate({errorPlacement: function () {}});

                            if ($('#temp-folder-form').valid()) {

                                $('#temp-folder-form', '#' + $dialogName).ajaxSubmit({
                                    type: 'post',
                                    url: 'mdtemplate/editTemplateSave',
                                    dataType: 'json',
                                    beforeSend: function () {
                                        Core.blockUI({
                                            message: 'Saving...',
                                            boxed: true
                                        });
                                    },
                                    success: function (data) {
                                        PNotify.removeAll();
                                        new PNotify({
                                            title: data.status,
                                            text: data.message,
                                            type: data.status,
                                            addclass: pnotifyPosition,
                                            sticker: false 
                                        });

                                        if (data.status == 'success') {    
                                            childDataViewTemplate(dataViewId, folderId);
                                            $dialog.dialog('close');
                                        } 

                                        Core.unblockUI();
                                    }
                                });
                            }
                        }}, 
                        {text: data.close_btn, class: 'btn blue-hoki btn-sm', click: function () {
                            $dialog.dialog('close');
                        }}
                    ]
                });
                Core.initSelect2($dialog);
                $dialog.dialog('open');
            }
            
            Core.unblockUI();
        },
        error: function () {
            alert("Error");
        }
    });
}
function editDataViewReportTemplate(id, dataViewId, folderId) {
    $.ajax({
        type: 'post',
        url: 'mdtemplate/editTemplateFile', 
        data: {id: id, dataViewId: dataViewId, folderId: folderId}, 
        dataType: 'json',
        beforeSend: function () {
            Core.blockUI({message: 'Loading...', boxed: true });
        },
        success: function (data) {
            
            if (data.hasOwnProperty('status')) {
                PNotify.removeAll();
                new PNotify({
                    title: data.status,
                    text: data.message,
                    type: data.status,
                    addclass: pnotifyPosition,
                    sticker: false 
                });
            } else {
                
                var $dialogName = 'dialog-tmceTemplateEditor';
                if (!$('#' + $dialogName).length) {
                    $('<div id="' + $dialogName + '"></div>').appendTo('body');
                }
                var $dialog = $('#' + $dialogName);
    
                $dialog.empty().append(data.html);
                $dialog.dialog({
                    cache: false,
                    resizable: true,
                    bgiframe: true,
                    autoOpen: false,
                    title: data.title,
                    width: 1200,
                    minWidth: 1200,
                    height: 600,
                    modal: false,
                    open: function() {
                        var windowHeight = $(window).height();
                        var _tinymceHeight = windowHeight - 290;
                        _tinymceHeight = (_tinymceHeight <= 100) ? '400px' : _tinymceHeight+ 'px';
                        
                        $.cachedScript('assets/custom/addon/plugins/tinymce/tinymce.min.js').done(function() { 
                            tinymce.dom.Event.domLoaded = true;
                            tinymce.baseURL = URL_APP+'assets/custom/addon/plugins/tinymce';
                            tinymce.suffix = ".min";
                            tinymce.init({
                                selector: 'textarea#tempEditor',
                                height: _tinymceHeight,
                                plugins: [
                                    'advlist autolink lists link image charmap print preview hr anchor pagebreak',
                                    'searchreplace wordcount visualblocks visualchars code fullscreen',
                                    'insertdatetime media nonbreaking save table contextmenu directionality importcss codemirror',
                                    'emoticons template paste textcolor colorpicker textpattern imagetools moxiemanager mention lineheight'
                                ],
                                toolbar1: 'undo redo | styleselect | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
                                toolbar2: 'print preview | forecolor backcolor | fontselect | fontsizeselect | lineheightselect | table | fullscreen | code | customEnterSpacer',
                                fontsize_formats: '8px 9px 10px 11px 12px 13px 14px 15px 16px 17px 18px 19px 20px 21px 22px 23px 24px 25px 36px 8pt 9pt 10pt 11pt 12pt 13pt 14pt 15pt 16pt 17pt 18pt 19pt 20pt 21pt 22pt 23pt 24pt 25pt 36pt', 
                                lineheight_formats: '8px 9px 10px 11px 12px 13px 14px 15px 16px 17px 18px 19px 20px 1.0 1.15 1.5 2.0 2.5 3.0',
                                image_advtab: true, 
                                force_br_newlines: true,
                                force_p_newlines: false, 
                                apply_source_formatting: true, 
                                remove_linebreaks: false,
                                forced_root_block: '', 
                                paste_data_images: true, 
                                importcss_append: true, 
                                table_toolbar: '', 
                                table_class_list: [
                                    {title: 'None', value: ''}, 
                                    {title: 'No border', value: 'pf-report-table-none'}, 
                                    {title: 'Dotted', value: 'pf-report-table-dotted'}, 
                                    {title: 'Dashed', value: 'pf-report-table-dashed'},  
                                    {title: 'Solid', value: 'pf-report-table-solid'}
                                ], 
                                object_resizing: 'img',
                                paste_word_valid_elements: 'b,p,br,strong,i,em,h1,h2,h3,h4,ul,li,ol,table,span,div,font,page',
                                font_formats: "Andale Mono=andale mono,monospace;"+
                                    "Arial=arial,helvetica,sans-serif;"+
                                    "Arial Black=arial black,sans-serif;"+
                                    "Book Antiqua=book antiqua,palatino,serif;"+
                                    "Comic Sans MS=comic sans ms,sans-serif;"+
                                    "Courier New=courier new,courier,monospace;"+
                                    "Georgia=georgia,palatino,serif;"+
                                    "Helvetica=helvetica,arial,sans-serif;"+
                                    "Impact=impact,sans-serif;"+
                                    "Symbol=symbol;"+
                                    "Tahoma=tahoma,arial,helvetica,sans-serif;"+
                                    "Terminal=terminal,monaco,monospace;"+
                                    "Times New Roman=times new roman,times,serif;"+
                                    "Calibri=Calibri, sans-serif;"+
                                    "Trebuchet MS=trebuchet ms,geneva,sans-serif;"+
                                    "Verdana=verdana,geneva,sans-serif;"+
                                    "Webdings=webdings;"+
                                    "Wingdings=wingdings,zapf dingbats;"+
                                    "Next Museo=Next_MuseoSansCyrl;",
                                mentions: {
                                    delimiter: '#',
                                    delay: 0, 
                                    queryBy: 'FIELD_PATH', 
                                    source: function (query, process, delimiter) {
                                        $.ajax({
                                            type: "post",
                                            url: "mdtemplate/getAllVariablesByJson",
                                            data: {dataViewId: $("#metaGroupId").val()}, 
                                            dataType: 'json', 
                                            success: function(data){
                                                process(data);
                                            }
                                        });
                                    }, 
                                    render: function(item) {
                                        return '<li>' +
                                                   '<a href="javascript:;">' + item.FIELD_PATH + ' - '+item.META_DATA_NAME+'</a>' +
                                               '</li>';
                                    },
                                    insert: function(item) {
                                        return '#'+item.field_path+'#';
                                    }
                                },
                                codemirror: {
                                    indentOnInit: true, 
                                    fullscreen: false,   
                                    path: 'codemirror', 
                                    config: {           
                                        mode: 'text/html',
                                        styleActiveLine: true,
                                        lineNumbers: true, 
                                        lineWrapping: true,
                                        matchBrackets: true,
                                        autoCloseBrackets: true,
                                        indentUnit: 2, 
                                        foldGutter: true,
                                        gutters: ["CodeMirror-linenumbers", "CodeMirror-foldgutter"], 
                                        extraKeys: {
                                            "F11": function(cm) {
                                                cm.setOption("fullScreen", !cm.getOption("fullScreen"));
                                            },
                                            "Esc": function(cm) {
                                                if (cm.getOption("fullScreen")) cm.setOption("fullScreen", false);
                                            }, 
                                            "Ctrl-Q": function(cm) { 
                                                cm.foldCode(cm.getCursor()); 
                                            }, 
                                            "Ctrl-Space": "autocomplete"
                                        }
                                    },
                                    width: ($(window).width() - 20),        
                                    height: ($(window).height() - 120),        
                                    saveCursorPosition: false,    
                                    jsFiles: [          
                                        'mode/clike/clike.js',
                                        'mode/htmlmixed/htmlmixed.js', 
                                        'mode/css/css.js', 
                                        'mode/xml/xml.js', 
                                        'addon/fold/foldcode.js', 
                                        'addon/fold/foldgutter.js', 
                                        'addon/fold/brace-fold.js', 
                                        'addon/fold/xml-fold.js', 
                                        'addon/fold/indent-fold.js', 
                                        'addon/fold/comment-fold.js', 
                                        'addon/hint/show-hint.js', 
                                        'addon/hint/xml-hint.js', 
                                        'addon/hint/html-hint.js', 
                                        'addon/hint/css-hint.js'
                                    ]
                                },
                                setup: function(editor) {
                                    editor.on('init', function() {
                                        $('textarea#tempEditor').prev('.mce-container').find('.mce-edit-area')
                                        .droppable({
                                            drop: function(event, ui) {
                                                tinymce.activeEditor.execCommand('mceInsertContent', false, '#'+ui.draggable.text()+'#');
                                            }
                                        });
                                    });
                                    editor.addButton('customEnterSpacer', {
                                        type: 'menubutton',
                                        text: 'Line spacer',
                                        icon: false,
                                        menu: [
                                            {icon: false, text: '4pt', onclick: function() {
                                                tinymce.activeEditor.execCommand('mceInsertContent', false, '<div style="height:4pt"></div>');
                                            }},
                                            {icon: false, text: '5pt', onclick: function() {
                                                tinymce.activeEditor.execCommand('mceInsertContent', false, '<div style="height:5pt"></div>');
                                            }},
                                            {icon: false, text: '6pt', onclick: function() {
                                                tinymce.activeEditor.execCommand('mceInsertContent', false, '<div style="height:6pt"></div>');
                                            }},
                                            {icon: false, text: '7pt', onclick: function() {
                                                tinymce.activeEditor.execCommand('mceInsertContent', false, '<div style="height:7pt"></div>');
                                            }},
                                            {icon: false, text: '8pt', onclick: function() {
                                                tinymce.activeEditor.execCommand('mceInsertContent', false, '<div style="height:8pt"></div>');
                                            }},
                                            {icon: false, text: '9pt', onclick: function() {
                                                tinymce.activeEditor.execCommand('mceInsertContent', false, '<div style="height:9pt"></div>');
                                            }},
                                            {icon: false, text: '10pt', onclick: function() {
                                                tinymce.activeEditor.execCommand('mceInsertContent', false, '<div style="height:10pt"></div>');
                                            }},
                                            {icon: false, text: '11pt', onclick: function() {
                                                tinymce.activeEditor.execCommand('mceInsertContent', false, '<div style="height:11pt"></div>');
                                            }},
                                            {icon: false, text: '12pt', onclick: function() {
                                                tinymce.activeEditor.execCommand('mceInsertContent', false, '<div style="height:12pt"></div>');
                                            }}
                                        ]
                                    });           
                                    editor.on('keydown', function(evt) {    
                                        if (evt.keyCode == 9) {
                                            editor.execCommand('mceInsertContent', false, '&emsp;&emsp;');
                                            evt.preventDefault();
                                            return false;
                                        }
                                    });
                                },  
                                contextmenu: 'link image inserttable | tableprops cell row column deletetable',
                                plugin_preview_width: 1200,
                                document_base_url: URL_APP, 
                                content_css: [
                                    URL_APP+'assets/custom/css/print/tinymce.css', 
                                    URL_APP+'assets/custom/webfonts/nextmuseo/font.css'
                                ]
                            });

                            tinymce.init({
                                selector: 'textarea[name="tempHeaderEditor"], textarea[name="tempFooterEditor"]',
                                height: windowHeight - 285 - 350,
                                plugins: [
                                    'advlist autolink lists link image charmap print preview hr anchor pagebreak',
                                    'searchreplace wordcount visualblocks visualchars code fullscreen',
                                    'insertdatetime media nonbreaking save table contextmenu directionality importcss codemirror',
                                    'emoticons template paste textcolor colorpicker textpattern imagetools moxiemanager mention lineheight'
                                ],
                                toolbar1: 'undo redo | styleselect | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
                                toolbar2: 'print preview | forecolor backcolor | fontselect | fontsizeselect | lineheightselect | table | fullscreen | code | customEnterSpacer',
                                fontsize_formats: '8px 9px 10px 11px 12px 13px 14px 15px 16px 17px 18px 19px 20px 21px 22px 23px 24px 25px 36px 8pt 9pt 10pt 11pt 12pt 13pt 14pt 15pt 16pt 17pt 18pt 19pt 20pt 21pt 22pt 23pt 24pt 25pt 36pt', 
                                lineheight_formats: '8px 9px 10px 11px 12px 13px 14px 15px 16px 17px 18px 19px 20px 1.0 1.15 1.5 2.0 2.5 3.0',
                                image_advtab: true, 
                                force_br_newlines: true,
                                force_p_newlines: false, 
                                apply_source_formatting: true, 
                                remove_linebreaks: false,
                                forced_root_block: '', 
                                paste_data_images: true, 
                                importcss_append: true, 
                                table_toolbar: '', 
                                table_class_list: [
                                    {title: 'None', value: ''}, 
                                    {title: 'No border', value: 'pf-report-table-none'}, 
                                    {title: 'Dotted', value: 'pf-report-table-dotted'}, 
                                    {title: 'Dashed', value: 'pf-report-table-dashed'},  
                                    {title: 'Solid', value: 'pf-report-table-solid'}
                                ], 
                                object_resizing: 'img',
                                paste_word_valid_elements: 'b,p,br,strong,i,em,h1,h2,h3,h4,ul,li,ol,table,span,div,font,page',
                                font_formats: "Andale Mono=andale mono,monospace;"+
                                    "Arial=arial,helvetica,sans-serif;"+
                                    "Arial Black=arial black,sans-serif;"+
                                    "Book Antiqua=book antiqua,palatino,serif;"+
                                    "Comic Sans MS=comic sans ms,sans-serif;"+
                                    "Courier New=courier new,courier,monospace;"+
                                    "Georgia=georgia,palatino,serif;"+
                                    "Helvetica=helvetica,arial,sans-serif;"+
                                    "Impact=impact,sans-serif;"+
                                    "Symbol=symbol;"+
                                    "Tahoma=tahoma,arial,helvetica,sans-serif;"+
                                    "Terminal=terminal,monaco,monospace;"+
                                    "Times New Roman=times new roman,times,serif;"+
                                    "Calibri=Calibri, sans-serif;"+
                                    "Trebuchet MS=trebuchet ms,geneva,sans-serif;"+
                                    "Verdana=verdana,geneva,sans-serif;"+
                                    "Webdings=webdings;"+
                                    "Wingdings=wingdings,zapf dingbats;"+
                                    "Next Museo=Next_MuseoSansCyrl;",
                                mentions: {
                                    delimiter: '#',
                                    delay: 0, 
                                    queryBy: 'FIELD_PATH', 
                                    source: function (query, process, delimiter) {
                                        $.ajax({
                                            type: "post",
                                            url: "mdtemplate/getAllVariablesByJson",
                                            data: {dataViewId: $("#metaGroupId").val()}, 
                                            dataType: 'json', 
                                            success: function(data){
                                                process(data);
                                            }
                                        });
                                    }, 
                                    render: function(item) {
                                        return '<li>' +
                                                   '<a href="javascript:;">' + item.FIELD_PATH + ' - '+item.META_DATA_NAME+'</a>' +
                                               '</li>';
                                    },
                                    insert: function(item) {
                                        return '#'+item.field_path+'#';
                                    }
                                },
                                codemirror: {
                                    indentOnInit: true, 
                                    fullscreen: false,   
                                    path: 'codemirror', 
                                    config: {           
                                        mode: 'text/html',
                                        styleActiveLine: true,
                                        lineNumbers: true, 
                                        lineWrapping: true,
                                        matchBrackets: true,
                                        autoCloseBrackets: true,
                                        indentUnit: 2, 
                                        foldGutter: true,
                                        gutters: ["CodeMirror-linenumbers", "CodeMirror-foldgutter"], 
                                        extraKeys: {
                                            "F11": function(cm) {
                                                cm.setOption("fullScreen", !cm.getOption("fullScreen"));
                                            },
                                            "Esc": function(cm) {
                                                if (cm.getOption("fullScreen")) cm.setOption("fullScreen", false);
                                            }, 
                                            "Ctrl-Q": function(cm) { 
                                                cm.foldCode(cm.getCursor()); 
                                            }, 
                                            "Ctrl-Space": "autocomplete"
                                        }
                                    },
                                    width: ($(window).width() - 20),        
                                    height: ($(window).height() - 120),        
                                    saveCursorPosition: false,    
                                    jsFiles: [          
                                        'mode/clike/clike.js',
                                        'mode/htmlmixed/htmlmixed.js', 
                                        'mode/css/css.js', 
                                        'mode/xml/xml.js', 
                                        'addon/fold/foldcode.js', 
                                        'addon/fold/foldgutter.js', 
                                        'addon/fold/brace-fold.js', 
                                        'addon/fold/xml-fold.js', 
                                        'addon/fold/indent-fold.js', 
                                        'addon/fold/comment-fold.js', 
                                        'addon/hint/show-hint.js', 
                                        'addon/hint/xml-hint.js', 
                                        'addon/hint/html-hint.js', 
                                        'addon/hint/css-hint.js'
                                    ]
                                },
                                setup: function(editor) {
                                    editor.on('init', function() {
                                        $('textarea#tempEditor').prev('.mce-container').find('.mce-edit-area')
                                        .droppable({
                                            drop: function(event, ui) {
                                                tinymce.activeEditor.execCommand('mceInsertContent', false, '#'+ui.draggable.text()+'#');
                                            }
                                        });
                                    });
                                    editor.addButton('customEnterSpacer', {
                                        type: 'menubutton',
                                        text: 'Line spacer',
                                        icon: false,
                                        menu: [
                                            {icon: false, text: '4pt', onclick: function() {
                                                tinymce.activeEditor.execCommand('mceInsertContent', false, '<div style="height:4pt"></div>');
                                            }},
                                            {icon: false, text: '5pt', onclick: function() {
                                                tinymce.activeEditor.execCommand('mceInsertContent', false, '<div style="height:5pt"></div>');
                                            }},
                                            {icon: false, text: '6pt', onclick: function() {
                                                tinymce.activeEditor.execCommand('mceInsertContent', false, '<div style="height:6pt"></div>');
                                            }},
                                            {icon: false, text: '7pt', onclick: function() {
                                                tinymce.activeEditor.execCommand('mceInsertContent', false, '<div style="height:7pt"></div>');
                                            }},
                                            {icon: false, text: '8pt', onclick: function() {
                                                tinymce.activeEditor.execCommand('mceInsertContent', false, '<div style="height:8pt"></div>');
                                            }},
                                            {icon: false, text: '9pt', onclick: function() {
                                                tinymce.activeEditor.execCommand('mceInsertContent', false, '<div style="height:9pt"></div>');
                                            }},
                                            {icon: false, text: '10pt', onclick: function() {
                                                tinymce.activeEditor.execCommand('mceInsertContent', false, '<div style="height:10pt"></div>');
                                            }},
                                            {icon: false, text: '11pt', onclick: function() {
                                                tinymce.activeEditor.execCommand('mceInsertContent', false, '<div style="height:11pt"></div>');
                                            }},
                                            {icon: false, text: '12pt', onclick: function() {
                                                tinymce.activeEditor.execCommand('mceInsertContent', false, '<div style="height:12pt"></div>');
                                            }}
                                        ]
                                    });        
                                    editor.on('keydown', function(evt) {    
                                        if (evt.keyCode == 9) {
                                            editor.execCommand('mceInsertContent', false, '&emsp;&emsp;');
                                            evt.preventDefault();
                                            return false;
                                        }
                                    });
                                },  
                                contextmenu: 'link image inserttable | tableprops cell row column deletetable',
                                plugin_preview_width: 1200,
                                document_base_url: URL_APP, 
                                content_css: [
                                    URL_APP+'assets/custom/css/print/tinymce.css', 
                                    URL_APP+'assets/custom/webfonts/nextmuseo/font.css'
                                ]
                            });
                        });
                    }, 
                    close: function () {
                        tinymce.remove('textarea');
                        $dialog.empty().dialog('destroy').remove();
                    },
                    buttons: [
                        {text: data.save_btn, class: 'btn green-meadow btn-sm', click: function() {

                            tinymce.triggerSave();

                            $('#temp-content-form', '#' + $dialogName).ajaxSubmit({
                                type: 'post',
                                url: 'mdtemplate/editTemplateFileSave',
                                dataType: 'json',
                                beforeSend: function () {
                                    Core.blockUI({
                                        message: 'Saving...',
                                        boxed: true
                                    });
                                },
                                success: function (data) {
                                    PNotify.removeAll();
                                    new PNotify({
                                        title: data.status,
                                        text: data.message,
                                        type: data.status,
                                        sticker: false 
                                    });

                                    if (data.status === 'success') {
                                        $dialog.dialog('close');
                                    }
                                    Core.unblockUI();
                                }
                            });
                        }}, 
                        {text: data.close_btn, class: 'btn blue-hoki btn-sm', click: function () {
                            $dialog.dialog('close');
                        }}
                    ]
                }).dialogExtend({
                    "closable": true,
                    "maximizable": true,
                    "minimizable": true,
                    "collapsable": true,
                    "dblclick": "maximize",
                    "minimizeLocation": "left", 
                    "icons": {
                        "close": "ui-icon-circle-close",
                        "maximize": "ui-icon-extlink",
                        "minimize": "ui-icon-minus",
                        "collapse": "ui-icon-triangle-1-s",
                        "restore": "ui-icon-newwin"
                    }, 
                    "maximize": function() { 
                        $dialog.find('#childList').css('height', $dialog.height());
                    }
                });
                $dialog.dialog('open');
                $dialog.dialogExtend('maximize');
            }
            
            Core.unblockUI();
        },
        error: function () {
            alert("Error");
            Core.unblockUI();
        }
    });
}
function iframeReportDesigner(reportTemplateId, dvId) {
    $.ajax({
        type: 'post',
        url: 'mdtemplate/iframeReportDesigner',
        data: {
            reportTemplateId: reportTemplateId, 
            dvId: dvId,
            windowHeight: $(window).height()
        },
        dataType: 'json',
        beforeSend: function() {
            Core.blockUI({
                message: 'Loading...',
                boxed: true
            });
        },
        success: function(data) {
            
            if (data.status == 'success') {
                
                var $dialogName = 'dialog-reportiframe-'+reportTemplateId;
                if (!$("#" + $dialogName).length) {
                    $('<div id="' + $dialogName + '"></div>').appendTo('body');
                }
                var $dialog = $('#' + $dialogName);
    
                $dialog.empty().append(data.html);
                $dialog.dialog({
                    cache: false,
                    resizable: true,
                    bgiframe: true,
                    autoOpen: false,
                    title: data.title,
                    width: 1200,
                    minWidth: 1200,
                    height: "auto",
                    modal: false,
                    buttons: [
                        {text: data.close_btn, class: 'btn btn-sm blue-hoki', click: function() {
                            $dialog.dialog('close');
                        }}
                    ]
                }).dialogExtend({
                    "closable": true,
                    "maximizable": true,
                    "minimizable": true,
                    "collapsable": true,
                    "dblclick": "maximize",
                    "minimizeLocation": "left",
                    "icons": {
                        "close": "ui-icon-circle-close",
                        "maximize": "ui-icon-extlink",
                        "minimize": "ui-icon-minus",
                        "collapse": "ui-icon-triangle-1-s",
                        "restore": "ui-icon-newwin"
                    }
                });
                $dialog.dialog('open');
                $dialog.dialogExtend("maximize");
                
            } else {
                PNotify.removeAll();
                new PNotify({
                    title: data.status,
                    text: data.message,
                    type: data.status,
                    sticker: false
                });  
            }
            Core.unblockUI();
        },
        error: function() {
            alert("Error");
            Core.unblockUI();
        }
    });
}
function copyDataViewReportTemplate(id, dataViewId, folderId) {
    var $dialogName = 'dialog-template-copy';
    if (!$("#" + $dialogName).length) {
        $('<div id="' + $dialogName + '"></div>').appendTo('body');
    }
    var $dialog = $('#' + $dialogName);
    
    $.ajax({
        type: 'post',
        url: 'mdtemplate/copyTemplate', 
        data: {id: id, dataViewId: dataViewId, folderId: folderId}, 
        dataType: 'json',
        beforeSend: function () {
            Core.blockUI({
                message: 'Loading...', 
                boxed: true 
            });
        },
        success: function (data) {
            $dialog.empty().append(data.html);
            $dialog.dialog({
                cache: false,
                resizable: true,
                bgiframe: true,
                autoOpen: false,
                title: data.title,
                width: 500,
                height: 'auto',
                modal: true,
                close: function () {
                    $dialog.empty().dialog('destroy').remove();
                },
                buttons: [
                    {text: data.save_btn, class: 'btn green-meadow btn-sm', click: function () {
                        
                        $('#temp-folder-form').validate({errorPlacement: function () {}});
                        
                        if ($('#temp-folder-form').valid()) {
        
                            $('#temp-folder-form', '#' + $dialogName).ajaxSubmit({
                                type: 'post',
                                url: 'mdtemplate/copyTemplateSave',
                                dataType: 'json',
                                beforeSend: function () {
                                    Core.blockUI({
                                        message: 'Saving...',
                                        boxed: true
                                    });
                                },
                                success: function (data) {
                                    PNotify.removeAll();
                                    new PNotify({
                                        title: data.status,
                                        text: data.message,
                                        type: data.status,
                                        sticker: false 
                                    });
                                        
                                    if (data.status === 'success') {
                                        childDataViewTemplate(dataViewId, folderId);
                                        $dialog.dialog('close');
                                    } 
                                    
                                    Core.unblockUI();
                                }
                            });
                        }
                    }}, 
                    {text: data.close_btn, class: 'btn blue-hoki btn-sm', click: function () {
                        $dialog.dialog('close');
                    }}
                ]
            });
            $dialog.dialog('open');
            
            Core.unblockUI();
        },
        error: function () {
            alert("Error");
        }
    }).done(function(){
        Core.initSelect2($dialog);
    });
}
function deleteDataViewTemplate(id, elem) {
    var $dialogName = 'dialog-delete-confirm';
    if (!$("#" + $dialogName).length) {
        $('<div id="' + $dialogName + '"></div>').appendTo('body');
    }

    $.ajax({
        type: 'post',
        url: 'mdcommon/deleteConfirm',
        dataType: 'json',
        beforeSend: function(){
            Core.blockUI({
                animate: true
            });
        },
        success: function(data){
            $("#" + $dialogName).empty().html(data.Html);
            $("#" + $dialogName).dialog({
                cache: false,
                resizable: false,
                bgiframe: true,
                autoOpen: false,
                title: data.Title,
                width: 330,
                height: "auto",
                modal: true,
                close: function(){
                    $("#" + $dialogName).empty().dialog('destroy').remove();
                },
                buttons: [
                    {text: data.yes_btn, class: 'btn green-meadow btn-sm', click: function(){
                        $.ajax({
                            type: 'post',
                            url: 'mdtemplate/deleteDataViewTemplate',
                            data: {id: id},
                            dataType: 'json',
                            beforeSend: function(){
                                Core.blockUI({
                                    message: 'Deleting...',
                                    boxed: true
                                });
                            },
                            success: function(data){
                                
                                PNotify.removeAll();
                                new PNotify({
                                    title: data.status,
                                    text: data.message,
                                    type: data.status,
                                    sticker: false
                                });
                                    
                                if (data.status == 'success') {
                                    elem.remove();
                                    $("#" + $dialogName).dialog('close');
                                }
                                
                                Core.unblockUI();
                            },
                            error: function(){
                                alert("Error");
                            }
                        });

                    }},
                    {text: data.no_btn, class: 'btn blue-madison btn-sm', click: function(){
                        $("#" + $dialogName).dialog('close');
                    }}
                ]
            });
            $("#" + $dialogName).dialog('open');
            Core.unblockUI();
        },
        error: function(){
            alert("Error");
        }
    });
}
function previewDataViewReportTemplate(id) {
    $.ajax({
        type: 'post',
        url: 'mdtemplate/previewTemplateFile', 
        data: {id: id}, 
        dataType: 'json',
        beforeSend: function () {
            Core.blockUI({
                message: 'Loading...', 
                boxed: true 
            });
        },
        success: function (data) {
            
            PNotify.removeAll();
            
            if (data.hasOwnProperty('status')) {
                new PNotify({
                    title: data.status,
                    text: data.message,
                    type: data.status,
                    addclass: pnotifyPosition,
                    sticker: false 
                });
            } else {
                
                var $dialogName = 'dialog-rtemplate-preview';
                if (!$('#' + $dialogName).length) {
                    $('<div id="' + $dialogName + '"></div>').appendTo('body');
                }
                var $dialog = $('#' + $dialogName);
    
                $dialog.empty().append(data.html);
                $dialog.dialog({
                    cache: false,
                    resizable: true,
                    bgiframe: true,
                    autoOpen: false,
                    title: data.title,
                    width: 1200,
                    minWidth: 1200,
                    height: 600,
                    modal: false,
                    close: function () {
                        $dialog.empty().dialog('destroy').remove();
                    },
                    buttons: [ 
                        {text: plang.get('close_btn'), class: 'btn blue-hoki btn-sm', click: function () {
                            $dialog.dialog('close');
                        }}
                    ]
                }).dialogExtend({
                    "closable": true,
                    "maximizable": true,
                    "minimizable": true,
                    "collapsable": true,
                    "dblclick": "maximize",
                    "minimizeLocation": "left", 
                    "icons": {
                        "close": "ui-icon-circle-close",
                        "maximize": "ui-icon-extlink",
                        "minimize": "ui-icon-minus",
                        "collapse": "ui-icon-triangle-1-s",
                        "restore": "ui-icon-newwin"
                    }
                });
                $dialog.dialog('open');
                $dialog.dialogExtend('maximize');
            }
            
            Core.unblockUI();
        },
        error: function () {
            alert("Error");
            Core.unblockUI();
        }
    });
}