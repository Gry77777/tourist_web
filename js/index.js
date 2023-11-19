$(document).ready(function () {
  $(".menu ul li").click(function () {
    var url = $(this).attr("data-url");
    if (url) {
      $("iframe").attr("src", url);
    }
  });
});

// $(document).ready(function () {
//   function adjustIframeHeight() {
//     var iframe = $("#myIframe");
//     if (iframe.length) {
//       iframe.css(
//         "height",
//         iframe.contents().find("body")[0].scrollHeight + "px"
//       );
//     }
//   }

//   $(window).on("resize", adjustIframeHeight); // 如果需要在窗口大小变化时也自适应高度
//   // 在 iframe 内容加载完成后调整高度
//   $("#myIframe").on("load", adjustIframeHeight);
// });

// $(document).ready(function () {
//   function adjustIframeHeight() {
//     var iframe = $("#myIframe");
//     if (iframe.length) {
//       try {
//         iframe.css(
//           "height",
//           iframe.contents().find("body")[0].scrollHeight + "px"
//         );
//       } catch (e) {
//         console.error("Error adjusting iframe height:", e);
//       }
//     }
//   }

//   $(window).on("resize", adjustIframeHeight);
//   $("#myIframe").on("load", adjustIframeHeight);

//   // 调整 iframe 高度，在页面加载时和 iframe 内容变化时
//   $(window).on("load", function () {
//     adjustIframeHeight();
//   });
// });

$(document).ready(function () {
  // Define your base height value
  var baseHeight = 100; // You can change this value as needed
  function adjustIframeHeight() {
    var iframe = $("#myIframe");
    if (iframe.length) {
      try {
        var bodyHeight = iframe.contents().find("body")[0].scrollHeight;
        var totalHeight =
          bodyHeight +
          parseInt(iframe.css("paddingTop")) +
          parseInt(iframe.css("paddingBottom")) +
          baseHeight; // Add the base height here
        iframe.css("height", totalHeight + "px");
      } catch (e) {
        console.error("Error adjusting iframe height:", e);
      }
    }
  }


  // Attach the adjustIframeHeight function to the resize and load events
  $(window).on("resize", adjustIframeHeight);
  $("#myIframe").on("load", adjustIframeHeight);

  // Adjust iframe height on page load
  $(window).on("load", function () {
    adjustIframeHeight();
  });
});

$(document).ready(function () {
  // 发起AJAX请求以获取并更新用户信息
  $.ajax({
    type: "GET",
    url: "update_user_info.php",
    dataType: "json",
    success: function (response) {
      // 更新页面上的用户名和图片显示
      $("#userSection").html(
        '<a href="profile.php">' +
          response.username +
          "</a>" +
          '<img src="' +
          response.img +
          '" alt="User Image" style="width: 25px; height: 25px; border-radius: 15px;">' +
          '<form action="logout.php" method="post" style="display: inline;"><input type="submit" value="退出登录"></form>'
      );
    },
  });
});
