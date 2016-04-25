/**
 * Tex - plugin
 */
// add tinymce plugin
tinymce.PluginManager.add('tex', function(editor, url) {
  //　セレクタなど
  var vals = {
    iptTextArea: '#tex-text',
    tex_elm_class: 'tex-char'
  };

  // 値が更新された時の処理
  var srcChange = function(e) {

  };

  // プレビュー表示
  var preview = function() {
    // TODO tinymce処理版にできるかどうか
    var txt = $(vals.iptTextArea).val();
    $('#tex-preview').text('$$' + txt + '$$');
    MathJax.Hub.Queue(['Typeset', MathJax.Hub, 'tex-preview']);
    return false;
  };

  // domクリック時ダイアログ表示(TODO:使用するならば)
  var setWysiwygTextEvent = function() {
    $(editor.getDoc()).on('click', function(e) {
      console.debug('Editor was clicked: ' + e.target);
      if ($(e.target).hasClass('tex-char')) {
        var txt = $(e.target).text();
        var rep = txt.replace(/\$\$\s|\s\$\$/g, '');
        showDialog();
      }
    });
  };

  // フォーム部品
  var generalFormItems = function(val) {
    return [
      {
        type: 'label',
        text: 'TeX表記文字を入力してください',
        forId: 'tex-text'
      },
      {
        id: 'tex-text',
        type: 'textbox',
        flex: 1,
        name: 'tex',
        value: val,
        multiline: true,
        onchange: srcChange
      },
      {
        type: 'button',
        text: 'Preview',
        maxWidth: 100,
        align: 'center',
        onclick: preview
      },
      {
        type: 'panel',
        id: 'tex-preview',
        classes: 'reset',
        flex: 1,
        // TODO プレビューのスタイル(高さが変化しない。)
        style: 'margin: 0px; padding: 5px;' +
            ' background-color: #ddd; font-size: 14px;overflow-y: scroll;'
      }
    ];
  };

  // ダイアログ表示
  var showDialog = function(text) {
    var selectedNode = editor.selection.getNode(), val = '';
    var isTarget = selectedNode.tagName == 'SPAN' &&
        editor.dom.hasClass(selectedNode, vals.tex_elm_class) == true;
    if (isTarget) {
      val = selectedNode.innerText.replace(/\$\$\s|\s\$\$/g, '');
    }

    editor.windowManager.open({
      title: 'Tex',
      layout: 'flex',
      direction: 'column',
      align: 'stretch',
      padding: 10,
      spacing: 10,
      id: 'tex-dialog',
      body: generalFormItems(val),
      width: 400,
      height: 300,
      onsubmit: function(e) {
        var txt = e.data.tex;
        // 再編集の場合
        if (isTarget) {
          selectedNode.innerText = '$$' + txt + '$$';
        }
        // 新規挿入の場合
        else {
          var dom = editor.dom.createHTML(
              'span',
              {class: vals.tex_elm_class},
              '$$ ' + txt + ' $$'
              );
          var e = tinymce.activeEditor.execCommand(
              'mceInsertContent',
              false,
              dom
              );
        }
      }
    });
  };

  // コマンド登録
  editor.addCommand('mceTex', showDialog);

  // windowへのボタン登録
  editor.addButton('tex', {
    tooltip: 'Tex',
    id: 'tex-btn',
    stateSelector: '.' + vals.tex_elm_class,
    onclick: showDialog,
    image: editor.settings.nc3Configs.tex_icon
  });

  // setWysiwygTextEvent();
});
