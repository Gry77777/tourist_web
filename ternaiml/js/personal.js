function previewImage(event) {
  var input = event.target;
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    reader.onload = function (e) {
      $("#preview").attr("src", e.target.result);
    };
    reader.readAsDataURL(input.files[0]);
  }
}

// $(document).ready(function () {
//   function updatePersonalInfo(event) {
//     event.preventDefault(); // 阻止按钮默认行为
//     var formData = new FormData($("#personalForm")[0]);
//     formData.append("user_id", userId);
//     formData.set("user_id", userId);
//     $.ajax({
//       url: "update_personal.php",
//       type: "POST",
//       data: formData,
//       processData: false,
//       contentType: false,
//       success: function (response) {
//         alert("个人信息已更新");
//         // 可以根据需要更新页面上的其他信息
//       },
//       error: function () {
//         alert("发生错误，请重试");
//       },
//     });
//   }

//   $("#personalForm").submit(updatePersonalInfo); // 绑定表单提交事件
// });

// //判断修改用户的两次密码输入是否正确
// $(document).ready(function () {
//   $("#personalForm").submit(function (e) {
//     e.preventDefault(); // 阻止默认的提交行为
//     var password = $("#password").val();
//     var confirmPassword = $("#confirm_password").val();

//     if (password != confirmPassword) {
//       alert("两次密码输入不一致，请重新输入！");
//       return false; // 返回 false 来阻止表单提交
//     } else {
//       $(this).unbind("submit").submit(); // 如果验证通过，解除阻止并允许表单提交
//     }
//   });
// });

$(document).ready(function () {
  // 表单提交事件处理
  $("#personalForm").submit(function (event) {
    event.preventDefault(); // 阻止默认的提交行为
    var password = $("#password").val();
    var confirmPassword = $("#confirm_password").val();

    if (password !== confirmPassword) {
      // 检查密码是否一致
      alert("两次密码输入不一致，请重新输入！");
      return; // 返回，不继续向下执行
    }

    // 如果密码一致，则调用 updatePersonalInfo 函数来处理表单提交
    updatePersonalInfo(event);
  });

  // 更新个人信息的函数
  function updatePersonalInfo(event) {
    event.preventDefault(); // 阻止按钮默认行为
    var formData = new FormData($("#personalForm")[0]);
    formData.append("user_id", userId);
    formData.set("user_id", userId);
    $.ajax({
      url: "update_personal.php",
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        alert("个人信息已更新");
        location.reload();
        // 可以根据需要更新页面上的其他信息
      },
      error: function () {
        alert("发生错误，请重试");
      },
    });
  }
});

//判断用户名是否存在
