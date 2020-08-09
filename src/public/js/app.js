window.onload = function () {
    document.documentElement.style.setProperty('--my-hue', Math.floor(Math.random() * 360));

    const tabItems = document.querySelectorAll('.tab_buttons li a');
    const contents = document.querySelectorAll('.tab-content');

    tabItems.forEach(clickedItem => {
      clickedItem.addEventListener('click', e => {
        e.preventDefault();

        tabItems.forEach(item => {
          item.classList.remove('tab-active');
        });
        clickedItem.classList.add('tab-active');

        contents.forEach(content => {
          content.classList.remove('active-section');
        });
        document.getElementById(clickedItem.dataset.id).classList.add('active-section');
      });
    });

    //検索時のリクエストパラメータ設定
    const searchForm = document.querySelector("#search-form");
    if (searchForm !== null) {
      searchForm.addEventListener("submit", go, false);
    }
}

var serialize = function (form) {

  // Setup our serialized data
  var serialized = [];

  // Loop through each field in the form
  for (var i = 0; i < form.elements.length; i++) {

      var field = form.elements[i];

      // Don't serialize fields without a name, submits, buttons, file and reset inputs, and disabled fields
      if (!field.name || field.disabled || field.type === 'file' || field.type === 'reset' || field.type === 'submit' || field.type === 'button') continue;

      // If a multi-select, get all selections
      if (field.type === 'select-multiple') {
          for (var n = 0; n < field.options.length; n++) {
              if (!field.options[n].selected) continue;
              serialized.push(encodeURIComponent(field.name) + "=" + encodeURIComponent(field.options[n].value));
          }
      }

      // Convert field data to a query string
      else if ((field.type !== 'checkbox' && field.type !== 'radio') || field.checked) {
          serialized.push(encodeURIComponent(field.name) + "=" + encodeURIComponent(field.value));
      }
  }

  return serialized.join('&');

};
//serializeのVanilla JS版ここまで（引用ここまで）

//cleanQueryのVanilla JS版
function cleanQuery(query) {
  var arr = [], i, keyvalue = query.split("&");
  for (i = 0; i < keyvalue.length; i++) {
      if ( keyvalue[i].split("=")[1] ) { arr.push(keyvalue[i]); }
  }
  return arr.join("&");
}

//submitイベント発生時に行いたい処理。ここでいよいよserializeとcleanQueryを使う
function go(e) {
  e.preventDefault();
  var query = serialize(this);
  query = cleanQuery(query);
  location.href = this.action + "?" + query;
}

