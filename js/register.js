function previewImage(event) {
  var reader = new FileReader();
  reader.onload = function () {
    var output = document.getElementById("preview");
    output.src = reader.result;
    output.style.display = "block"; // 显示预览图像

    // 创建“取消”按钮
    var cancelBtn = document.createElement("button");
    cancelBtn.innerText = "×";
    cancelBtn.classList.add("cancel-btn");
    cancelBtn.onclick = function () {
      output.src = ""; // 清空预览图像
      output.style.display = "none"; // 隐藏预览图像
      this.remove(); // 移除取消按钮
    };
    output.parentNode.appendChild(cancelBtn); // 将取消按钮添加到预览图像的父元素中
  };

  if (event.target.files.length === 0) {
    var output = document.getElementById("preview");
    output.src = ""; // 清空预览图像
    output.style.display = "none"; // 隐藏预览图像
    var cancelBtn = document.querySelector(".cancel-btn");
    if (cancelBtn) {
      cancelBtn.remove(); // 如果存在取消按钮，则移除
    }
  } else {
    reader.readAsDataURL(event.target.files[0]);
  }
}

function validateForm() {
  var password = document.getElementById("password").value;
  var confirm_password = document.getElementById("confirm_password").value;
  if (password.length < 6) {
    alert("密码长度必须大于6");
    return false;
  } else if (password !== confirm_password) {
    alert("两次输入的密码不一致");
    return false;
  }
  return true;
}

function hashPassword() {
  var raw_password = document.getElementById("password").value;
  var hashed_password = CryptoJS.SHA256(raw_password).toString();
  document.getElementById("password_hash").value = hashed_password;
}

$(document).ready(function() {
    $('#username').on('input', function() {
        var username = $(this).val();

        // 发送Ajax请求之前，先清空提示文本
        $('#username-message').text('');

        // 如果用户名为空，则退出
        if (username === '') {
            return;
        }
        
        // 发送Ajax请求
        $.ajax({
            type: 'POST',
            url: 'check_username.php', // 假设检查用户名的接口为check_username.php
            data: { username: username },
            success: function(response) {
                if (response === 'exists') {
                    $('#username-message').text('用户名已存在，请选择其他用户名');
                } else {
                    $('#username-message').text('用户名可用');
                }
            },
            error: function() {
                console.error('发生错误');
            }
        });
    });
});

