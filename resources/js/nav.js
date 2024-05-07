
window.addEventListener('DOMContentLoaded', function () {
  const hamBurger = document.querySelector(".toggle-btn");
  const sidebarEls = document.querySelectorAll(".sidebar__item");
  const sidebarLinkEls = document.querySelectorAll(".sidebar__link");
  const sidebarLinkEls1 = document.querySelectorAll(".sidebar__link1");
  const registerActive = document.querySelector('[data-bs-target="#auth"]');
  const archiveActive = document.querySelector('[data-bs-target="#archived"]');
  const eyePasswordIcons =  document.querySelectorAll(".eye-password-icon")
  const password =  document.querySelector(".password")
  const eyeCPasswordIcons =  document.querySelectorAll(".eye-cpassword-icon")
  const cPassword =  document.querySelector(".confirm-password")
  const eyeOPasswordIcons =  document.querySelectorAll(".eye-old-password-icon")
  const oPassword =  document.querySelector(".old-password")

  hamBurger.addEventListener("click", function () {
    document.querySelector("#sidebar").classList.toggle("expand");
  });

  sidebarEls.forEach(sidebarEl => {
    sidebarEl.addEventListener('click', () => {
      document.querySelector('.active')?.classList.remove('active');
      sidebarEl.classList.add('active');
    });
    });

  sidebarLinkEls.forEach(sidebarLinkEl => {
    sidebarLinkEl.addEventListener('click', () => {
      document.querySelector('.active')?.classList.remove('active');
      registerActive.classList.add('active');
    });
  });

  sidebarLinkEls1.forEach(sidebarLinkEl => {
    sidebarLinkEl.addEventListener('click', () => {
      document.querySelector('.active')?.classList.remove('active');
      archiveActive.classList.add('active');
    });
  });

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