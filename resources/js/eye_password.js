
window.addEventListener('DOMContentLoaded', function () {
  const eyePasswordIcons =  document.querySelectorAll(".eye-password-icon")
  const password =  document.querySelector(".password")
  const eyeCPasswordIcons =  document.querySelectorAll(".eye-cpassword-icon")
  const cPassword =  document.querySelector(".confirm-password")
  const eyeOPasswordIcons =  document.querySelectorAll(".eye-old-password-icon")
  const oPassword =  document.querySelector(".old-password")


  eyePasswordIcons.forEach(eyePasswordIcon => {
     eyePasswordIcon.addEventListener('click', () => {
      if(password.type == "password") {
        password.type = "text";
        eyePasswordIcon.classList.remove('bx-hide');
        eyePasswordIcon.classList.add('bx-show');
      }
      else {
        password.type = "password";
        eyePasswordIcon.classList.remove('bx-show');
        eyePasswordIcon.classList.add('bx-hide');
      }
     });
  });

  eyeCPasswordIcons.forEach(eyeCPasswordIcon => {
     eyeCPasswordIcon.addEventListener('click', () => {
      if(cPassword.type == "password") {
        cPassword.type = "text";
        eyeCPasswordIcon.classList.remove('bx-hide');
         eyeCPasswordIcon.classList.add('bx-show');
      }
      else {
        cPassword.type = "password";
         eyeCPasswordIcon.classList.remove('bx-show');
         eyeCPasswordIcon.classList.add('bx-hide');
      }
     });



  });

  eyeOPasswordIcons.forEach(eyeOPasswordIcon => {
     eyeOPasswordIcon.addEventListener('click', () => {
      if(oPassword.type == "password") {
        oPassword.type = "text";
        eyeOPasswordIcon.classList.remove('bx-hide');
         eyeOPasswordIcon.classList.add('bx-show');
      }
      else {
        oPassword.type = "password";
         eyeOPasswordIcon.classList.remove('bx-show');
         eyeOPasswordIcon.classList.add('bx-hide');
      }
     });
  });

}); 