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


const DropDownButton = document.querySelector('#drop-down_page');
const DropDownMenu = document.querySelector('#drop-down_menu');
const DropDownArrow = document.querySelector('#drop-down_arrow');

DropDownButton.addEventListener('click', (e) => {

// e.stopPropagation();

  if (DropDownButton.classList.contains('hidden')) { // если есть класс хидден при наэжатиии он уберется и добавится коасс дроп даун то есть опен при повторном нажатии вернется в исходное положени зидден
    DropDownButton.classList.toggle('open');
  } else{
    DropDownMenu.classList.toggle('open');
    DropDownMenu.classList.toggle('hidden');
  }


if (DropDownArrow.classList.contains('rotate-back')){ // если есть класс ротате блэкл при нажатии клики он повернется ротатейт инчае при  2 нажатиии вернется в исъодное положение с помоью того де ротате

  DropDownArrow.classList.toggle('rotate');
}else{
  DropDownArrow.classList.toggle('rotate');
} 

  setTimeout(() => {
    DropDownMenu.classList.remove('open');
    DropDownMenu.classList.add('hidden');
    DropDownArrow.classList.remove('rotate');
  }, 1800);
// DropDownMenu.classList.toggle('drop-down_menu');

 // ?-если активен, то true, :-иначе false
});


