/**
 * File - plugin
 */
// ad tinymce plugin
tinymce.PluginManager.add('file', function(editor, url) {
  //
  var srcChange = function(e) {

  };

  var onSubmit = function() {

  };

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
        var src = e.data.uploadfile;
        if (src) {
          // formオブジェクト作成
          var files = $('#uploadForm').find('input[type="file"]')[0].files[0];
          var formData = new FormData();
          formData.append('data[Wysiwyg][file]', files);
          formData.append('data[Block][key]',
              editor.settings.nc3Configs.blockKey);
          formData.append('data[Block][room_id]',
              editor.settings.nc3Configs.roomId);

          NC3_APP.uploadFile(editor.settings.nc3Configs.roomId, formData,
              function(res) {
                // onsuccess
                if (res.result) {
                  editor.selection.collapse(true);
                  editor.execCommand('mceInsertContent', false,
                      editor.dom.createHTML('a',
                          {
                            href: res.file.path,
                            target: '_brank'
                          },
                          res.file.original_name
                      )
                  );
                } // if
              },
              function(res) {
                // onerror
              },
              editor.settings.isDEBUG); // uploadfile()
        } // if(src)
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
    image: '/wysiwyg/img/title_icons/fileup.svg'
  });
});