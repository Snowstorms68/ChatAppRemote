const form = document.querySelector(".signup form"),
continueBtn = form.querySelector(".button input"),
errorText = form.querySelector(".error-text");

form.onsubmit = (e) => {
    e.preventDefault();
}

continueBtn.onclick = () => {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "php/signup.php", true);
    xhr.onload = () => {
      if(xhr.readyState === XMLHttpRequest.DONE){
          if(xhr.status === 200){
              let data = xhr.response;
              if(data === "success"){
                location.href="users.php";
              }else{
                errorText.style.display = "block";
                errorText.textContent = data;
              }
          }
      }
    }
    let formData = new FormData(form);
    xhr.send(formData);
}
document.addEventListener("DOMContentLoaded", function () {
  const actualBtn = document.getElementById('actual-btn');
  const fileChosen = document.getElementById('file-chosen');

  document.getElementById('custom-button').addEventListener('click', function () {
    actualBtn.click();
  });

  actualBtn.addEventListener('change', function () {
    fileChosen.textContent = this.files[0].name;
  });
});
