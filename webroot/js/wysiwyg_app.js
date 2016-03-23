/**
 * nc3mce.app.js
 * 共通処理クラス
 */
var NC3_APP = new (function nc3WysiwygApp() {
  var self = this;
  /**
  * API URL設定
  */
  var __appURLs = (function() {
    return {
      uploadImage: function() {
        return '/wysiwyg/image/upload';
      },
      uploadFile: function() {
        return '/wysiwyg/file/upload';
      },
      searchBooks: function(q) {
        return 'https://www.googleapis.com/books/v1/volumes?q=' + q;
      },
      getBookDetail: function(id) {
        return 'https://www.googleapis.com/books/v1/volumes/' + id;
      }
    };
  })();
  /////////////////////////////////////////////////
  // Util
  /////////////////////////////////////////////////
  var JsonParse = function(data) {
    var obj = data;
    try {
      if (typeof data == 'string') {
        obj = JSON.parse(data);
      }
    } catch (e) {
      obj = {};
      // console.log('Error JSON.parse(data) : ' + e.message);
    }
    return obj;
  };
  var toJson = function(data) {
    var obj = data;
    try {
      if (typeof data == 'object' || typeof data == 'array') {
        obj = JSON.stringify(data);
      }
    } catch (e) {
      obj = {};
      // console.log('Error JSON.stringify(data) : ' + e.message);
    }
    return obj;
  };
  /////////////////////////////////////////////////
  // httpリクエスト
  /////////////////////////////////////////////////
  var __httpReq = function(method, url, formData, onsuccess, onerror, name) {
    $.ajax({
      type: method.toLowerCase(),
      url: url,
      data: formData,
      crossDomain: true,
      cache: false,
      processData: false, // Ajaxがdataを整形しない指定
      contentType: false  // contentTypeもfalseに指定

    }).done(function(data, success, xobj) {
      var obj = JsonParse(data);
      $.isFunction(onsuccess) && onsuccess(obj);
      $.Deferred().resolve();

    }).fail(function(data, textStatus, errorThrown) {
      $.isFunction(onerror) && onsuccess(onerror);
    });
  };
  /////////////////////////////////////////////////
  // API(formDataを使用)
  /////////////////////////////////////////////////
  /**
  * トークンの確認(TODO)
  */
  var __checkToken = function() {

  };
  /**
  * 画像のアップロード
  */
  self.uploadImage = function(formData, onsuccess, onerr, isDEBUG) {
    if (isDEBUG) {
      onsuccess();
      return false;
    }
    var url = __appURLs.uploadImage();
    __httpReq(
        'post',
        url,
        formData,
        onsuccess,
        onerror,
        'uploadImage'
    );
  };
  /**
  * ファイルのアップロード
  */
  self.uploadFile = function(formData, onsuccess, onerr, isDEBUG) {
    if (isDEBUG) {
      onsuccess(DUMMY_DATA.upload_file);
      return false;
    }
    var url = __appURLs.uploadFile();
    __httpReq(
        'post',
        url,
        formData,
        onsuccess,
        onerror,
        'uploadFile'
    );
  };
  /**
   * 書籍検索(Google books API)
   */
  self.searchBooks = function(params, onsuccess, onerr) {
    var url = __appURLs.searchBooks(params.keyword);
    __httpReq(
        'get',
        url,
        {},
        onsuccess,
        onerror,
        'searchBooks'
    );
  };

  /**
   * 書籍の詳細情報取得
   */
  self.getBookDetail = function(id, onsuccess, onerr) {
    var url = __appURLs.getBookDetail(id);
    __httpReq(
        'get',
        url,
        {},
        onsuccess,
        onerror,
        'getBookDetail'
    );
  };

})();


var DUMMY_DATA = {
  upload_file: {
    status_code: 200,
    result: true,
    file: {
      id: 0001,
      original_name: 'ファイル名',
      path: ''
    }
  },
  upload_file_false: {
    status_code: 401,
    result: false,
    message: 'エラー'
  }
};
