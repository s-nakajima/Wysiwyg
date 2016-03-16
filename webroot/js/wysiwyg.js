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

    nc3Configs: nc3Configs,
    titleIconSize: 18,

    setup: function(editor) {
      editor.addButton('books', {
        text: '書籍',
        tooltip: 'BookSearch',
        image: '/wysiwyg/img/title_icons/book.svg',
        onclick: function() {
          editor.windowManager.alert('書籍検索');
        }
      });
    },
    language: 'ja',
    language_url: '/wysiwyg/js/langs/ja.js',
    convert_urls: false
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
