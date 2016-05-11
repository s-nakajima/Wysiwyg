/**
 * plugin.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2015 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

/*global tinymce:true */

tinymce.PluginManager.add('nc3_colorpicker', function(editor) {
  var cols, rows;

  rows = editor.settings.textcolor_rows || 5;
  cols = editor.settings.textcolor_cols || 8;

  function mapColors() {
    var i, colors = [], colorMap;

    colorMap = editor.settings.textcolor_map || [
      '000000', 'Black',
      '993300', 'Burnt orange',
      '333300', 'Dark olive',
      '003300', 'Dark green',
      '003366', 'Dark azure',
      '000080', 'Navy Blue',
      '333399', 'Indigo',
      '333333', 'Very dark gray',
      '800000', 'Maroon',
      'FF6600', 'Orange',
      '808000', 'Olive',
      '008000', 'Green',
      '008080', 'Teal',
      '0000FF', 'Blue',
      '666699', 'Grayish blue',
      '808080', 'Gray',
      'FF0000', 'Red',
      'FF9900', 'Amber',
      '99CC00', 'Yellow green',
      '339966', 'Sea green',
      '33CCCC', 'Turquoise',
      '3366FF', 'Royal blue',
      '800080', 'Purple',
      '999999', 'Medium gray',
      'FF00FF', 'Magenta',
      'FFCC00', 'Gold',
      'FFFF00', 'Yellow',
      '00FF00', 'Lime',
      '00FFFF', 'Aqua',
      '00CCFF', 'Sky blue',
      '993366', 'Red violet',
      'FFFFFF', 'White',
      'FF99CC', 'Pink',
      'FFCC99', 'Peach',
      'FFFF99', 'Light yellow',
      'CCFFCC', 'Pale green',
      'CCFFFF', 'Pale cyan',
      '99CCFF', 'Light sky blue',
      'CC99FF', 'Plum'
    ];

    for (i = 0; i < colorMap.length; i += 2) {
      colors.push({
        text: colorMap[i + 1],
        color: '#' + colorMap[i]
      });
    }

    return colors;
  }

  function renderColorPicker() {
    var ctrl = this,
        colors, color, html, last, x, y, i, id = ctrl._id,
        count = 0;

    function getColorCellHtml(color, title) {
      var isNoColor = color == 'transparent';

      return (
          '<td class="mce-grid-cell' +
          (isNoColor ? ' mce-colorbtn-trans' : '') + '">' +
          '<div id="' + id + '-' + (count++) + '"' +
          ' data-mce-color="' + (color ? color : '') + '"' +
          ' role="option"' +
          ' tabIndex="-1"' +
          ' style="' + (color ? 'background-color: ' + color : '') + '"' +
          ' title="' + tinymce.translate(title) + '">' +
          (isNoColor ? '&#215;' : '') +
          '</div>' +
          '</td>'
      );
    }

    colors = mapColors();
    colors.push({
      text: tinymce.translate('No color'),
      color: 'transparent'
    });

    html = '<table class="mce-grid mce-grid-border mce-colorbutton-grid" ' +
        'role="list" cellspacing="0"><tbody>';

    last = colors.length - 1;

    for (y = 0; y < rows; y++) {
      html += '<tr>';

      for (x = 0; x < cols; x++) {
        i = y * cols + x;

        if (i > last) {
          html += '<td></td>';
        } else {
          color = colors[i];
          html += getColorCellHtml(color.color, color.text);
        }
      }

      html += '</tr>';
    }

    // html += '<tr><td colspan=' + cols +
    //     ' style="align: right; vertical-align: middle;">' +
    //     'カラーコード ' +
    //     '<input type="text" ' +
    //     'id="mce-colorcode-' + ctrl.parent().settings.format + '" ' +
    //     'class="mce-textbox" style="width: 8em; height: 1.2em;" /></td></tr>';

    html += '</tbody></table>';

    return html;
  }

  function colorPickerCallback(callback, value) {
    var win = editor.windowManager.open({
      title: 'Color',
      buttons: [
      ],
      items: {
        type: 'container',
        layout: 'flex',
        direction: 'row',
        align: 'stretch',
        padding: 5,
        spacing: 10,
        items: [
          {
            type: 'panel',
            html: renderColorPicker,
            onclick: onPanelClick
          }
        ]
      }
    });

    function onPanelClick(e) {
      var buttonCtrl = this.parent(),
          value;

      value = e.target.getAttribute('data-mce-color');
      if (value) {
        if (value == 'transparent') {
          callback('');
        } else {
          callback(value);
        }
        win.close();
      }
    }
  }

  if (!editor.settings.color_picker_callback) {
    editor.settings.color_picker_callback = colorPickerCallback;
  }
});
