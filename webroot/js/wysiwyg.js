/**
 * @fileoverview Wysiwyg Javascript
 * @author nakajimashouhei@gmail.com (Shohei Nakajima)
 */

NetCommonsApp.requires.push('ui.tinymce');


/**
 * NetCommonsWysiwyg factory
 */
NetCommonsApp.factory('NetCommonsWysiwyg', function(nc3Configs) {

  /**
   * tinymce optins
   *
   * @type {{mode: string, menubar: string, plugins: string, toolbar: string}}
   */
  var options = {
    mode: 'exact',
    menubar: false,
    plugins: 'advlist textcolor colorpicker table hr titleicons charmap ' +
        'link media nc3Image code nc3Preview searchreplace paste tex file',
    toolbar: [
              'fontselect fontsizeselect formatselect ' +
              '| bold italic underline strikethrough ' +
              '| subscript superscript | forecolor backcolor ' +
              '| removeformat' +
              '| undo redo | alignleft aligncenter alignright ' +
              '| bullist numlist | outdent indent blockquote ' +
              '| table | hr | titleicons | tex | link unlink' +
              '| media books nc3Image file | pastetext code nc3Preview'
    ],
    paste_as_text: true,
    convert_urls: false,

    nc3Configs: nc3Configs,

    // タイトルアイコンのサイズ指定
    titleIconSize: 18,

    setup: function(editor) {
      editor.addButton('books', {
        tooltip: 'BookSearch',
        image: '/wysiwyg/img/title_icons/book.svg',
        onclick: function() {
          editor.windowManager.alert('書籍検索');
        }
      });
    },

    // 言語設定
    language: nc3Configs.lang,
    language_url: '/wysiwyg/js/langs/' + nc3Configs.lang + '.js'
  };

  /**
   * variables
   *
   * @type {Object.<string>}
   */
  var variables = {
    options: options
  };

  /**
   * functions
   *
   * @type {Object.<function>}
   */
  var functions = {
    /**
     * new method
     */
    new: function() {
      return angular.extend(variables, functions);
    }
  };

  return functions.new();
});
