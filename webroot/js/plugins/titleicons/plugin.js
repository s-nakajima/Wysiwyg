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

tinymce.PluginManager.add('titleicons', function(editor, url) {
  var titleicons = editor.settings.nc3Configs.title_icon_paths;

  function getHtml() {
    var titleiconsHtml;

    titleiconsHtml = '<table role="list" class="mce-grid">';

    tinymce.each(titleicons, function(row) {
      titleiconsHtml += '<tr>';

      tinymce.each(row, function(icon) {
        var emoticonUrl = editor.settings.nc3Configs.NC3_URL + icon.path;

        titleiconsHtml += '<td><a href="#" data-mce-url="' + emoticonUrl +
            '" data-mce-alt="' + icon.alt + '" tabindex="-1" ' +
            'role="option" aria-label="' + icon.alt + '">' +
            '<img src="' + emoticonUrl + '" style="width: 18px; height: 18px"' +
            ' role="presentation" /></a></td>';
      });

      titleiconsHtml += '</tr>';
    });

    titleiconsHtml += '</table>';

    return titleiconsHtml;
  }

  editor.addButton('titleicons', {
    type: 'panelbutton',
    panel: {
      role: 'application',
      autohide: true,
      html: getHtml,
      onclick: function(e) {
        var linkElm = editor.dom.getParent(e.target, 'a');

        if (linkElm) {
          editor.insertContent(
              '<img src="' + linkElm.getAttribute('data-mce-url') + '"' +
              ' alt="' + linkElm.getAttribute('data-mce-alt') + '"' +
              ' class="nc-title-icon" />'
          );

          this.hide();
        }
      }
    },
    tooltip: 'Emoticons',
    icon: 'emoticons'
  });
});
