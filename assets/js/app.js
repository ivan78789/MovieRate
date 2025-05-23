// // выпадющий список
// const dropDownBtn = document.querySelector('#drop-down_page');
// dropDownBtn.addEventListener('click', (e) => {
//   e.preventDefault();

//   if (document.querySelector('.drop-down')) return;

//   const dropDown = document.createElement('div');
//   dropDown.classList.add('drop-down');
//   dropDown.innerHTML = `
//     <div class="drop-down-content">
//       <a href="index.html">Licenses</a>
//       <a href="about.html">Contact Us</a>
//       <a href="contact.html">Our Team</a>
//     </div>
//   `;

//   const rect = dropDownBtn.getBoundingClientRect();
//   dropDown.style.position = 'absolute';
//   dropDown.style.top = `${rect.bottom + window.scrollY}px`;
//   dropDown.style.left = `${rect.left + window.scrollX}px`;
//   dropDown.style.zIndex = '1000';


//   const style = document.createElement('style');

//   document.head.appendChild(style);

//   document.body.appendChild(dropDown);
//   setTimeout(() => {
//     dropDown.remove();
//     style.remove();
//   }, 3000);
// });



// DropDownMenu.classList.toggle('drop-down_menu');

 // ?-если активен, то true, :-иначе false
});

// просмотр пароля

    const passwordInput = document.getElementById('password');
    const toggleBtn = document.getElementById('toggle-button');
    const eyeIcon = document.getElementById('eye-icon');
    let isVisible = false;

    if (toggleBtn) {
        toggleBtn.addEventListener('click', function () {
            isVisible = !isVisible;
            passwordInput.type = isVisible ? 'text' : 'password';
            eyeIcon.src = isVisible ? '/assets/img/svg/eye.svg' : '/assets/img/svg/closed-eye.svg';
            eyeIcon.alt = isVisible ? 'Скрыть пароль' : 'Показать пароль';
        });
    };




  // для фильмов рейтинги и комментарии
  const  Rating = document.getElementById('rating');
  const  Comment = document.getElementById('comment');

  

