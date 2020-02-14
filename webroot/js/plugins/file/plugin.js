/**
 * File - plugin
 */
// ad tinymce plugin
tinymce.PluginManager.add('file', function(editor, url) {

  // ダイアログ表示
  var showDialog = function() {
    editor.windowManager.open({
      title: 'File',
      align: 'stretch',
      padding: 10,
      spacing: 10,
      width: 400,
      height: 100,
      id: 'uploadForm',
      body: [
        {
          name: 'src',
          type: 'textbox',
          subtype: 'file',
          label: 'File',
          name: 'uploadfile',
          autofocus: true
        }
      ],
      onsubmit: function(e) {
        e.preventDefault();
        e.stopPropagation();
        var src = e.data.uploadfile;
        if (src) {
          // formオブジェクト作成
          var files = $('#uploadForm').find('input[type="file"]')[0].files[0];
          var formData = new FormData();
          formData.append('data[Wysiwyg][file]', files);
          formData.append('data[Block][key]', editor.settings.nc3Configs.blockKey);
          formData.append('data[Block][room_id]', editor.settings.nc3Configs.roomId);
          formData.append('data[Room][id]', editor.settings.nc3Configs.roomId);
          formData.append('data[_Token][fields]', editor.settings.nc3Configs.fileSecure);
          formData.append('data[_Token][unlocked]', '');
          if (editor.settings.nc3Configs.debug !== '0') {
            formData.append('data[_Token][debug]', editor.settings.nc3Configs.debug);
          }

          var loading = $('#loading');
          loading.removeClass('ng-hide');

          NC3_APP.uploadFile(editor.settings.nc3Configs.roomId, formData,
              function(res) {
                // onsuccess
                if (res.result) {
                  editor.selection.collapse(true);
                  editor.execCommand('mceInsertContent', false,
                      editor.dom.createHTML('a',
                          {
                            href: res.file.path,
                            target: '_blank'
                          },
                          res.file.original_name
                      )
                  );
                  // dialog close
                  top.tinymce.activeEditor.windowManager.close();
                } // if
                loading.addClass('ng-hide');
              },
              function(res) {
                // onerror
                alert(res.responseJSON.message);
                loading.addClass('ng-hide');
              },
              editor.settings.isDEBUG); // uploadfile()
        } // if(src)
        return false;
      } // onsubmit
    }); // open()
  }; // showDialog

  // コマンド登録
  editor.addCommand('mceFile', showDialog);

  // windowへのボタン登録
  editor.addButton('file', {
    tooltip: 'Attach file',
    id: 'file-btn',
    onclick: showDialog,
    image: editor.settings.nc3Configs.fileup_icon
  });
});
