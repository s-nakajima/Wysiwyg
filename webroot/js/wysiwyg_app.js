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
      uploadImage: function(roomId) {
        return tinymce.editors[0].settings.nc3Configs.image_upload_path;
      },
      uploadFile: function(roomId) {
        return tinymce.editors[0].settings.nc3Configs.file_upload_path;
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
  // 画面の向き取得
  var currentO, defaultO, timer = false;
  self.checkOrientation = function() {
    if ('orientation' in window) {
      var o = (window.orientation % 180 == 0);
      if ((o && defaultO) || !(o || defaultO)) {
        currentO = 'portrait';
      }
      else {
        currentO = 'landscape';
      }
    }
    return currentO;
  };
  if ('orientation' in window) {
    var o1 = (window.innerWidth < window.innerHeight);
    var o2 = (window.orientation % 180 == 0);
    defaultO = (o1 && o2) || !(o1 || o2);
    beforeO = defaultO;
    self.checkOrientation();
  }

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
      // $.isFunction(onerror) && onsuccess(onerror);
      var obj = JsonParse(data);
      $.isFunction(onerror) && onerror(obj);
    });
  };
  /////////////////////////////////////////////////
  // API(formDataを使用)
  /////////////////////////////////////////////////
  /**
  * トークンの確認
  * success時にアップロードの実行
  */
  var url, formData;
  var __getCsrfToken = function(url, formData, onsuccess, onerr, name, isDEBUG) {
    var u = url;
    var fd = formData;
    var onss = onsuccess;
    var oner = onerr;
    var n = name;

    __httpReq(
        'get',
        tinymce.editors[0].settings.nc3Configs.csrfTokenPath,
        {},
        function(res) {
          // 取得した csrfToken をフォームデータとして作成する
          fd.append('data[_Token][key]', res.data._Token.key);

          // アップロードの実行
          __httpReq('post', u, fd, onss, oner, n);
        },
        function(res) {
        },
        'getCsrfToken'
    );
  };

  /**
  * 画像のアップロード
  */
  self.uploadImage = function(roomId, formData, onsuccess, onerr, isDEBUG) {
    if (isDEBUG) {
      onsuccess();
      return false;
    }

    var url = __appURLs.uploadImage(roomId);
    __getCsrfToken(url, formData, onsuccess, onerr, 'uploadImage', isDEBUG);

  };
  /**
  * ファイルのアップロード
  */
  self.uploadFile = function(roomId, formData, onsuccess, onerr, isDEBUG) {
    if (isDEBUG) {
      onsuccess(DUMMY_DATA.upload_file);
      return false;
    }

    var url = __appURLs.uploadFile(roomId);
    __getCsrfToken(url, formData, onsuccess, onerr, 'uploadFile', isDEBUG);
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
      id: '0001',
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
